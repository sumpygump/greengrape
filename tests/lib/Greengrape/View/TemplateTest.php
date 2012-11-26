<?php
/**
 * Template test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests\View;

use Greengrape\View\Template;

/**
 * Template Test
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class TemplateTest extends \BaseTestCase
{
    /**
     * Setup before tests
     *
     * @return void
     */
    public function setUp()
    {
        //$this->_object = new Template();
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
        $template = new Template();
    }
}
