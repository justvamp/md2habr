<?php

/**
 * Allows to convert an article in markdown syntax right to correct
 * habrahabr article that is ready to publish.
 */
class MarkdownToHabrahabrConverter
{
    public function convert($txt)
    {
        $txt = $this->convertSources($txt);
        $txt = $this->convertImages($txt);
        $txt = $this->convertLinks($txt);
        $txt = $this->convertHeaders($txt);
        $txt = $this->convertInlines($txt);
        $txt = $this->convertLists($txt);
        $txt = $this->convertBlockquotes($txt);

        return $txt;
    }

    /* TODO: refactor */
    private function convertSources($txt)
    {
        $txt = preg_replace('/[\n\r]*```([\w]*)(.+?)```[\n\r]*/s', "\n\n<source lang=\"$1\">$2</source>\n", $txt);
        return $txt;
    }

    private function convertImages($txt)
    {
        $txt = preg_replace('/\!\[([^\n\r]+?)\][ \t]*\((.+?)\)/s', '<img src="$2" alt="$1"></img>', $txt);
        return $txt;
    }

    private function convertLinks($txt)
    {
        $txt = preg_replace('/\[([^\n\r]+?)\][ \t]*\((http.+?)\)/s', '<a href="$2">$1</a>', $txt);
        return $txt;
    }

    private function convertHeaders($txt)
    {
        $txt = preg_replace('/^\s*#{4}[ \t]+(.+?)\s*$/ms', "<h6>$1</h6>", $txt);
        $txt = preg_replace('/^\s*#{3}[ \t]+(.+?)\s*$/ms', "<h5>$1</h5>", $txt);
        $txt = preg_replace('/^\s*#{2}[ \t]+(.+?)\s*$/ms', "<h4>$1</h4>", $txt);

        return $txt;
    }

    private function convertInlines($txt)
    {
        $txt = preg_replace('/(\s+|^)\*{2}([^\*\n\r]+?[^\* \t]+)\*{2}/m', '$1<b>$2</b>', $txt);
        $txt = preg_replace('/(\s+|^)_([^_\n\r]+?[^_ \t]+)_/m', '$1<i>$2</i>', $txt);
        $txt = preg_replace('/(\s+|^)`([^`\n\r]+?[^` \t]+)`/m', '$1<code>$2</code>', $txt);

        return $txt;
    }

    private function convertLists($txt)
    {
        $txt = preg_replace('/((^( *\* +([^\n\r]+)[^\n]+)$[\n]+)+)/m', "<ul>\n$1</ul>\n", $txt);
        $txt = preg_replace('/^( *\* +([^\n\r]+)[^\n]+)$/m', "<li>$2</li>", $txt);

        return $txt;
    }

    private function convertBlockquotes($txt)
    {
        $txt = preg_replace('/((^( *\> +([^\n\r]+)[^\n]+)$\s+)+)/m', "<blockquote>\n$1</blockquote>\n", $txt);
        $txt = preg_replace('/^ *\> +(([^\n\r]+)[^\n]+$)/m', "$1", $txt);

        return $txt;
    }
}