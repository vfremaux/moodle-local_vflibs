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
 *
 */
defined('MOODLE_INTERNAL') || die();

/**
 *
 *
 */
function local_vflibs_require_jqplot_libs() {
    global $CFG, $PAGE;

    static $jqplotloaded = false;

    if ($jqplotloaded) return;
    $PAGE->requires->jquery_plugin('jqplot', 'local_vflibs');
    $jqplotloaded = true;
}

/**
 * prints any JQplot graph type given a php descriptor and dataset
 *
 */
function local_vflibs_jqplot_print_graph($htmlid, $graph, &$data, $width, $height, $addstyle = '', $return = false, $ticks = null) {
    global $plotid;
    static $instance = 0;

    $htmlid = $htmlid.'_'.$instance;
    $instance++;

    $str = "<center><div id=\"$htmlid\" style=\"{$addstyle} width:{$width}px; height:{$height}px;\"></div></center>";
    $str .= "<script type=\"text/javascript\">\n";

    if (!is_null($ticks)) {
        $ticksvalues = implode("','", $ticks);
        $str .= "var ticks = ['$ticksvalues']; \n";
    }

    $varsetlist = json_encode($data);
    // Fixing data to arrays.
    $varsetlist = str_replace('{', '[', $varsetlist);
    $varsetlist = str_replace('}', ']', $varsetlist);

    $varsetlist = preg_replace('/"(\d+)\"/', "$1", $varsetlist);
    $jsongraph = json_encode($graph);
    $jsongraph = preg_replace('/"\$\$\.(.*?)\"/', "$1", $jsongraph);
    $jsongraph = preg_replace('/"(\$\.jqplot.*?)\"/', "$1", $jsongraph);

    $str .= "
    $.jqplot.config.enablePlugins = true;

    plot{$plotid} = $.jqplot(
        '{$htmlid}',
        $varsetlist,
        {$jsongraph}
    );
     ";
    $str .= "</script>";

     $plotid++;

     if ($return) {
        return $str;
    }
     echo $str;
}
