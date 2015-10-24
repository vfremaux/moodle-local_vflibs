<?php

/**
 *
 *
 */
function local_vflibs_require_jqplot_libs() {
    global $CFG, $PAGE;

    static $jqplotloaded = false;

    if ($jqplotloaded) return;
    /*
    $PAGE->requires->js('/local/vflibs/jqplot/jquery.jqplot.js', true);
    $PAGE->requires->js('/local/vflibs/jqplot/excanvas.js', true);
    $PAGE->requires->js('/local/vflibs/jqplot/plugins/jqplot.dateAxisRenderer.js', true);
    $PAGE->requires->js('/local/vflibs/jqplot/plugins/jqplot.barRenderer.min.js', true);
    $PAGE->requires->js('/local/vflibs/jqplot/plugins/jqplot.highlighter.min.js', true);
    $PAGE->requires->js('/local/vflibs/jqplot/plugins/jqplot.canvasOverlay.min.js', true);
    $PAGE->requires->js('/local/vflibs/jqplot/plugins/jqplot.cursor.min.js', true);
    $PAGE->requires->js('/local/vflibs/jqplot/plugins/jqplot.categoryAxisRenderer.min.js', true);
    $PAGE->requires->js('/local/vflibs/jqplot/plugins/jqplot.pointLabels.min.js', true);
    $PAGE->requires->js('/local/vflibs/jqplot/plugins/jqplot.logAxisRenderer.min.js', true);
    $PAGE->requires->js('/local/vflibs/jqplot/plugins/jqplot.canvasTextRenderer.min.js', true);
    $PAGE->requires->js('/local/vflibs/jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js', true);
    $PAGE->requires->js('/local/vflibs/jqplot/plugins/jqplot.canvasAxisLabelRenderer.min.js', true);
    $PAGE->requires->js('/local/vflibs/jqplot/plugins/jqplot.enhancedLegendRenderer.min.js', true);
    $PAGE->requires->js('/local/vflibs/jqplot/plugins/jqplot.pieRenderer.min.js', true);
    $PAGE->requires->js('/local/vflibs/jqplot/plugins/jqplot.donutRenderer.min.js', true);
    */
    $PAGE->requires->jquery_plugin('jqplot', 'local_vflibs');
    $jqplotloaded = true;
}

/**
* prints any JQplot graph type given a php descriptor and dataset
*
*/
function local_vflibs_jqplot_print_graph($htmlid, $graph, &$data, $width, $height, $addstyle = '', $return = false, $ticks = null) {
    global $PLOTID;
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
    $varsetlist = preg_replace('/"(\d+)\"/', "$1", $varsetlist);
    $jsongraph = json_encode($graph);
    $jsongraph = preg_replace('/"\$\$\.(.*?)\"/', "$1", $jsongraph);
    $jsongraph = preg_replace('/"(\$\.jqplot.*?)\"/', "$1", $jsongraph);

    $str .= "
    $.jqplot.config.enablePlugins = true;

    plot{$PLOTID} = $.jqplot(
        '{$htmlid}',
        $varsetlist,
        {$jsongraph}
    );
     ";
    $str .= "</script>";

     $PLOTID++;

     if ($return) return $str;
     echo $str;
}
