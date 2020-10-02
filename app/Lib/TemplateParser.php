<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Lib;

/**
 * Description of TemplateParser
 *
 * @author pablo
 */
class TemplateParser {

    public static function parseTemplate($text, $vars) {
        $parsedText = $text;
        $template_vars = [];
        preg_match_all('#\[[A-Za-z0-9\.]+\]#', $text, $template_vars);
        if ($template_vars[0]) {
            $template_vars = $template_vars[0];
        }
        foreach ($template_vars as $v) {
            $var = preg_replace('#\[([A-Za-z0-9\.]+)\]#', '$1', $v);           
            $parsedText = str_replace($v, Utils::getByKey($var, $vars), $parsedText);
        }
        return $parsedText;
    }

}
