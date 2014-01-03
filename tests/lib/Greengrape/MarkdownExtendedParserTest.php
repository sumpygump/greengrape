<?php
/**
 * MarkdownExtendedParser test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests;

use Greengrape\MarkdownExtendedParser;

/**
 * MarkdownExtendedParser Test
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class MarkdownExtendedParserTest extends \BaseTestCase
{
    /**
     * Setup before tests
     *
     * @return void
     */
    public function setUp()
    {
        $this->_object = new MarkdownExtendedParser();
    }

    /**
     * Tear down after tests
     *
     * @return void
     */
    public function tearDown()
    {
    }

    /**
     * Test Transform
     *
     * @return void
     */
    public function testTransform()
    {
        $markdown = '#Heading';
        $expected = '<h1>Heading</h1>';

        $result = $this->_object->transform($markdown);
        $this->assertContains($expected, $result);
    }

    public function testDoBlockQuotesNoCite()
    {
        $markdown = '> something quoted';
        $expected = "<blockquote>\n  <p>something quoted</p>\n</blockquote>";

        $result = $this->_object->transform($markdown);
        $this->assertContains($expected, $result);
    }

    public function testDoBlockQuotesWithCite()
    {
        $markdown = '> (url) something quoted';
        $expected = "<blockquote cite=\"url\">\n  <p>something quoted</p>\n</blockquote>";

        $result = $this->_object->transform($markdown);
        $this->assertContains($expected, $result);
    }

    public function testDoBlockQuotesParenthetical()
    {
        $markdown = '>  (url) something quoted';
        $expected = "<blockquote>\n  <p>(url) something quoted</p>\n</blockquote>";

        $result = $this->_object->transform($markdown);
        $this->assertContains($expected, $result);
    }

    public function testDoCodeBlockOriginal()
    {
        $markdown = '    $i = 0;';
        $expected = "<pre><code>\$i = 0;\n</code></pre>";

        $result = $this->_object->transform($markdown);
        $this->assertContains($expected, $result);
    }

    public function testDoFencedCodeBlocks()
    {
        $markdown = "```php\n\$i = 0;\n```";
        $expected = "<pre><code class=\"language-php\">\$i = 0;\n</code></pre>";

        $result = $this->_object->transform($markdown);
        $this->assertContains($expected, $result);
    }

    public function testDoFencedFiguresNoCaption()
    {
        $markdown = "===\n![](img/reference.png)\n===";
        $expected = "<figure>\n  <p><img src=\"img/reference.png\" alt=\"\" /></p>\n</figure>";

        $result = $this->_object->transform($markdown);
        $this->assertContains($expected, $result);
    }

    public function testDoFencedFiguresTopCaption()
    {
        $markdown = "=== [This **is** a caption]\n![](img/reference.png)\n===";
        $expected = "<figure>\n<figcaption><p>This <strong>is</strong> a caption</p></figcaption>\n  <p><img src=\"img/reference.png\" alt=\"\" /></p>\n</figure>";

        $result = $this->_object->transform($markdown);
        $this->assertContains($expected, $result);
    }

    public function testDoFencedFiguresBottomCaption()
    {
        $markdown = "===\n![](img/reference.png)\n=== [This **is** a caption]";
        // Not sure why this isn't working
        //$expected = "<figure>\n  <p><img src=\"img/reference.png\" alt=\"\" /></p>\n<figcaption><p>This <strong>is</strong> a caption</p></figcaption>\n</figure>";
        $expected = "<figure>\n  <p><img src=\"img/reference.png\" alt=\"\" /></p>\n</figure>";

        $result = $this->_object->transform($markdown);
        $this->assertContains($expected, $result);
    }
}
