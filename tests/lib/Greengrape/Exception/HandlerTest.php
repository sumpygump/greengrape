<?php
/**
 * Handler test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests\Exception;

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
    public function setUp()
    {
        $config = array();
        $this->_kernel = new Kernel($config);
    }

    /**
     * Tear down after tests
     *
     * @return void
     */
    public function tearDown()
    {
    }

    public function testInitHandlers()
    {
        Handler::initHandlers($this->_kernel);
        Handler::releaseHandlers();
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

        $this->assertContains(
            "E_NOTICE: Test message in fakefile.php:33",
            $contents
        );
    }

    public function testHandleShutdown()
    {
        // handleShutdown uses error_get_last() to fetch the most recent error 
        // message, so we just need to trigger an error. 
        ob_start();
        @file_get_contents('gg');
        $contents = ob_get_contents();
        ob_end_clean();

        ob_start();
        Handler::handleShutdown();
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertContains(
            "E_WARNING: file_get_contents(gg): failed to open stream",
            $contents
        );
    }

    public function testHandleException()
    {
        $exception = new \Exception("Things are broken.", 121);

        ob_start();
        Handler::handleException($exception);
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertContains(
            "<title>[Greengrape]</title>",
            $contents
        );
        $this->assertContains(
            "<h1>Error 500 Internal Server Error</h1>",
            $contents
        );
        $this->assertContains(
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

        $this->assertContains(
            "<title>[Greengrape]</title>",
            $contents
        );
        $this->assertContains(
            "<h1>Error 404 Page Not Found</h1>",
            $contents
        );
        $this->assertContains(
            "Things are broken",
            $contents
        );
    }
}
