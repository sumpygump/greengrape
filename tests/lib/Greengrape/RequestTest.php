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
class RequestTest extends \BaseTestCase
{
    /**
     * Setup before tests
     *
     * @return void
     */
    public function setUp()
    {
        $this->_object = new Request();
    }

    /**
     * Tear down after tests
     *
     * @return void
     */
    public function tearDown()
    {
    }

    /**
     * Test constructor
     *
     * @return void
     */
    public function testConstruct()
    {
    }
}
