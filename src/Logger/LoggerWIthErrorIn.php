<?php

namespace Errosha\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;

class LoggerWIthErrorIn implements LoggerInterface
{
    use LoggerTrait;

    public function log($level, $message, array $context = array())
    {
        $a = $b;
    }
}
