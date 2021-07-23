<?php
/**
 * Sitemap test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests;

use Greengrape\Sitemap;
use Greengrape\Location;
use Greengrape\Exception\GreengrapeException;

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
    public function setUp(): void
    {
        $contentDir = 'testcontent';
        mkdir($contentDir);
        mkdir($contentDir . DIRECTORY_SEPARATOR . 'f1');
        mkdir($contentDir . DIRECTORY_SEPARATOR . 'f2');

        file_put_contents($contentDir . DIRECTORY_SEPARATOR . 'index.md', '#hello');
        file_put_contents($contentDir . DIRECTORY_SEPARATOR . 'about.md', '#about');
        file_put_contents($contentDir . DIRECTORY_SEPARATOR . 'f1' . DIRECTORY_SEPARATOR . 'index.md', '#hello1');
        file_put_contents($contentDir . DIRECTORY_SEPARATOR . 'f1' . DIRECTORY_SEPARATOR . 'extra.md', '#extra');
        file_put_contents($contentDir . DIRECTORY_SEPARATOR . 'f2' . DIRECTORY_SEPARATOR . 'index.md', '#hello2');

        $this->_object = new Sitemap($contentDir);
    }

    /**
     * Tear down after tests
     *
     * @return void
     */
    public function tearDown(): void
    {
        passthru('rm -rf testcontent');
    }

    /**
     * Test constructor
     *
     * @return void
     */
    public function testConstructNoArgs()
    {
        $this->expectException(\ArgumentCountError::class);
        $sitemap = new Sitemap();
    }

    public function testConstructEmptyDir()
    {
        mkdir('emptydir');

        $sitemap = new Sitemap('emptydir');

        $this->assertTrue($sitemap instanceof Sitemap);
        $this->assertEquals(0, $sitemap->getCountMapItems());
        rmdir('emptydir');
    }

    /**
     * testConstructArray
     *
     * @return void
     */
    public function testConstructArray()
    {
        $this->expectException(GreengrapeException::class);
        $sitemap = new Sitemap(array('foobar'));
    }

    public function testConstructFileNoExist()
    {
        $sitemap = new Sitemap('fakedir');
        $this->assertEquals(0, $sitemap->getCountMapItems());
    }

    public function testSetContentDir()
    {
        $this->_object->setContentDir('mycontent');
        $this->assertEquals('mycontent', $this->_object->getContentDir());
    }

    public function testGetLocationForUrl()
    {
        $location = $this->_object->getLocationForUrl('/');

        $this->assertTrue($location instanceof Location);
        $this->assertEquals('index.md', $location->getFile());
        $this->assertEquals('', $location->getCanonical());
    }

    public function testGetLocationForUrlNoExist()
    {
        $location = $this->_object->getLocationForUrl('floorbar');

        $this->assertTrue($location instanceof Location);
        $this->assertEquals('floorbar', $location->getFile());
        $this->assertEquals('', $location->getCanonical());
    }

    public function testGetLocationCanonical()
    {
        $location = $this->_object->getLocationForUrl('f1');

        $this->assertEquals('', $location->getFile());
        $this->assertEquals('f1/', $location->getCanonical());
    }

    public function testGetLocationDir()
    {
        $location = $this->_object->getLocationForUrl('f1/');

        $this->assertEquals('f1/index.md', $location->getFile());
        $this->assertEquals('', $location->getCanonical());
    }
}
