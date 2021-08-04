<?php
/**
 * Handler test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests\Exception;

use Greengrape\Tests\BaseTestCase;
use Greengrape\Config;
use Greengrape\Exception\Handler;
use Greengrape\Exception\NotFoundException;
use Greengrape\Kernel;

/**
 * Handler Test
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class HandlerTest extends BaseTestCase
{
    /**
     * Kernel object
     *
     * @var Kernel
     */
    protected $kernel;

    /**
     * Setup before tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $config = new Config();
        $config['debug'] = true;
        $this->kernel = new Kernel($config);
    }

    public function testInitHandlers(): void
    {
        Handler::initHandlers($this->kernel);
        Handler::releaseHandlers();
        $this->assertTrue($this->kernel instanceof Kernel);
    }

    public function testGetKernel(): void
    {
        Handler::initHandlers($this->kernel);
        Handler::releaseHandlers();

        $kernel = Handler::getKernel();

        $this->assertEquals($this->kernel, $kernel);
    }

    public function testHandleError(): void
    {
        ob_start();
        Handler::handleError(
            8,
            'Test message',
            'fakefile.php',
            33
        );
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertStringContainsString(
            "E_NOTICE: Test message in fakefile.php:33",
            $contents
        );
    }

    public function testHandleShutdown(): void
    {
        // I would have to trigger an error to test the rest of this method,
        // but I couldn't do it, even with output buffering and not have
        // phpunit output some errors after running the tests

        $result = Handler::handleShutdown();

        $this->assertFalse($result);
    }

    public function testHandleException(): void
    {
        $exception = new \Exception("Things are broken.", 121);

        ob_start();
        Handler::handleException($exception);
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertStringContainsString(
            "<title>[Greengrape]</title>",
            $contents
        );
        $this->assertStringContainsString(
            "<h1>Error 500 Internal Server Error</h1>",
            $contents
        );
        $this->assertStringContainsString(
            "Things are broken",
            $contents
        );
    }

    public function testHandleExceptionNotFound(): void
    {
        $exception = new NotFoundException("Things are broken.", 121);

        ob_start();
        Handler::handleException($exception);
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertStringContainsString(
            "<title>[Greengrape]</title>",
            $contents
        );
        $this->assertStringContainsString(
            "<h1>Error 404 Page Not Found</h1>",
            $contents
        );
        $this->assertStringContainsString(
            "Things are broken",
            $contents
        );
    }
}
