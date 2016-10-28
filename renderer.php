<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package local_vflibs
 * @author valery.fremaux@gmail.com
 * @category local
 */

class local_vflibs_renderer extends plugin_renderer_base {

    // JQWidget wrappers
    /*
     * Properties : max, width, height
     */
    public function jqw_bargauge_simple($name, $data, $properties = null, $labels = array()) {

        $str = '';

        if (empty($properties['max'])) {
            $properties['max'] = 100;
        }
        if (empty($properties['width'])) {
            $properties['width'] = 500;
        }
        if (empty($properties['height'])) {
            $properties['height'] = 500;
        }
        if (empty($properties['cropwidth'])) {
            $properties['cropwidth'] = 300;
        }
        if (empty($properties['cropheight'])) {
            $properties['cropheight'] = 300;
        }
        if (!empty($properties['crop'])) {
            $properties['cropheight'] = 300;
            $properties['cropwidth'] = $properties['crop'];
        }
        if (empty($properties['animationduration'])) {
            $properties['animationduration'] = 500;
        }

        $str .= '';

        $str .= '<script type="text/javascript">';
        $str .= '    $(document).ready(function ()';
        $str .= '    {';
        $str .= '        $(\'#'.$name.'\').jqxBarGauge({colorScheme: "scheme02", width: '.$properties['width'].', height:' .$properties['height'].',';
        if (array_key_exists('title', $properties)) {
            $str .= '            title: { text: \''.$properties['title'].'\', ';
        }
        if (array_key_exists('subtitle', $properties)) {
            $str .= '            subtitle: \''.$properties['subtitle'].'\') },';
        }
        $str .= '            values: ['.implode(', ', $data).'], max: '.$properties['max'].', tooltip: {';
        $str .= '                visible: true, formatFunction: function (value)';
        $str .= '                {';
        $str .= '                    var realVal = parseInt(value);';
        $str .= '                    return (realVal);';
        $str .= '                },';
        $str .= '            },';
        $str .= '            animationDuration: '.$properties['animationduration'];
        $str .= '        });';
        $str .= '    });';
        $str .= '</script>';

        $w = $properties['cropwidth'];
        $h = $properties['cropheight'];
        $l = round(($properties['cropwidth'] - $properties['width']) / 2);
        $t = round(($properties['cropheight'] - $properties['height']) / 2);
        $str .= '<div class="jqw-gauge-container" style="width:'.$w.'px;height:'.$h.'px;">';
        $str .= '<div id="'.$name.'" style="overflow:hidden;position:relative;top:'.$t.'px;left:'.$l.'px"></div>';
        $str .= '</div>';

        return $str;
    }

    public function jqw_progress_bar($name, $value, $properties = array()) {

        if (empty($properties['animation'])) {
            $properties['animation'] = 0;
        }
        if (empty($properties['width'])) {
            $properties['width'] = 150;
        }
        if (empty($properties['height'])) {
            $properties['height'] = 24;
        }
        if (empty($properties['template'])) {
            $properties['template'] = 'primary';
        }

        $str = '';
        $str .= '<script type="text/javascript">';
        $str .= '    $(document).ready(function ()';
        $str .= '    {';
        $str .= '       $("#'.$name.'").jqxProgressBar({';
        $str .= '           width: '.$properties['width'].',';
        $str .= '           height: '.$properties['height'].',';
        $str .= '           value: '.$value.',';
        $str .= '           template: \''.$properties['template'].'\',';
        $str .= '           animationDuration: '.$properties['animation'].'});';
        $str .= '    });';
        $str .= '</script>';
        $str .= '<div style="margin-top: 10px; overflow: hidden;" id="'.$name.'"></div>';

        return $str;
    }

    /**
     * @param string $name
     * @param array $properties array with ('width', 'height', 'desc', 'barsize', 'tooltip') keys
     * @param array $ranges an array of range objects having ('start', 'end', 'color', 'opacity') keys
     * @param object $pointer an object with ('value', 'label', 'size', 'color') keys
     * @param object $target an object with ('value', 'label', 'size', 'color') keys
     * @param object $ticks an object with ('position', 'interval', 'size') keys
     */
    public function jqw_bulletchart($name, $properties, $ranges, $pointer, $target, $ticks = null) {

        if (is_null($ticks)) {
            $ticks = new Stdclass;
            $ticks->position = 'both';
            $ticks->interval = 50;
            $ticks->size = 10;
        }

        if (!isset($properties['barsize'])) {
            $properties['barsize'] = 20;
        }

        if (!isset($properties['bgcolor'])) {
            $properties['bgcolor'] = '#e0e0e0';
        }

        if (!isset($properties['bgopacity'])) {
            $properties['bgopacity'] = 1;
        }

        if (empty($ranges)) {
            $ranges = array();
            $defaultrange = (object) array('start' => 0, 'end' => 100, 'color' => $properties['bgcolor'], 'opacity' => $properties['bgopacity']);
            $ranges[] = $defaultrange;
        }

        if (empty($pointer)) {
            if (!isset($pointer->size)) {
                $pointer->size = 80;
            }
            if (!isset($pointer->color)) {
                $pointer->color = '#000000';
            }
        }

        if (empty($target)) {
            if (!isset($target->size)) {
                $target->size = 80;
            }
            if (!isset($target->color)) {
                $target->color = '#000000';
            }
        }

        if (!array_key_exists('tooltip', $properties)) {
            $properties['tooltip'] = 'true';
        }

        $str = '';

        $str .= '<div id="jqxBulletChart'.$properties['id'].'" class="jqwidgets-bulletchart"></div>'."\n";
        $str .= '<script type="text/javascript">';
        $str .= '    $(document).ready(function () {'."\n";
        $str .= '        $("#jqxBulletChart'.$properties['id'].'").jqxBulletChart({'."\n";
        $str .= '            width: '.$properties['width'].', '."\n";
        $str .= '            height: '.$properties['height'].', '."\n";
        $str .= '            barSize: "'.$properties['barsize'].'%", '."\n";
        $str .= '            title: "'.$name.'",'."\n";
        if (!empty($properties['desc'])) {
            $str .= '            description: "'.$properties['desc'].'",'."\n";
        } else {
            $str .= '            description: "",'."\n";
        }

        if (!empty($ranges)) {
            $str .= '            ranges: [';
            foreach ($ranges as $r) {
                if (empty($r->opacity)) {
                    $r->opacity = '1';
                }
                $rangearr[] = '    { startValue: '.(0 + $r->start).', endValue: '.(0 + $r->end).', color: "'.$r->color.'", opacity: '.$r->opacity.'} '."\n";
            }
            $str .= implode(',', $rangearr);
            $str .= '    ],'."\n";
        }

        if (!empty($pointer)) {
            $str .= 'pointer: { value: '.(0 + $pointer->value).', label: "'.$pointer->label.'", size: "'.$pointer->size.'%", color: "'.$pointer->color.'" },'."\n";
        }
        if (!empty($target)) {
            $str .= 'target: { value: '.(0 + $target->value).', label: "'.$target->label.'", size: '.$target->size.', color: "'.$target->color.'" },'."\n";
        }

        $str .= 'ticks: { position: "'.$ticks->position.'", interval: '.$ticks->interval.', size: '.$ticks->size.' },'."\n";
        $str .= 'labelsFormat: "'.$properties['ticklabelformat'].'",'."\n";
        $str .= 'showTooltip: '.$properties['tooltip']."\n";
        $str .= '    });'."\n";
        $str .= '});'."\n";
        $str .= '</script>'."\n";

        return $str;
    }
}