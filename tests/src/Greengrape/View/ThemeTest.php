<?php
/**
 * Theme test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests\View;

use Greengrape\Tests\BaseTestCase;
use Greengrape\Exception\NotFoundException;
use Greengrape\View\Theme;
use Greengrape\View\AssetManager;

/**
 * Theme Test
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class ThemeTest extends BaseTestCase
{
    /**
     * Setup before tests
     *
     * @return void
     */
    public function setUp(): void
    {
        mkdir(APP_PATH . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'faketheme');

        $this->object = new Theme('faketheme', '/baseUrl', APP_PATH . DIRECTORY_SEPARATOR . 'tests');
    }

    /**
     * Tear down after tests
     *
     * @return void
     */
    public function tearDown(): void
    {
        rmdir(APP_PATH . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'faketheme');
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
        $theme = new Theme();
    }

    /**
     * testConstructDefaultDir
     *
     * @return void
     */
    public function testConstructDefaultDir(): void
    {
        $this->expectException(NotFoundException::class);
        $theme = new Theme('foobar', '/baseUrl');
    }

    /**
     * testConstructCustomDir
     *
     * @return void
     */
    public function testConstructCustomDirNoExist(): void
    {
        $this->expectException(NotFoundException::class);
        $theme = new Theme('foobar', '/baseUrl', APP_PATH . DIRECTORY_SEPARATOR . 'tests');
    }

    public function testConstructCustomDirExist(): void
    {
        $themePath = APP_PATH . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'newfaketheme';
        mkdir($themePath);

        $theme = new Theme('newfaketheme', '/baseUrl', APP_PATH . DIRECTORY_SEPARATOR . 'tests');
        $this->assertTrue($theme instanceof Theme);

        rmdir($themePath);
    }

    public function testGetAssetManager(): void
    {
        $assetManager = $this->object->getAssetManager();

        $this->assertTrue($assetManager instanceof AssetManager);
    }

    public function testGetPathBlank(): void
    {
        $path = $this->object->getPath();

        $expected = APP_PATH . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'faketheme';
        $this->assertEquals($expected, $path);
    }

    public function testGetPathFile(): void
    {
        $path = $this->object->getPath('foobar');

        $expected = APP_PATH . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'faketheme'
            . DIRECTORY_SEPARATOR . 'foobar';

        $this->assertEquals($expected, $path);
    }

    public function testSetDefaultTitle(): void
    {
        $this->object->setDefaultTitle('wonky wonk');

        $this->assertEquals('wonky wonk', $this->object->getDefaultTitle());
    }

    public function testGetDefaultTitleWhenNotSet(): void
    {
        $this->assertEquals('[Greengrape]', $this->object->getDefaultTitle());
    }
}
