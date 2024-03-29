<?php
/**
 * Request test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests;

use Greengrape\Request;

/**
 * Request Test
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class RequestTest extends BaseTestCase
{
    /**
     * Setup before tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $input = array(
            'REQUEST_URI' => '/all/the/things/about/',
            'SCRIPT_FILENAME' => '/var/www/all/the/things/index.php',
            'PHP_SELF' => '/all/the/things/index.php',
        );

        $this->object = new Request($input);
    }

    /**
     * Test constructor
     *
     * @return void
     */
    public function testConstruct(): void
    {
        $request = new Request(array('foo' => 'bar'));

        $this->assertTrue($request instanceof Request);
        $this->assertEquals('bar', $request->foo);
    }

    public function testConstructString(): void
    {
        $request = new Request('foobar');

        // This is a weird case, because you have to fetch it by the offset 0
        // instead of a key, but whatever, users can do it this way if they want
        $this->assertEquals('foobar', $request->get(0));
    }

    public function testConstructDefaultCorrectlyCombinesServerAndGet(): void
    {
        $_SERVER['foo1'] = 'test1';
        $_GET['foobar'] = 'xyz';
        $_GET['action'] = 'save';

        $request = new Request();

        $this->assertEquals('test1', $request->foo1);
        $this->assertEquals('xyz', $request->foobar);
        $this->assertEquals('save', $request->action);
    }

    public function testGetRequestedFile(): void
    {
        $this->assertEquals('about/', $this->object->getRequestedFile());
    }

    public function testGetRequestedFileRoot(): void
    {
        $input = array(
            'REQUEST_URI' => '/all/the/things/',
            'SCRIPT_FILENAME' => '/var/www/all/the/things/index.php',
            'PHP_SELF' => '/all/the/things/index.php',
        );

        $this->object = new Request($input);

        $this->assertEquals('/', $this->object->getRequestedFile());
    }

    public function testGetBaseUrlBlank(): void
    {
        $this->assertEquals('/all/the/things', $this->object->getBaseUrl());
    }
}
