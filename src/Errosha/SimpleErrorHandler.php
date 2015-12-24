<?php

namespace Errosha;

class SimpleErrorHandler
{
    protected $errorLog;
    protected $memoryForFatalErrorHandling;
    protected $showErrors;

    protected $ignoreLevels = array();

    protected $headerIfShowErrorOff = 'Content-Type: text/plain; charset=UTF-8';
    protected $bodyIfShowErrorOff = 'Internal server error';

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

    public function setIgnoreLevels(array $levels)
    {
        $this->ignoreLevels = $levels;
        return $this;
    }

    public function setHeaderIfShowErrorOff($header)
    {
        $this->headerIfShowErrorOff = $header;
        return $this;
    }

    public function setBodyIfShowErrorOff($body)
    {
        $this->bodyIfShowErrorOff = $body;
        return $this;
    }

    public function handleError($code, $str, $file, $line)
    {
        if (!error_reporting()) { // если код хочет молча есть ошибки с помощью @, то мы ему поможем
            return;
        }

        if (in_array($code, $this->ignoreLevels)) {
            return;
        }

        $level = self::codeToString($code);
        $msg = "$level: $str in $file:$line";

        if ($this->errorLog instanceof \Closure) {
            call_user_func($this->errorLog, $msg);
        } else {
            $dt  = date('Y-m-d H:i:s');
            $msg = "[$dt] $msg\n";

            file_put_contents($this->errorLog, $msg, FILE_APPEND);
        }


        if (defined('STDIN')) {
            fwrite(STDERR, $msg . "\n");
            exit(1);
        } else {
            $msg .= ', ' . $_SERVER['REQUEST_METHOD'] . ' '
                . ((empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off') ? 'http://' : 'https://') . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT']
                . $_SERVER['REQUEST_URI']
            ;

            header("HTTP/1.1 500 Internal server error");

            if ($this->showErrors) {
                header('Content-Type: text/plain; charset=UTF-8');
                echo $msg;
            } else {
                header($this->headerIfShowErrorOff);
                echo $this->bodyIfShowErrorOff;
            }

            die;
        }
    }

    public function handleException(\Exception $e)
    {
        $this->handleError($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
    }

    protected static $fatalErrors = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR);
    public function handleFatalError()
    {
        $this->memoryForFatalErrorHandling = null;
        $lastError = error_get_last();
        if ($lastError && in_array($lastError['type'], self::$fatalErrors)) {
            $this->handleError($lastError['type'], $lastError['message'], $lastError['file'], $lastError['line']);
        }
    }

    protected static function codeToString($code)
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
