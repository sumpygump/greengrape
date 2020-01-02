<?php
/**
 * NotFoundException test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests\Exception;

use Greengrape\Exception\NotFoundException;

/**
 * NotFoundException Test
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class NotFoundExceptionTest extends \BaseTestCase
{
    /**
     * Setup before tests
     *
     * @return void
     */
    public function setUp()
    {
        $this->_object = new NotFoundException();
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
        $this->assertTrue($this->_object instanceof NotFoundException);
    }
}
