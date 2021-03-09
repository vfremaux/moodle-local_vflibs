/**
 * JavaScript for the course selectors.
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package courseselector
 */
// jshint unused:false, undef:false

// Define the core_course namespace if it has not already been defined.
M.core_course = M.core_course || {};

// Define a course selectors array for against the cure_course namespace.
M.core_course.course_selectors = [];

/**
 * Retrieves an instantiated course selector or null if there isn't one by the requested name
 * @param {string} name The name of the selector to retrieve
 * @return bool
 */
M.core_course.get_course_selector = function (name) {
    return this.course_selectors[name] || null;
};

/**
 * Initialise a new course selector.
 *
 * @param {YUI} Y The YUI3 instance
 * @param {string} name the control name/id.
 * @param {string} hash the hash that identifies this selector in the course's session.
 * @param {array} extrafields extra fields we are displaying for each course in addition to fullname.
 * @param {string} lastsearch The last search that took place
 */
M.core_course.init_course_selector = function (Y, name, hash, extrafields, lastsearch) {
    // Creates a new course_selector object
    var course_selector = {

        /** This id/name used for this control in the HTML. */
        name : name,

        /** Array of fields to display for each course, in addition to fullname. */
        extrafields: extrafields,

        /** Number of seconds to delay before submitting a query request */
        querydelay : 0.5,

        /** The input element that contains the search term. */
        searchfield : Y.one('#' + name + '_searchtext'),

        /** The clear button. */
        clearbutton : null,

        /** The select element that contains the list of courses. */
        listbox : Y.one('#' + name),

        /** Used to hold the timeout id of the timeout that waits before doing a search. */
        timeoutid : null,

        /** Stores any in-progress remote requests. */
        iotransactions : {},

        /** The last string that we searched for, so we can avoid unnecessary repeat searches. */
        lastsearch : lastsearch,

        /** Whether any options where selected last time we checked. Used by
         *  handle_selection_change to track when this status changes. */
        selectionempty : true,

        /**
         * Initialises the course selector object
         * @constructor
         */
        init : function() {
            // Hide the search button and replace it with a label.
            var searchbutton = Y.one('#' + this.name + '_searchbutton');
            var label = Y.Node.create('<label for="' + this.name + '_searchtext">' + searchbutton.get('value') + '</label>');
            this.searchfield.insert(label, this.searchfield);
            searchbutton.remove();

            // Hook up the event handler for when the search text changes.
            this.searchfield.on('keyup', this.handle_keyup, this);

            // Hook up the event handler for when the selection changes.
            this.listbox.on('keyup', this.handle_selection_change, this);
            this.listbox.on('click', this.handle_selection_change, this);
            this.listbox.on('change', this.handle_selection_change, this);

            // And when the search any substring preference changes. Do an immediate re-search.
            Y.one('#courseselector_searchanywhereid').on('click', this.handle_searchanywhere_change, this);

            // Define our custom event.
            this.selectionempty = this.is_selection_empty();

            // Replace the Clear submit button with a clone that is not a submit button.
            var clearbtn = Y.one('#' + this.name + '_clearbutton');
            this.clearbutton = Y.Node.create('<input type="button" value="' + clearbtn.get('value') + '" />');
            clearbtn.replace(Y.Node.getDOMNode(this.clearbutton));
            this.clearbutton.set('id', this.name + "_clearbutton");
            this.clearbutton.on('click', this.handle_clear, this);
            this.clearbutton.set('disabled', (this.get_search_text() === ''));

            this.send_query(false);
        },

        /**
         * Key up hander for the search text box.
         * @param {Y.Event} e the keyup event.
         */
        handle_keyup : function(e) {
            // Trigger an ajax search after a delay.
            this.cancel_timeout();
            this.timeoutid = Y.later(this.querydelay * 1000, e, function(obj){obj.send_query(false);}, this);

            // Enable or diable the clear button.
            this.clearbutton.set('disabled', (this.get_search_text() === ''));

            // If enter was pressed, prevent a form submission from happening.
            if (e.keyCode === 13) {
                e.halt();
            }
        },

        /**
         * Handles when the selection has changed. If the selection has changed from
         * empty to not-empty, or vice versa, then fire the event handlers.
         */
        handle_selection_change : function() {
            var isselectionempty = this.is_selection_empty();
            if (isselectionempty !== this.selectionempty) {
                this.fire('course_selector:selectionchanged', isselectionempty);
            }
            this.selectionempty = isselectionempty;
        },

        /**
         * Trigger a re-search when the 'search any substring' option is changed.
         */
        handle_searchanywhere_change : function() {
            if (this.lastsearch !== '' && this.get_search_text() !== '') {
                this.send_query(true);
            }
        },

        /**
         * Click handler for the clear button..
         */
        handle_clear : function() {
            this.searchfield.set('value', '');
            this.clearbutton.set('disabled',true);
            this.send_query(false);
        },

        /**
         * Fires off the ajax search request.
         */
        send_query : function(forceresearch) {
            // Cancel any pending timeout.
            this.cancel_timeout();

            var value = this.get_search_text();
            this.searchfield.set('class', '');
            if (this.lastsearch == value && !forceresearch) {
                return;
            }

            // Try to cancel existing transactions.
            Y.Object.each(this.iotransactions, function(trans) {
                trans.abort();
            });

            var params = 'selectorid=' + hash + '&sesskey=' + M.cfg.sesskey + '&search=' + value;
            params += '&courseselector_searchanywhere=' + this.get_option('searchanywhere');

            var iotrans = Y.io(M.cfg.wwwroot + '/local/vflibs/classes/course/selector/search.php', {
                method: 'POST',
                data: params,
                on: {
                    complete: this.handle_response
                },
                context:this
            });
            this.iotransactions[iotrans.id] = iotrans;

            this.lastsearch = value;
            this.listbox.setStyle('background', 'url(' + M.util.image_url('i/loading', 'moodle') + ') no-repeat center center');
        },

        /**
         * Handle what happens when we get some data back from the search.
         * @param {int} requestid not used.
         * @param {object} response the list of courses that was returned.
         */
        handle_response : function(requestid, response) {
            try {
                delete this.iotransactions[requestid];
                if (!Y.Object.isEmpty(this.iotransactions)) {
                    // More searches pending. Wait until they are all done.
                    return;
                }
                this.listbox.setStyle('background','');
                var data = Y.JSON.parse(response.responseText);
                if (data.error) {
                    this.searchfield.addClass('error');
                    return new M.core.ajaxException(data);
                }
                this.output_options(data);
            } catch (e) {
                this.listbox.setStyle('background','');
                this.searchfield.addClass('error');
                return new M.core.exception(e);
            }
        },

        /**
         * This method should do the same sort of thing as the PHP method
         * course_selector_base::output_options.
         * @param {object} data the list of courses to populate the list box with.
         */
        output_options : function(data) {
            // Clear out the existing options, keeping any ones that are already selected.
            var selectedcourses = {};
            this.listbox.all('optgroup').each(function(optgroup){
                optgroup.all('option').each(function(option){
                    if (option.get('selected')) {
                        selectedcourses[option.get('value')] = {
                            id : option.get('value'),
                            name : option.get('innerText') || option.get('textContent'),
                            disabled: option.get('disabled')
                        };
                    }
                    option.remove();
                }, this);
                optgroup.remove();
            }, this);

            // Output each optgroup.
            var count = 0;
            for (var key in data.results) {
                var groupdata = data.results[key];
                this.output_group(groupdata.name, groupdata.courses, selectedcourses, true);
                count ++;
            }
            if (!count) {
                if (this.lastsearch !== '') {
                    searchstr = this.insert_search_into_str(M.str.moodle.nomatchingcourses, this.lastsearch);
                } else {
                    searchstr = M.str.moodle.none;
                }
                this.output_group(searchstr, {}, selectedcourses, true);
            }

            // If there were previously selected courses who do not match the search, show them too.
            if (this.get_option('preserveselected') && selectedcourses) {
                str = this.insert_search_into_str(M.str.moodle.previouslyselectedcourses, this.lastsearch);
                this.output_group(str, selectedcourses, true, false);
            }
            this.handle_selection_change();
        },

        /**
         * This method should do the same sort of thing as the PHP method
         * course_selector_base::output_optgroup.
         *
         * @param {string} groupname the label for this optgroup.v
         * @param {object} courses the courses to put in this optgroup.
         * @param {boolean|object} selectedcourses if true, select the courses in this group.
         * @param {boolean} processsingle
         */
        output_group : function(groupname, courses, selectedcourses, processsingle) {
            var optgroup = Y.Node.create('<optgroup></optgroup>');
            var count = 0;
            var option;
            for (var key in courses) {
                var course = courses[key];
                option = Y.Node.create('<option value="' + course.id + '">' + course.name + '</option>');
                if (course.disabled) {
                    option.set('disabled', true);
                } else if (selectedcourses === true || selectedcourses[course.id]) {
                    option.set('selected', true);
                    delete selectedcourses[course.id];
                } else {
                    option.set('selected', false);
                }
                optgroup.append(option);
                if (course.infobelow) {
                    extraoption = Y.Node.create('<option disabled="disabled" class="courseselector-infobelow"/>');
                    extraoption.appendChild(document.createTextNode(course.infobelow));
                    optgroup.append(extraoption);
                }
                count ++;
            }

            if (count > 0) {
                optgroup.set('label', groupname + ' (' + count + ')');
                if (processsingle && count === 1 && this.get_option('autoselectunique') && option.get('disabled') === false) {
                    option.set('selected', true);
                }
            } else {
                optgroup.set('label', groupname);
                optgroup.append(Y.Node.create('<option disabled="disabled">\u00A0</option>'));
            }
            this.listbox.append(optgroup);
        },

        /**
         * Replace
         * @param {string} str
         * @param {string} search The search term
         * @return string
         */
        insert_search_into_str : function(str, search) {
            return str.replace("%%SEARCHTERM%%", search);
        },

        /**
         * Gets the search text
         * @return String the value to search for, with leading and trailing whitespace trimmed.
         */
        get_search_text : function() {
            return this.searchfield.get('value').toString().replace(/^ +| +$/, '');
        },

        /**
         * Returns true if the selection is empty (nothing is selected)
         * @return Boolean check all the options and return whether any are selected.
         */
        is_selection_empty : function() {
            var selection = false;
            this.listbox.all('option').each(function(){
                if (this.get('selected')) {
                    selection = true;
                }
            });
            return !(selection);
        },

        /**
         * Cancel the search delay timeout, if there is one.
         */
        cancel_timeout : function() {
            if (this.timeoutid) {
                clearTimeout(this.timeoutid);
                this.timeoutid = null;
            }
        },

        /**
         * @param {string} name The name of the option to retrieve
         * @return the value of one of the option checkboxes.
         */
        get_option : function(name) {
            var checkbox = Y.one('#courseselector_' + name + 'id');
            if (checkbox) {
                return (checkbox.get('checked'));
            } else {
                return false;
            }
        }
    };

    /*
     * Augment the course selector with the EventTarget class so that we can use
     * custom events
     */
    Y.augment(course_selector, Y.EventTarget, null, null, {});
    // Initialise the course selector.
    course_selector.init();
    // Store the course selector so that it can be retrieved.
    this.course_selectors[name] = course_selector;
    // Return the course selector.
    return course_selector;
};

/**
 * Initialise a class that updates the course's preferences when they change one of
 * the options checkboxes.
 * @constructor
 * @param {YUI} Y
 * @return Tracker object
 */
M.core_course.init_course_selector_options_tracker = function(Y) {
    // Create a course selector options tracker.
    var course_selector_options_tracker = {
        /**
         * Initlises the option tracker and gets everything going.
         * @constructor
         */
        init : function() {
            var settings = [
                'courseselector_preserveselected',
                'courseselector_autoselectunique',
                'courseselector_searchanywhere'
            ];
            for (var s in settings) {
                var setting = settings[s];
                Y.one('#' + setting + 'id').on('click', this.set_course_preference, this, setting);
            }
        },

        /**
         * Sets a course preference for the options tracker
         * @param {Y.Event|null} e
         * @param {string} name The name of the preference to set
         */
        set_course_preference : function(e, name) {
            M.util.set_course_preference(name, Y.one('#' + name + 'id').get('checked'));
        }
    };
    // Initialise the options tracker.
    course_selector_options_tracker.init();
    // Return it just incase it is ever wanted.
    return course_selector_options_tracker;
};
