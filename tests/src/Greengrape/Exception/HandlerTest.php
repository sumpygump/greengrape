<?php
/**
 * Handler test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests\Exception;

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
class HandlerTest extends \BaseTestCase
{
    /**
     * Kernel object
     *
     * @var Greengrape\Kernel
     */
    protected $_kernel;

    /**
     * Setup before tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $config = new Config();
        $config['debug'] = true;
        $this->_kernel = new Kernel($config);
    }

    public function testInitHandlers()
    {
        Handler::initHandlers($this->_kernel);
        Handler::releaseHandlers();
        $this->assertTrue($this->_kernel instanceof Kernel);
    }

    public function testGetKernel()
    {
        Handler::initHandlers($this->_kernel);
        Handler::releaseHandlers();

        $kernel = Handler::getKernel();

        $this->assertEquals($this->_kernel, $kernel);
    }

    public function testHandleError()
    {
        ob_start();
        Handler::handleError(
            8, 'Test message', 'fakefile.php', '33'
        );
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertStringContainsString(
            "E_NOTICE: Test message in fakefile.php:33",
            $contents
        );
    }

    public function testHandleShutdown()
    {
        // I would have to trigger an error to test the rest of this method,
        // but I couldn't do it, even with output buffering and not have
        // phpunit output some errors after running the tests

        $result = Handler::handleShutdown();

        $this->assertFalse($result);
    }

    public function testHandleException()
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

    public function testHandleExceptionNotFound()
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
