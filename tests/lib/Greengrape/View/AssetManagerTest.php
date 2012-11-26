<?php
/**
 * AssetManager test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests\View;

use Greengrape\View\AssetManager;

/**
 * AssetManager Test
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class AssetManagerTest extends \BaseTestCase
{
    /**
     * Setup before tests
     *
     * @return void
     */
    public function setUp()
    {
        //$this->_object = new AssetManager();
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
        $assetManager = new AssetManager();
    }
}
