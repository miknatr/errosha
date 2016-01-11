<?php

namespace Errosha;

use Errosha\Display\DisplayInterface;
use Psr\Log\LoggerInterface;

class ErrorHandler
{
    protected $memoryForFatalErrorHandling;

    protected $ignoreLevels = array();

    /** @var LoggerInterface[] */
    protected $loggers = array();

    /** @var DisplayInterface */
    protected $display;

    protected $logTrace = true;

    public function __construct(DisplayInterface $display, $errorReporting = -1)
    {
        // reserve memory for fatal error handling
        $this->memoryForFatalErrorHandling = str_repeat(' ', 50 * 1024);

        // handle all errors
        error_reporting($errorReporting);

        // from that moment error handler outputs error messages and we can disable php display_errors
        ini_set("display_errors", false);

        set_error_handler(array($this, 'handleError'));
        set_exception_handler(array($this, 'handleException'));
        register_shutdown_function(array($this, 'handleFatalError'));

        $this->setDisplay($display);
    }

    public function setIgnoreLevels(array $levels)
    {
        $this->ignoreLevels = $levels;
        return $this;
    }

    public function setDisplay(DisplayInterface $display)
    {
        $this->display = $display;
        return $this;
    }

    public function setLogTrace($logTrace)
    {
        $this->logTrace = $logTrace;
        return $this;
    }

    public function addLogger(LoggerInterface $logger)
    {
        $this->loggers[] = $logger;
        return $this;
    }

    public function handleException(\Exception $e)
    {
        $this->handleErrorAndExit($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString(), true);
    }

    protected static $fatalErrors = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR);
    public function handleFatalError()
    {
        $this->memoryForFatalErrorHandling = null;
        $lastError = error_get_last();
        if ($lastError && in_array($lastError['type'], self::$fatalErrors)) {
            $this->handleErrorAndExit($lastError['type'], $lastError['message'], $lastError['file'], $lastError['line'], (new \Exception)->getTraceAsString());
        }
    }

    public function handleError($code, $str, $file, $line)
    {
        if (!error_reporting()) { // suppress errors with @
            return;
        }

        if (in_array($code, $this->ignoreLevels)) {
            return;
        }

        $this->handleErrorAndExit($code, $str, $file, $line, (new \Exception)->getTraceAsString());
    }

    public function handleErrorAndExit($code, $str, $file, $line, $trace = '', $isException = false)
    {
        $url = $this->getUrl();

        $codeText = $isException ? "EXCEPTION" . ($code ? "(code $code)" : '') : $this->codeToString($code);

        $trace = str_replace(array("\r\n", "\r", "\n"), ' ||| ', $trace); // newlines to |||

        $logMsg = $codeText . ": $str in $file:$line via " . $url . ($this->logTrace ? ' ||| TRACE: ' . $trace : '');

        foreach ($this->loggers as $logger) {
            $this->callLogger($logger, $code, $logMsg);
        }

        if (defined('STDIN')) {
            fwrite(STDERR, $logMsg);
            exit(1);
        } else {
            $this->display->showError($code, $str, $file, $line, $codeText, $url);
            exit;
        }
    }

    protected function callLogger(LoggerInterface $logger, $code, $msg)
    {

        switch ($code) {
            case E_ERROR:
            case E_RECOVERABLE_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
            case E_PARSE:
                $logger->error($msg);
                break;
            case E_WARNING:
            case E_USER_WARNING:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
                $logger->warning($msg);
                break;
            case E_NOTICE:
            case E_USER_NOTICE:
                $logger->notice($msg);
                break;
            case E_STRICT:
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                $logger->info($msg);
                break;
            default:
                $logger->critical($msg);
        }
    }

    protected function codeToString($code)
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

        return 'E_UNKNOWN';
    }

    protected function getUrl()
    {
        if (defined('STDIN')) {
            return 'CLI';
        }

        $insecure = empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off';
        $port = $_SERVER['SERVER_PORT'];
        $defaultPort = ($insecure && $port == 80) || (!$insecure && $port == 443);

        return $_SERVER['REQUEST_METHOD'] . ' '
                . ($insecure ? 'http://' : 'https://')
                . $_SERVER['SERVER_NAME'] . ($defaultPort ? '' : ':' . $port)
                . $_SERVER['REQUEST_URI']
        ;
    }
}
