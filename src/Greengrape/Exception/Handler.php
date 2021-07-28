<?php

namespace Greengrape\Exception;

use Greengrape\Csp;
use Greengrape\Kernel;
use Greengrape\Request;
use Greengrape\View;
use Greengrape\View\Theme;
use Greengrape\View\Content;

/**
 * Exception Handler
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 */
class Handler
{
    /**
     * Storage of kernel object
     *
     * @var \Greengrape\Kernel
     */
    protected static $_kernel;

    /**
     * Init the error handlers
     *
     * @param Kernel $kernel Kernel object
     * @return void
     */
    public static function initHandlers(Kernel $kernel)
    {
        self::setKernel($kernel);

        set_exception_handler(
            array('\Greengrape\Exception\Handler', 'handleException')
        );

        set_error_handler(
            array('\Greengrape\Exception\Handler', 'handleError')
        );

        register_shutdown_function(
            array('\Greengrape\Exception\Handler', 'handleShutdown')
        );
    }

    /**
     * Restore original error and exception handlers
     *
     * @return void
     */
    public static function releaseHandlers()
    {
        restore_exception_handler();
        restore_error_handler();
    }

    /**
     * Set the kernel object
     *
     * @param \Greengrape\Kernel $kernel Apricot Kernel object
     * @return void
     */
    public static function setKernel($kernel)
    {
        self::$_kernel = $kernel;
    }

    /**
     * Get Kernel object
     *
     * @return \Greengrape\Kernel
     */
    public static function getKernel()
    {
        return self::$_kernel;
    }

    /**
     * Handle an error
     *
     * @return void
     */
    public static function handleError()
    {
        list($errno, $message, $file, $line) = func_get_args();

        $message = self::convertErrorCode($errno)
            . ": " . $message . " in " . $file . ":" . $line;

        print $message;
    }

    /**
     * Handle a shutdown
     *
     * @return bool
     */
    public static function handleShutdown()
    {
        $error = error_get_last();

        if (empty($error)) {
            return false;
        }

        // This way fatal errors will get handled as well.
        self::handleError(
            $error['type'], $error['message'],
            $error['file'], $error['line']
        );

        return true;
    }

    /**
     * Handle thrown exceptions
     *
     * @param \Throwable $exception The Exception object
     * @return void
     */
    public static function handleException(\Throwable $exception)
    {
        $request = new Request();

        try {
            $theme = new Theme(self::getKernel()->getConfig('theme'), $request->getBaseUrl());
            $theme->setDefaultTitle(self::getKernel()->getConfig('sitename'));
            $view  = new View($theme);

            $view->setParams(self::getKernel()->getConfig());

            if ($exception instanceof \Greengrape\Exception\NotFoundException) {
                $httpHeader   = 'HTTP/1.1 404 Not Found';
                $templateFile = '404.html';
            } else {
                $httpHeader   = 'HTTP/1.1 500 Internal Server Error';
                $templateFile = 'error.html';
            }

            if (!headers_sent()) {
                header($httpHeader);
            }

            $content = new Content('', $view);
            $content->setTemplateFile($templateFile);

            if (self::getKernel()->getConfig('debug')) {
                $content->setContent($exception->getMessage() . '<pre>' . $exception->getTraceAsString() . '</pre>');
            }

            $vars = [
                'trace' => self::displayException($exception),
            ];

            $csp = new Csp(self::getKernel()->getConfig('csp'));
            $view->setParam('nonce', $csp->getNonce());
            if (!headers_sent()) {
                $csp->render();
            }

            echo $view->render($content, $vars);
        } catch (\Throwable $newException) {
            $errorTitle = 'Exception found while handling exception:';
            $message = htmlentities($newException->getMessage());
            printf(self::EXCEPTION_MESSAGE_CAPSULE, $errorTitle, $message);
            print self::displayException($newException);

            if ($newException->getMessage() != $exception->getMessage()) {
                $errorTitle = 'This was the original exception:';
                $message = htmlentities($exception->getMessage());
                printf(self::EXCEPTION_MESSAGE_CAPSULE, $errorTitle, $message);
            }
        }
    }

