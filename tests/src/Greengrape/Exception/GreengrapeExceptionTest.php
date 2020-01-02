<?php
/**
 * GreengrapeException test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests\Exception;

use Greengrape\Exception\GreengrapeException;

/**
 * GreengrapeException Test
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class GreengrapeExceptionTest extends \BaseTestCase
{
    /**
     * Setup before tests
     *
     * @return void
     */
    public function setUp()
    {
        $this->_object = new GreengrapeException();
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
        $this->_object = new GreengrapeException();

        $this->assertTrue($this->_object instanceof GreengrapeException);
    }
}
