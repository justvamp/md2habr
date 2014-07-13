<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Allows to convert an article in markdown syntax right to correct
 * habrahabr article that is ready to publish.
 */
class MarkdownToHabrahabrConverter
{
    public function convert($txt)
    {
        // block /* TODO: refactor */
        $txt = preg_replace('/[\n\r]*```([\w]*)(.+?)```[\n\r]*/s', "\n\n<source lang=\"$1\">$2</source>\n", $txt); // sources
        
        // complex /* TODO: refactor */
        $txt = preg_replace('/\!\[([^\n\r]+?)\][ \t]*\((.+?)\)/s', '<img src="$2" alt="$1"></img>', $txt); // images
        $txt = preg_replace('/\[([^\n\r]+?)\][ \t]*\((http.+?)\)/s', '<a href="$2">$1</a>', $txt); // links
        
        // string
        $txt = preg_replace('/^\s*#{4}[ \t]+(.+?)\s*$/ms', "<h6>$1</h6>", $txt); // h6
        $txt = preg_replace('/^\s*#{3}[ \t]+(.+?)\s*$/ms', "<h5>$1</h5>", $txt); // h5
        $txt = preg_replace('/^\s*#{2}[ \t]+(.+?)\s*$/ms', "<h4>$1</h4>", $txt); // h4

        // inline
        $txt = preg_replace('/(\s+|^)\*{2}([^\*\n\r]+?[^\* \t]+)\*{2}/m', '$1<b>$2</b>', $txt); // b
        $txt = preg_replace('/(\s+|^)_([^_\n\r]+?[^_ \t]+)_/m', '$1<i>$2</i>', $txt); // i
        $txt = preg_replace('/(\s+|^)`([^`\n\r]+?[^` \t]+)`/m', '$1<code>$2</code>', $txt); // code
        
        // lists
        $txt = preg_replace('/((^( *\* +([^\n\r]+)[^\n]+)$[\n]+)+)/m', "<ul>\n$1</ul>\n", $txt); // ul
        $txt = preg_replace('/^( *\* +([^\n\r]+)[^\n]+)$/m', "<li>$2</li>", $txt); // li

        // blockquotes
        $txt = preg_replace('/((^( *\> +([^\n\r]+)[^\n]+)$\s+)+)/m', "<blockquote>\n$1</blockquote>\n", $txt); // blockquote
        $txt = preg_replace('/^ *\> +(([^\n\r]+)[^\n]+$)/m', "$1", $txt); // remove >
        
        return $txt;
    }
}