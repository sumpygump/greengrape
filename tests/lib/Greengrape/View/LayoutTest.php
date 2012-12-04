<?php
/**
 * Layout test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests\View;

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
class LayoutTest extends \BaseTestCase
{
    /**
     * Setup before tests
     *
     * @return void
     */
    public function setUp()
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

        $this->_object = new Layout('layout.html', $theme);
    }

    /**
     * Tear down after tests
     *
     * @return void
     */
    public function tearDown()
    {
        passthru('rm -rf testtheme');
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

    public function testConstructDefault()
    {
        $theme = new Theme('testtheme', '/', APP_PATH . DIRECTORY_SEPARATOR . 'tests');
        $layout = new Layout('layout.html', $theme);

        $this->assertTrue($layout instanceof Layout);
    }

    public function testGetTitle()
    {
        $this->_object->setTitle('foobar2');

        $this->assertEquals('foobar2 | [testing]', $this->_object->getTitle());
    }

    public function testSetTitleReset()
    {
        $this->_object->setTitle('foobar2', true);

        $this->assertEquals('foobar2', $this->_object->getTitle());
    }

    public function testSetTitleBlank()
    {
        $this->_object->setTitle('');

        $this->assertEquals('[testing]', $this->_object->getTitle());
    }

    public function testSetTitleWhitespace()
    {
        $this->_object->setTitle('   ');

        $this->assertEquals('[testing]', $this->_object->getTitle());
    }

    public function testSetContent()
    {
        $this->_object->setContent('this is the content');

        $this->assertEquals('this is the content', $this->_object->getContent());
    }

    public function testSetParams()
    {
        $params = array(
            'face' => 'palm',
            'total' => '81.22',
        );
        $this->_object->setParams($params);

        $this->assertEquals('palm', $this->_object->getParam('face'));
    }

    public function testGetParamNotSet()
    {
        $params = array(
            'face' => 'palm',
            'total' => '81.22',
        );
        $this->_object->setParams($params);

        $this->assertNull($this->_object->getParam('nada'));
    }

    public function testGetParamMagicMethod()
    {
        $params = array(
            'face' => 'palm',
            'total' => '81.22',
        );
        $this->_object->setParams($params);

        $this->assertEquals('palm', $this->_object->face());
    }

    public function testRender()
    {
        $content = '<h1>Radical</h1>';
        $vars = array(
            'something' => 'awesome',
        );
        $output = $this->_object->render($content, $vars);

        $this->assertEquals('<h1>Radical</h1>awesome', $output);
    }

    /**
     * testSetNavigationItemsArray
     *
     * @return void
     */
    public function testSetNavigationItemsArray()
    {
        $this->_object->setNavigationItems(array('foobar'));

        // An empty array is acceptable
        $this->assertEquals(array('foobar'), $this->_object->getNavigationItems());
    }

    public function testSetNavigationItems()
    {
        $collection = new Collection('t1', '/baseurl');

        $this->_object->setNavigationItems($collection);
        $this->assertEquals($collection, $this->_object->getNavigationItems());
    }

    public function testSetSubNavigationItems()
    {
        $collection = new Collection('t2', '/baseurl');

        $this->_object->setSubNavigationItems($collection);
        $this->assertEquals($collection, $this->_object->getSubNavigationItems());
    }

    public function testGetNavigationNoItems()
    {
        $output = $this->_object->getNavigation();

        $this->assertEquals('', $output);
    }

    /**
     * testGetNavigationCollection
     *
     * @return void
     */
    public function testGetNavigationCollectionTemplateNotFound()
    {
        $collection = new Collection('t1', '/baseurl');

        $this->_object->setNavigationItems($collection);
        $output = $this->_object->getNavigation();

        $this->assertEquals('', $output);
    }

    public function testGetNavigation()
    {
        $collection = new Collection('t1', '/baseurl');

        $items = array();
        $items[] = new Item('text1', 'text1/');
        $items[] = new Item('text2', 'text2/');

        $collection->addItems($items);

        $this->_object->setNavigationItems($collection);

        $this->assertEquals('/baseurl/text1//baseurl/text2/', $this->_object->getNavigation());
    }

    public function testGetSubNavigationNoItems()
    {
        $output = $this->_object->getSubNavigation();

        $this->assertEquals('', $output);
    }

    public function testGetSubNavigation()
    {
        $collection = new Collection('t1', '/baseurl');

        $items = array();
        $items[] = new Item('text1', 'text1/');
        $items[] = new Item('text2', 'text2/');

        $collection->addItems($items);

        $this->_object->setSubNavigationItems($collection);

        $this->assertEquals('/baseurl/text1//baseurl/text2/', $this->_object->getSubNavigation());
    }
}
