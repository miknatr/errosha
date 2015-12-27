<?php

namespace Errosha\Display;

class TextDisplay implements DisplayInterface
{
    public function showError($code, $str, $file, $line, $codeText, $url)
    {
        header('HTTP/1.1 500 Internal server error');
        header('Content-Type: text/plain; charset=UTF-8');

        echo $codeText . "\n";
        echo "Time: " . date('Y-m-d H:i:s') . "\n";
        echo "Url: " . $url . "\n";
        echo "File: $file:$line\n";
        echo "$str\n";
    }
}
