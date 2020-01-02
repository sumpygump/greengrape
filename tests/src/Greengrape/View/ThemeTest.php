<?php
/**
 * Theme test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests\View;

use Greengrape\View\Theme;
use Greengrape\View\AssetManager;

/**
 * Theme Test
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class ThemeTest extends \BaseTestCase
{
    /**
     * Setup before tests
     *
     * @return void
     */
    public function setUp()
    {
        mkdir(APP_PATH . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'faketheme');

        $this->_object = new Theme('faketheme', '/baseUrl', APP_PATH . DIRECTORY_SEPARATOR . 'tests');
    }

    /**
     * Tear down after tests
     *
     * @return void
     */
    public function tearDown()
    {
        rmdir(APP_PATH . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'faketheme');
    }

    /**
     * Test constructor
     *
     * @expectedException ArgumentCountError
     * @return void
     */
    public function testConstructNoArgs()
    {
        $theme = new Theme();
    }

    /**
     * testConstructDefaultDir
     *
     * @expectedException Greengrape\Exception\NotFoundException
     * @return void
     */
    public function testConstructDefaultDir()
    {
        $theme = new Theme('foobar', '/baseUrl');
    }

    /**
     * testConstructCustomDir
     *
     * @expectedException Greengrape\Exception\NotFoundException
     * @return void
     */
    public function testConstructCustomDirNoExist()
    {
        $theme = new Theme('foobar', '/baseUrl', APP_PATH . DIRECTORY_SEPARATOR . 'tests');
    }

    public function testConstructCustomDirExist()
    {
        $themePath = APP_PATH . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'newfaketheme';
        mkdir($themePath);

        $theme = new Theme('newfaketheme', '/baseUrl', APP_PATH . DIRECTORY_SEPARATOR . 'tests');
        $this->assertTrue($theme instanceof Theme);

        rmdir($themePath);
    }

    public function testGetAssetManager()
    {
        $assetManager = $this->_object->getAssetManager();

        $this->assertTrue($assetManager instanceof AssetManager);
    }

    public function testGetPathBlank()
    {
        $path = $this->_object->getPath();

        $expected = APP_PATH . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'faketheme';
        $this->assertEquals($expected, $path);
    }

    public function testGetPathFile()
    {
        $path = $this->_object->getPath('foobar');

        $expected = APP_PATH . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'faketheme'
            . DIRECTORY_SEPARATOR . 'foobar';

        $this->assertEquals($expected, $path);
    }

    public function testSetDefaultTitle()
    {
        $this->_object->setDefaultTitle('wonky wonk');

        $this->assertEquals('wonky wonk', $this->_object->getDefaultTitle());
    }

    public function testGetDefaultTitleWhenNotSet()
    {
        $this->assertEquals('[Greengrape]', $this->_object->getDefaultTitle());
    }
}
