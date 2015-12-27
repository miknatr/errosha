<?php

namespace Errosha\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

class FileLogger implements LoggerInterface
{
    use LoggerTrait;

    protected $filename;

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    public function log($level, $message, array $context = array())
    {
        $dt = date('Y-m-d H:i:s');
        file_put_contents($this->filename, "[$dt] $message\n", FILE_APPEND);
    }
}
