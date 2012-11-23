<?php

namespace Greengrape\Exception;

use Greengrape\Kernel;
use Greengrape\Request;
use Greengrape\View;
use Greengrape\View\Theme;
use Greengrape\View\Content;

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
     * @param Greengrape\Kernel $kernel Kernel object
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

        $message = self::_convertErrorCode($errno)
            . ": " . $message . " in " . $file . ":" . $line;

        print $message;
    }

    /**
     * Handle a shutdown
     *
     * @return void
     */
    public static function handleShutdown()
    {
        $error = error_get_last();

        if (!empty($error)) {
            // This way fatal errors will get logged as well.
            self::handleError(
                $error['type'], $error['message'],
                $error['file'], $error['line']
            );
        }
    }

    /**
     * Handle thrown exceptions
     *
     * @param \Exception $exception The Exception object
     * @return void
     */
    public static function handleException(\Exception $exception)
    {
        $request = new Request();

        try {
            $theme = new Theme(self::getKernel()->getConfig('theme'), $request->getBaseUrl());
            $view  = new View($theme);

            $content = new Content('', $theme);
            $content->setTemplateFile('error.html');
            $content->setContent($exception->getMessage());

            echo $view->render($content);
        } catch (\Exception $newException) {
            print 'Exception found while handling exception: ' . $newException->getMessage() . "\n";
            print 'This was the original exception: ' . $exception->getMessage();
        }
    }

    /**
     * Convert an error code into the PHP error constant name
     *
     * @param int $code The PHP error code
     * @return string
     */
    protected static function _convertErrorCode($code)
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
}
