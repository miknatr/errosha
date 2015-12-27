<?php

namespace Errosha\Display;

class ProductionDisplay implements DisplayInterface
{
    protected $contentTypeHeader = 'Content-Type: text/html; charset=UTF-8';
    protected $body = 'Internal server error';

    public function showError($code, $str, $file, $line, $codeText, $url)
    {
        header('HTTP/1.1 500 Internal server error');
        header($this->contentTypeHeader);
        echo $this->body;
    }

    public function setContentTypeHeader($contentTypeHeader)
    {
        $this->contentTypeHeader = $contentTypeHeader;
        return $this;
    }

    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }
}
