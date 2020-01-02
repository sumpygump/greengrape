<?php
/**
 * Template test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests\View;

use Greengrape\View\Template;
use Greengrape\View\Theme;
use Greengrape\View\AssetManager;

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
        mkdir('foobar');
        mkdir('foobar' . DIRECTORY_SEPARATOR . 'templates');
        file_put_contents('foobar' . DIRECTORY_SEPARATOR . 'layout.html', '{{ layout.content|raw }}');
        file_put_contents('foobar' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'default.html', '{{ content | raw }}');

        file_put_contents('template1.html', 'T{{ content }}');

        $testThemesDir = APP_PATH . DIRECTORY_SEPARATOR . 'tests';
        $theme = new Theme('foobar', '/baseurl', $testThemesDir);

        $this->_object = new Template('template1.html', $theme);
    }

    /**
     * Tear down after tests
     *
     * @return void
     */
    public function tearDown()
    {
        passthru('rm template1.html');
        passthru('rm -rf foobar');
    }

    /**
     * Test constructor
     *
     * @expectedException ArgumentCountError
     * @return void
     */
    public function testConstructNoArgs()
    {
        $template = new Template();
    }

    public function testConstructDefault()
    {
        $testThemesDir = APP_PATH . DIRECTORY_SEPARATOR . 'tests';
        $theme = new Theme('foobar', '/baseurl', $testThemesDir);

        $template = new Template('template1.html', $theme);
        $this->assertTrue($template instanceof Template);
    }

    /**
     * testSetFileNoExist
     *
     * @expectedException Greengrape\Exception\NotFoundException
     * @return void
     */
    public function testSetFileNoExist()
    {
        $this->_object->setFile('fakefile.html');
    }

    public function testGetFile()
    {
        $this->_object->setFile('template1.html');

        $this->assertEquals('template1.html', $this->_object->getFile());
    }

    public function testGetTheme()
    {
        $theme = $this->_object->getTheme();

        $this->assertTrue($theme instanceof Theme);
        $this->assertEquals('foobar', $theme->getName());
    }

    public function testGetAssetManager()
    {
        $assetManager = $this->_object->getAssetManager();

        $this->assertTrue($assetManager instanceof AssetManager);
        $this->assertEquals('/baseurl/', $assetManager->getBaseUrl());
    }

    public function testRender()
    {
        $content = 'My goodness';

        $result = $this->_object->render($content);
        $this->assertEquals('TMy goodness', $result);
    }
}
