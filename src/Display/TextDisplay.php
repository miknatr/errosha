<?php

namespace Errosha\Display;

class TextDisplay implements DisplayInterface
{
    public function showError($code, $str, $file, $line, $codeText, $url, $trace)
    {
        header('HTTP/1.1 500 Internal server error');
        header('Content-Type: text/plain; charset=UTF-8');

        echo $codeText . "\n";
        echo "$str\n";
        echo "File: $file:$line\n";
        echo "Url: $url\n";
        echo 'Time: ' . date('Y-m-d H:i:s') . "\n";
        echo "Trace: $trace\n";
    }
}