    /**
     * Get the message from the exception that includes the file and line number
     *
     * @param \Throwable $exception Exception object
     * @return string
     */
    public static function getInformativeMessage(\Throwable $exception)
    {
        return "Error code #" . $exception->getCode()
            . " in file " . $exception->getFile()
            . " on line " . $exception->getLine() . ".";
    }

    /**
     * Display exception
     *
     * @param \Throwable $e Exception object
     * @return string
     */
    public static function displayException(\Throwable $e)
    {
        $out  = "";
        $out .= "<p>"
            . get_class($e) . ": "
            . self::getInformativeMessage($e) . "</p>";
        $out .= "<p>Trace:</p>";

        $trace = $e->getTrace();

        $out  .= "<table class=\"table table-bordered table-condensed table-striped\">"
            . "<tr><th>#</th>"
            . "<th style=\"text-align:left\">function</th>"
            . "<th style=\"text-align:left\">location</th>"
            . "<th style=\"text-align:left\">args</th></tr>";

        foreach ($trace as $i => $tl) {
            if (!isset($tl['args'])) {
                $tl['args'] = [];
            }
            $file  = isset($tl['file']) ? $tl['file'] : '';
            $class = isset($tl['class']) ? $tl['class'] : 'main';
            $line  = isset($tl['line']) ? $tl['line'] : '0';
            $out  .= "<tr>"
                . "<td>" . $i . "</td>"
                . "<td>" . $class . "::" .  $tl['function'] . "()</td>"
                . "<td>" . $file  . ":" . $line . "</td>"
                . "<td>" . self::renderTraceArgs($tl['args']) . "</td>"
                . "</tr>";
        }

        $out .= "</table>";

        return $out;
    }

    /**
     * Display Trace Args
     *
     * @param mixed $args Arguments to display
     * @param string $glue The glue used to implode() the args if array
     * @return string
     */
    public static function renderTraceArgs($args, $glue="\n")
    {
        $out = '';

        if (is_array($args)) {
            foreach ($args as $arg) {
                if (is_object($arg)) {
                    $out .= get_class($arg) . $glue;
                } else {
                    if (is_array($arg)) {
                        $out .= 'Array' . $glue;
                        continue;
                    }
                    $out .= '"' . substr($arg, 0, 40) . '..."' . $glue;
                }
            }
        } else {
            $out .= '"' . substr($args, 0, 40) . '..."';
        }

        return htmlentities($out);
    }

    /**
     * Convert an error code into the PHP error constant name
     *
     * @param int $code The PHP error code
     * @return string
     */
    protected static function convertErrorCode($code)
    {
        $errorLevels = array(
            1     => 'E_ERROR',
            2     => 'E_WARNING',
            4     => 'E_PARSE',
            8     => 'E_NOTICE',
            16    => 'E_CORE_ERROR',
            32    => 'E_CORE_WARNING',
            64    => 'E_COMPILE_ERROR',
            128   => 'E_COMPILE_WARNING',
            256   => 'E_USER_ERROR',
            512   => 'E_USER_WARNING',
            1024  => 'E_USER_NOTICE',
            2048  => 'E_STRICT',
            4096  => 'E_RECOVERABLE_ERROR',
            8192  => 'E_DEPRECATED',
            16384 => 'E_USER_DEPRECATED',
        );

        return $errorLevels[$code];
    }

    const EXCEPTION_MESSAGE_CAPSULE = '<div style="margin:4px;padding:8px;color:#b94a48;background-color:#f2dede;border:1px solid #eed3d7;border-radius:4px;"><p style="margin:0;font-size:24px;font-weight:bold;">Error: %s</p><pre>%s</pre></div>';
}
