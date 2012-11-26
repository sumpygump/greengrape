<?php
/**
 * Layout test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests\View;

use Greengrape\View\Layout;

/**
 * Layout Test
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class LayoutTest extends \BaseTestCase
{
    /**
     * Setup before tests
     *
     * @return void
     */
    public function setUp()
    {
        //$this->_object = new Layout();
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
     * @expectedException PHPUnit_Framework_Error
     * @return void
     */
    public function testConstructNoArgs()
    {
        $layout = new Layout();
    }
}
