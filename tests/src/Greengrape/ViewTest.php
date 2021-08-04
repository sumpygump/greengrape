<?php
/**
 * View test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests;

use Greengrape\Exception\GreengrapeException;
use Greengrape\Exception\NotFoundException;
use Greengrape\View;
use Greengrape\View\Layout;
use Greengrape\View\Theme;
use Greengrape\Navigation\Collection;
use Greengrape\Navigation\Item;

/**
 * View Test
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class ViewTest extends BaseTestCase
{
    /**
     * Setup before tests
     *
     * @return void
     */
    public function setUp(): void
    {
        mkdir('foobar');
        mkdir('foobar' . DIRECTORY_SEPARATOR . 'templates');
        file_put_contents(
            'foobar' . DIRECTORY_SEPARATOR . 'layout.html',
            '{{ layout.content|raw }}'
        );
        file_put_contents(
            'foobar' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'main.html',
            '{{ content | raw }}'
        );

        $testThemesDir = APP_PATH . DIRECTORY_SEPARATOR . 'tests';
        $this->object = new View(new MockTheme('foobar', '/', $testThemesDir));
        $this->object->setContentDir(realpath('.'));
    }

    /**
     * Tear down after tests
     *
     * @return void
     */
    public function tearDown(): void
    {
        passthru('rm -rf foobar');
    }

    /**
     * Test constructor
     *
     * @return void
     */
    public function testConstructNoArgs(): void
    {
        $this->expectException(\ArgumentCountError::class);
        // @phpstan-ignore-next-line
        $view = new View();
    }

    /**
     * testConstructThemeNotExist
     *
     * @return void
     */
    public function testConstructThemeNotExist(): void
    {
        $this->expectException(NotFoundException::class);
        $view = new View(new MockTheme('foo'));

        $this->assertTrue($view instanceof View);
        $this->assertEquals('foo', $view->getTheme()->getName());
    }

    /**
     * testSetThemeNotTheme
     *
     * @return void
     */
    public function testSetThemeString(): void
    {
        $this->expectException(\TypeError::class);
        $this->object->setTheme('string');
    }

    public function testGetLayout(): void
    {
        $layout = $this->object->getLayout();
        $this->assertTrue($layout instanceof Layout);
    }

    /**
     * testGetLayoutFileNotExist
     *
     * @return void
     */
    public function testGetLayoutFileNotExist(): void
    {
        $this->expectException(NotFoundException::class);
        $view = new View(new MockTheme('foo'));

        $layout = $view->getLayout();
    }

    public function testSetParams(): void
    {
        $this->object->setParams(array('a' => '1', 'b' => '2'));

        $expected = array('a' => '1', 'b' => '2');
        $this->assertEquals($expected, $this->object->getParams());
    }

    public function testSetNavigationItems(): void
    {
        $collection = new Collection('t1', '/baseurl');

        $this->object->setNavigationItems($collection);
        $this->assertEquals($collection, $this->object->getNavigationItems());
    }

    /**
     * testSetNavigationItemsArray
     *
     * @return void
     */
    public function testSetNavigationItemsArray(): void
    {
        $this->expectException(\TypeError::class);
        $items = array('a', 'b');
        $this->object->setNavigationItems($items);
    }

    /**
     * testSetActiveNavigationItemString
     *
     * @return void
     */
    public function testSetActiveNavigationItemString(): void
    {
        $this->expectException(\TypeError::class);
        $this->object->setActiveNavigationItem('aa');
    }

    public function testSetActiveNavigationItem(): void
    {
        $item = new Item('text1', 'text1/');

        $this->object->setActiveNavigationItem($item);

        $this->assertEquals($item, $this->object->getActiveNavigationItem());
    }

    /**
     * testSetActiveSubNavigationItemString
     *
     * @return void
     */
    public function testSetActiveSubNavigationItemString(): void
    {
        $this->expectException(\TypeError::class);
        $this->object->setActiveSubNavigationItem('foobar');
    }

    public function testSetActiveSubNavigationItem(): void
    {
        $item = new Item('text2', 'text2/');

        $this->object->setActiveSubNavigationItem($item);

        $this->assertEquals($item, $this->object->getActiveSubNavigationItem());
    }

    /**
     * testSetSubNavigationItemsArray
     *
     * @return void
     */
    public function testSetSubNavigationItemsArray(): void
    {
        $this->expectException(\TypeError::class);
        $items = array('c', 'd', 'e');
        $this->object->setSubNavigationItems($items);
    }

    public function testSetSubNavigationItems(): void
    {
        $collection = new Collection('t1', '/baseurl');

        $this->object->setSubNavigationItems($collection);
        $this->assertEquals($collection, $this->object->getSubNavigationItems());
    }

    /**
     * testRenderFile
     *
     * @return void
     */
    public function testRenderFileNoExists(): void
    {
        $this->expectException(NotFoundException::class);
        $output = $this->object->renderContentFile('fake.md');
    }

    public function testRenderFile(): void
    {
        file_put_contents('foobar/templates/contentfile.md', '#hiya');

        $output = $this->object->renderContentFile('foobar/templates/contentfile.md');
        $this->assertStringContainsString('<h1>hiya</h1>', $output);

        unlink('foobar/templates/contentfile.md');
    }
}
