<?php
/**
 * Content test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests\View;

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
class ContentTest extends \BaseTestCase
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
        file_put_contents('foobar' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'main.html', '{{ content | raw }}');

        $testThemesDir = APP_PATH . DIRECTORY_SEPARATOR . 'tests';
        $theme = new Theme('foobar', '/baseurl', $testThemesDir);

        $view = new View($theme);
        $view->setContentDir(realpath('.'));

        file_put_contents('mycontentfile.md', '#contents');
        $this->_object = new Content('mycontentfile.md', $view);
    }

    /**
     * Tear down after tests
     *
     * @return void
     */
    public function tearDown()
    {
        passthru('rm mycontentfile.md');
        passthru('rm -rf foobar');
    }

    /**
     * Test constructor
     *
     * @return void
     */
    public function testConstruct()
    {
        $content = new Content();

        $this->assertEquals('', $content->getContent());
    }

    /**
     * testConstructFileNoExist
     *
     * @expectedException Greengrape\Exception\NotFoundException
     * @return void
     */
    public function testConstructFileNoExist()
    {
        $content = new Content('missing.md');
    }

    /**
     * testGetThemeWhenNotSet
     *
     * @expectedException Greengrape\Exception\GreengrapeException
     * @return void
     */
    public function testGetThemeWhenNotSet()
    {
        $content = new Content('mycontentfile.md');

        $theme = $content->getTheme();
    }

    public function testGetTemplate()
    {
        $this->_object->setTemplate(null);

        $this->assertEquals(
            'main.html', basename($this->_object->getTemplate()->getFile())
        );
    }

    public function testReadFile()
    {
        $this->_object->readFile();

        $this->assertEquals('#contents', $this->_object->getContent());
    }

    public function testGetTitle()
    {
        $this->_object->setTitle('The new title');

        $this->assertEquals('The new title', $this->_object->getTitle());
    }

    public function testRender()
    {
        $output = $this->_object->render();

        $this->assertContains('<h1>contents</h1>', $output);
    }

    public function testFilterMarkdownLinks()
    {
        $content = "[test1](foobarnews) - [test2]: someplace";

        $reformatted = $this->_object->filterMarkdown($content);

        $this->assertContains('[test1](/baseurl/foobarnews)', $reformatted);
        $this->assertContains('[test2]: /baseurl/someplace', $reformatted);
    }

    public function testFilterMarkdownImages()
    {
        $content = "![test3](assets/img/foobar.jpg) - [test4]: assets/img/fanbar.png";

        $reformatted = $this->_object->filterMarkdown($content);

        $this->assertContains('![test3](/baseurl/assets/img/foobar.jpg)', $reformatted);
        $this->assertContains('[test4]: /baseurl/assets/img/fanbar.png', $reformatted);
    }
}
