<?php

namespace Errosha;

class SimpleErrorHandler
{
    private $errorLog;
    private $memoryForFatalErrorHandling;
    private $showErrors;

    public function __construct($errorLog, $showErrors = true)
    {
        $this->errorLog = $errorLog;
        $this->memoryForFatalErrorHandling = str_repeat(' ', 50 * 1024);

        error_reporting(-1);
        ini_set("display_errors", true);

        set_error_handler(array($this, 'handleError'));
        set_exception_handler(array($this, 'handleException'));
        register_shutdown_function(array($this, 'handleFatalError'));
        $this->showErrors = $showErrors;
    }

    public function handleError($code, $str, $file, $line)
    {
        if (!error_reporting()) { // если код хочет молча есть ошибки с помощью @, то мы ему поможем
            return;
        }

        $dt  = date('Y-m-d H:i:s');
        $level = self::codeToString($code);
        $msg = "[$dt] $level: $str in $file:$line";

        file_put_contents($this->errorLog, $msg, FILE_APPEND);

        if (defined('STDIN')) {
            fwrite(STDERR, $msg . "\n");
            exit(1);
        } elseif ($this->showErrors) {
            header("HTTP/1.1 500 Internal server error");
            header('Content-Type: text/html; charset=UTF-8');
            die($msg . "\n");
        } else {
            die("Internal server error\n");
        }

    }

    public function handleException(\Exception $e)
    {
        $this->handleError($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
    }

    private static $fatalErrors = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR);
    public function handleFatalError()
    {
        $this->memoryForFatalErrorHandling = null;
        $lastError = error_get_last();
        if ($lastError && in_array($lastError['type'], self::$fatalErrors)) {
            $this->handleError($lastError['type'], $lastError['message'], $lastError['file'], $lastError['line']);
        }
    }

    private static function codeToString($code)
    {
        switch ($code) {
            case E_ERROR:
                return 'E_ERROR';
            case E_WARNING:
                return 'E_WARNING';
            case E_PARSE:
                return 'E_PARSE';
            case E_NOTICE:
                return 'E_NOTICE';
            case E_CORE_ERROR:
                return 'E_CORE_ERROR';
            case E_CORE_WARNING:
                return 'E_CORE_WARNING';
            case E_COMPILE_ERROR:
                return 'E_COMPILE_ERROR';
            case E_COMPILE_WARNING:
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR:
                return 'E_USER_ERROR';
            case E_USER_WARNING:
                return 'E_USER_WARNING';
            case E_USER_NOTICE:
                return 'E_USER_NOTICE';
            case E_STRICT:
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR:
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED:
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED:
                return 'E_USER_DEPRECATED';
        }

        return 'Unknown error code';
    }
}
