<?php
/**
 * Layout test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests\View;

use Greengrape\Tests\BaseTestCase;
use Greengrape\View\Layout;
use Greengrape\View\Theme;
use Greengrape\Navigation\Collection;
use Greengrape\Navigation\Item;

/**
 * Layout Test
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class LayoutTest extends BaseTestCase
{
    /**
     * Setup before tests
     *
     * @return void
     */
    public function setUp(): void
    {
        mkdir('testtheme');

        file_put_contents('layout.html', "{{ layout.content|raw }}{{ something }}");

        mkdir('testtheme' . DIRECTORY_SEPARATOR . 'templates');

        file_put_contents(
            'testtheme'. DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . '_navigation.html',
            '{% for item in navigation %}{{ item.href(true) }}{% endfor %}'
        );

        file_put_contents(
            'testtheme'. DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . '_subnavigation.html',
            '{% for item in navigation %}{{ item.href(true) }}{% endfor %}'
        );

        $theme = new Theme('testtheme', '/', APP_PATH . DIRECTORY_SEPARATOR . 'tests');
        $theme->setDefaultTitle('[testing]');

        $this->object = new Layout('layout.html', $theme);
    }

    /**
     * Tear down after tests
     *
     * @return void
     */
    public function tearDown(): void
    {
        passthru('rm -rf testtheme');
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
        $layout = new Layout();
    }

    public function testConstructDefault(): void
    {
        $theme = new Theme('testtheme', '/', APP_PATH . DIRECTORY_SEPARATOR . 'tests');
        $layout = new Layout('layout.html', $theme);

        $this->assertTrue($layout instanceof Layout);
    }

    public function testGetTitle(): void
    {
        $this->object->setTitle('foobar2');

        $this->assertEquals('foobar2 | [testing]', $this->object->getTitle());
    }

    public function testSetTitleReset(): void
    {
        $this->object->setTitle('foobar2', true);

        $this->assertEquals('foobar2', $this->object->getTitle());
    }

    public function testSetTitleBlank(): void
    {
        $this->object->setTitle('');

        $this->assertEquals('[testing]', $this->object->getTitle());
    }

    public function testSetTitleWhitespace(): void
    {
        $this->object->setTitle('   ');

        $this->assertEquals('[testing]', $this->object->getTitle());
    }

    public function testSetContent(): void
    {
        $this->object->setContent('this is the content');

        $this->assertEquals('this is the content', $this->object->getContent());
    }

    public function testSetParams(): void
    {
        $params = array(
            'face' => 'palm',
            'total' => '81.22',
        );
        $this->object->setParams($params);

        $this->assertEquals('palm', $this->object->getParam('face'));
    }

    public function testGetParamNotSet(): void
    {
        $params = array(
            'face' => 'palm',
            'total' => '81.22',
        );
        $this->object->setParams($params);

        $this->assertNull($this->object->getParam('nada'));
    }

    public function testGetParamMagicMethod(): void
    {
        $params = array(
            'face' => 'palm',
            'total' => '81.22',
        );
        $this->object->setParams($params);

        $this->assertEquals('palm', $this->object->face());
    }

    public function testRender(): void
    {
        $content = '<h1>Radical</h1>';
        $vars = array(
            'something' => 'awesome',
        );
        $output = $this->object->render($content, $vars);

        $this->assertEquals('<h1>Radical</h1>awesome', $output);
    }

    /**
     * testSetNavigationItemsArray
     *
     * @return void
     */
    public function testSetNavigationItemsArray(): void
    {
        $this->object->setNavigationItems(array('foobar'));

        // An empty array is acceptable
        $this->assertEquals(array('foobar'), $this->object->getNavigationItems());
    }

    public function testSetNavigationItems(): void
    {
        $collection = new Collection('t1', '/baseurl');

        $this->object->setNavigationItems($collection);
        $this->assertEquals($collection, $this->object->getNavigationItems());
    }

    public function testSetSubNavigationItems(): void
    {
        $collection = new Collection('t2', '/baseurl');

        $this->object->setSubNavigationItems($collection);
        $this->assertEquals($collection, $this->object->getSubNavigationItems());
    }

    public function testGetNavigationNoItems(): void
    {
        $output = $this->object->getNavigation();

        $this->assertEquals('', $output);
    }

    /**
     * testGetNavigationCollection
     *
     * @return void
     */
    public function testGetNavigationCollectionTemplateNotFound(): void
    {
        $collection = new Collection('t1', '/baseurl');

        $this->object->setNavigationItems($collection);
        $output = $this->object->getNavigation();

        $this->assertEquals('', $output);
    }

    public function testGetNavigation(): void
    {
        $collection = new Collection('t1', '/baseurl');

        $items = array();
        $items[] = new Item('text1', 'text1/');
        $items[] = new Item('text2', 'text2/');

        $collection->addItems($items);

        $this->object->setNavigationItems($collection);

        $this->assertEquals('/baseurl/text1//baseurl/text2/', $this->object->getNavigation());
    }

    public function testGetSubNavigationNoItems(): void
    {
        $output = $this->object->getSubNavigation();

        $this->assertEquals('', $output);
    }

    public function testGetSubNavigation(): void
    {
        $collection = new Collection('t1', '/baseurl');

        $items = array();
        $items[] = new Item('text1', 'text1/');
        $items[] = new Item('text2', 'text2/');

        $collection->addItems($items);

        $this->object->setSubNavigationItems($collection);

        $this->assertEquals('/baseurl/text1//baseurl/text2/', $this->object->getSubNavigation());
    }
}
