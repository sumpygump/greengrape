<?php
/**
 * Sitemap test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests;

use Greengrape\Sitemap;

/**
 * Sitemap Test
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class SitemapTest extends \BaseTestCase
{
    /**
     * Setup before tests
     *
     * @return void
     */
    public function setUp()
    {
        //$this->_object = new Sitemap();
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
        $sitemap = new Sitemap();
    }
}
