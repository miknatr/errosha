<?php

namespace Errosha\Display;

interface DisplayInterface
{
    public function showError($code, $str, $file, $line, $codeText, $url);
}
