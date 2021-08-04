<?php
/**
 * Template test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests\View;

use Greengrape\Tests\BaseTestCase;
use Greengrape\Exception\NotFoundException;
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
class TemplateTest extends BaseTestCase
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
            'foobar' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'default.html',
            '{{ content | raw }}'
        );

        file_put_contents('template1.html', 'T{{ content }}');

        $testThemesDir = APP_PATH . DIRECTORY_SEPARATOR . 'tests';
        $theme = new Theme('foobar', '/baseurl', $testThemesDir);

        $this->object = new Template('template1.html', $theme);
    }

    /**
     * Tear down after tests
     *
     * @return void
     */
    public function tearDown(): void
    {
        passthru('rm template1.html');
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
        $template = new Template();
    }

    public function testConstructDefault(): void
    {
        $testThemesDir = APP_PATH . DIRECTORY_SEPARATOR . 'tests';
        $theme = new Theme('foobar', '/baseurl', $testThemesDir);

        $template = new Template('template1.html', $theme);
        $this->assertTrue($template instanceof Template);
    }

    /**
     * testSetFileNoExist
     *
     * @return void
     */
    public function testSetFileNoExist(): void
    {
        $this->expectException(NotFoundException::class);
        $this->object->setFile('fakefile.html');
    }

    public function testGetFile(): void
    {
        $this->object->setFile('template1.html');

        $this->assertEquals('template1.html', $this->object->getFile());
    }

    public function testGetTheme(): void
    {
        $theme = $this->object->getTheme();

        $this->assertTrue($theme instanceof Theme);
        $this->assertEquals('foobar', $theme->getName());
    }

    public function testGetAssetManager(): void
    {
        $assetManager = $this->object->getAssetManager();

        $this->assertTrue($assetManager instanceof AssetManager);
        $this->assertEquals('/baseurl/', $assetManager->getBaseUrl());
    }

    public function testRender(): void
    {
        $content = 'My goodness';

        $result = $this->object->render($content);
        $this->assertEquals('TMy goodness', $result);
    }
}
