<?php


class local_vflibs_renderer extends plugin_renderer_base {

    // JQWidget wrappers
    /*
     * Properties : max, width, height
     */
    function jqw_bargauge_simple($name, $data, $properties = null, $labels = array()) {

        $str = '';

        if (empty($properties['max'])) $properties['max'] = 100;
        if (empty($properties['width'])) $properties['width'] = 500;
        if (empty($properties['height'])) $properties['height'] = 500;
        if (empty($properties['cropwidth'])) $properties['cropwidth'] = 300;
        if (empty($properties['cropheight'])) $properties['cropheight'] = 300;
        if (!empty($properties['crop'])) $properties['cropheight'] = $properties['cropwidth'] = $properties['crop'];
        if (empty($properties['animationduration'])) $properties['animationduration'] = 500;

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

    function jqw_progress_bar($name, $value, $properties = array()) {

        if (empty($properties['animation'])) $properties['animation'] = 0;
        if (empty($properties['width'])) $properties['width'] = 150;
        if (empty($properties['height'])) $properties['height'] = 24;
        if (empty($properties['template'])) $properties['template'] = 'primary';

        $str = '';
        $str .= '<script type="text/javascript">';
        $str .= '    $(document).ready(function ()';
        $str .= '    {';
        $str .= '       $("#'.$name.'").jqxProgressBar({ width: '.$properties['width'].', height: '.$properties['height'].', value: '.$value.', template: \''.$properties['template'].'\', animationDuration: '.$properties['animation'].'});';
        $str .= '    });';
        $str .= '</script>';
        $str .= '<div style="margin-top: 10px; overflow: hidden;" id="'.$name.'"></div>';

        return $str;
    }
}