<?php
/**
 * Content test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests\View;

use Greengrape\Tests\BaseTestCase;
use Greengrape\Exception\GreengrapeException;
use Greengrape\Exception\NotFoundException;
use Greengrape\View\Content;
use Greengrape\View\Theme;
use Greengrape\View;

/**
 * Content Test
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class ContentTest extends BaseTestCase
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
        $theme = new Theme('foobar', '/baseurl', $testThemesDir);

        $view = new View($theme);
        $view->setContentDir(realpath('.'));

        file_put_contents('mycontentfile.md', '#contents');
        $this->object = new Content('mycontentfile.md', $view);
    }

    /**
     * Tear down after tests
     *
     * @return void
     */
    public function tearDown(): void
    {
        passthru('rm mycontentfile.md');
        passthru('rm -rf foobar');
    }

    /**
     * Test constructor
     *
     * @return void
     */
    public function testConstruct(): void
    {
        $content = new Content();

        $this->assertEquals('', $content->getContent());
    }

    /**
     * testConstructFileNoExist
     *
     * @return void
     */
    public function testConstructFileNoExist(): void
    {
        $this->expectException(NotFoundException::class);
        $content = new Content('missing.md');
    }

    /**
     * testGetThemeWhenNotSet
     *
     * @return void
     */
    public function testGetThemeWhenNotSet(): void
    {
        $this->expectException(GreengrapeException::class);
        $content = new Content('mycontentfile.md');

        $theme = $content->getTheme();
    }

    public function testGetTemplate(): void
    {
        $this->object->setTemplate(null);

        $this->assertEquals(
            'main.html',
            basename($this->object->getTemplate()->getFile())
        );
    }

    public function testReadFile(): void
    {
        $this->object->readFile();

        $this->assertEquals('#contents', $this->object->getContent());
    }

    public function testGetTitle(): void
    {
        $this->object->setTitle('The new title');

        $this->assertEquals('The new title', $this->object->getTitle());
    }

    public function testRender(): void
    {
        $output = $this->object->render();

        $this->assertStringContainsString('<h1>contents</h1>', $output);
    }

    public function testFilterMarkdownLinks(): void
    {
        $content = "[test1](foobarnews) - [test2]: someplace";

        $reformatted = $this->object->filterMarkdown($content);

        $this->assertStringContainsString('[test1](/baseurl/foobarnews)', $reformatted);
        $this->assertStringContainsString('[test2]: /baseurl/someplace', $reformatted);
    }

    public function testFilterMarkdownImages(): void
    {
        $content = "![test3](assets/img/foobar.jpg) - [test4]: assets/img/fanbar.png";

        $reformatted = $this->object->filterMarkdown($content);

        $this->assertStringContainsString('![test3](/baseurl/assets/img/foobar.jpg)', $reformatted);
        $this->assertStringContainsString('[test4]: /baseurl/assets/img/fanbar.png', $reformatted);
    }
}
