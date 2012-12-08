<?php
/**
 * View test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests;

use Greengrape\View;
use Greengrape\View\Theme;
use Greengrape\Navigation\Collection;
use Greengrape\Navigation\Item;

class MockTheme extends Theme
{
}

/**
 * View Test
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class ViewTest extends \BaseTestCase
{
    /**
     * Setup before tests
     *
     * @return void
     */
    public function setUp()
    {
        mkdir('foobar');
        mkdir('foobar' . DIRECTORY_SEPARATOR . 'templates');
        file_put_contents('foobar' . DIRECTORY_SEPARATOR . 'layout.html', '{{ layout.content|raw }}');
        file_put_contents('foobar' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'default.html', '{{ content | raw }}');

        $testThemesDir = APP_PATH . DIRECTORY_SEPARATOR . 'tests';
        $this->_object = new View(new MockTheme('foobar', '/', $testThemesDir));
        $this->_object->setContentDir(realpath('.'));
    }

    /**
     * Tear down after tests
     *
     * @return void
     */
    public function tearDown()
    {
        passthru('rm -rf foobar');
    }

    /**
     * Test constructor
     *
     * @expectedException PHPUnit_Framework_Error
     * @return void
     */
    public function testConstructNoArgs()
    {
        $view = new View();
    }

    /**
     * testConstructThemeNotExist
     *
     * @expectedException Greengrape\Exception\NotFoundException
     * @return void
     */
    public function testConstructThemeNotExist()
    {
        $view = new View(new MockTheme('foo'));

        $this->assertTrue($view instanceof View);
        $this->assertEquals('foo', $view->getTheme()->getName());
    }

    /**
     * testSetThemeNotTheme
     *
     * @expectedException PHPUnit_Framework_Error
     * @return void
     */
    public function testSetThemeString()
    {
        $this->_object->setTheme('string');
    }

    public function testGetLayout()
    {
        $layout = $this->_object->getLayout();
    }

    /**
     * testGetLayoutFileNotExist
     *
     * @expectedException Greengrape\Exception\NotFoundException
     * @return void
     */
    public function testGetLayoutFileNotExist()
    {
        $view = new View(new MockTheme('foo'));

        $layout = $view->getLayout();
    }

    public function testSetParams()
    {
        $this->_object->setParams(array('a' => '1', 'b' => '2'));

        $expected = array('a' => '1', 'b' => '2');
        $this->assertEquals($expected, $this->_object->getParams());
    }

    public function testSetNavigationItems()
    {
        $collection = new Collection('t1', '/baseurl');

        $this->_object->setNavigationItems($collection);
        $this->assertEquals($collection, $this->_object->getNavigationItems());
    }

    /**
     * testSetNavigationItemsArray
     *
     * @expectedException PHPUnit_Framework_Error
     * @return void
     */
    public function testSetNavigationItemsArray()
    {
        $items = array('a', 'b');
        $this->_object->setNavigationItems($items);
    }

    /**
     * testSetActiveNavigationItemString
     *
     * @expectedException PHPUnit_Framework_Error
     * @return void
     */
    public function testSetActiveNavigationItemString()
    {
        $this->_object->setActiveNavigationItem('aa');
    }

    public function testSetActiveNavigationItem()
    {
        $item = new Item('text1', 'text1/');

        $this->_object->setActiveNavigationItem($item);

        $this->assertEquals($item, $this->_object->getActiveNavigationItem());
    }

    /**
     * testSetActiveSubNavigationItemString
     *
     * @expectedException PHPUnit_Framework_Error
     * @return void
     */
    public function testSetActiveSubNavigationItemString()
    {
        $this->_object->setActiveSubNavigationItem('foobar');
    }

    public function testSetActiveSubNavigationItem()
    {
        $item = new Item('text2', 'text2/');

        $this->_object->setActiveSubNavigationItem($item);

        $this->assertEquals($item, $this->_object->getActiveSubNavigationItem());
    }

    /**
     * testSetSubNavigationItemsArray
     *
     * @expectedException PHPUnit_Framework_Error
     * @return void
     */
    public function testSetSubNavigationItemsArray()
    {
        $items = array('c', 'd', 'e');
        $this->_object->setSubNavigationItems($items);
    }

    public function testSetSubNavigationItems()
    {
        $collection = new Collection('t1', '/baseurl');

        $this->_object->setSubNavigationItems($collection);
        $this->assertEquals($collection, $this->_object->getSubNavigationItems());
    }

    /**
     * testRenderFile
     *
     * @expectedException Greengrape\Exception\NotFoundException
     * @return void
     */
    public function testRenderFileNoExists()
    {
        $output = $this->_object->renderContentFile('fake.md');
    }

    public function testRenderFile()
    {
        file_put_contents('foobar/templates/contentfile.md', '#hiya');

        $output = $this->_object->renderContentFile('foobar/templates/contentfile.md');
        $this->assertContains('<h1>hiya</h1>', $output);

        unlink('foobar/templates/contentfile.md');
    }
}
