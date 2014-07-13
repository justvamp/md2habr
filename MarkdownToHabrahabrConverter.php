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
    protected function convertSources($txt)
    {
        $txt = preg_replace('/[\n\r]*```([\w]*)(.+?)```[\n\r]*/s', "\n\n<source lang=\"$1\">$2</source>\n", $txt);
        return $txt;
    }

    protected function convertImages($txt)
    {
        $txt = preg_replace('/\!\[([^\n\r]+?)\][ \t]*\((.+?)\)/s', '<img src="$2" alt="$1"></img>', $txt);
        return $txt;
    }

    protected function convertLinks($txt)
    {
        $txt = preg_replace('/\[([^\n\r]+?)\][ \t]*\((http.+?)\)/s', '<a href="$2">$1</a>', $txt);
        return $txt;
    }

    protected function convertHeaders($txt)
    {
        $txt = $this->parseStringLiteral($txt, '####', 'h6');
        $txt = $this->parseStringLiteral($txt, '###', 'h5');
        $txt = $this->parseStringLiteral($txt, '##', 'h4');

        return $txt;
    }

    protected function convertInlines($txt)
    {
        $txt = $this->parseInlineLiteral($txt, '**', 'b');
        $txt = $this->parseInlineLiteral($txt, '_', 'i');
        $txt = $this->parseInlineLiteral($txt, '`', 'code');

        return $txt;
    }

    protected function convertLists($txt)
    {
        $txt = preg_replace('/((^( *\* +([^\n\r]+)[^\n]+)$[\n]+)+)/m', "<ul>\n$1</ul>\n", $txt);
        $txt = preg_replace('/^( *\* +([^\n\r]+)[^\n]+)$/m', "<li>$2</li>", $txt);

        return $txt;
    }

    protected function convertBlockquotes($txt)
    {
        $txt = preg_replace('/((^( *\> +([^\n\r]+)[^\n]+)$\s+)+)/m', "<blockquote>\n$1</blockquote>\n", $txt);
        $txt = preg_replace('/^ *\> +(([^\n\r]+)[^\n]+$)/m', "$1", $txt);

        return $txt;
    }

    protected function parseInlineLiteral($string, $delimiter, $tag)
    {
        $u = $this->addSlashes($this->extractUniqueSymbols($delimiter));
        $d = $this->addSlashes($delimiter);
        return preg_replace(
            '/(\s+|^)' . $d . '([^' . $u . '\n\r]+?[^' . $u . ' \t]+)' . $d . '/m',
            '$1<' . $tag . '>$2</' . $tag . '>',
            $string
        );
    }

    protected function parseStringLiteral($string, $prefix, $tag)
    {
        $p = $this->addSlashes($prefix);
        return preg_replace(
            '/^\s*' . $p . '[ \t]+(.+?)\s*$/ms',
            '<' . $tag . '>$1</' . $tag . '>',
            $string
        );
    }

    private function extractUniqueSymbols($string)
    {
        $allSymbolsArray = str_split($string);
        $uniqueSymbolsArray = array_unique($allSymbolsArray);

        return implode('', $uniqueSymbolsArray);
    }

    private function addSlashes($string)
    {
        return preg_replace('/(.)/s', '\\\$1', $string); // abc => /a/b/c
    }
}
