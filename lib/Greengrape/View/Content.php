<?php
/**
 * Content class file
 *
 * @package Greengrape
 */

namespace Greengrape\View;

use dflydev\markdown\MarkdownParser;

/**
 * Content
 *
 * This defines a content block in markdown
 * In the future we would have separate content classes for different formats, 
 * but right now we only support markdown
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class Content
{
    /**
     * The content (in markdown format)
     *
     * @var string
     */
    protected $_content = '';

    /**
     * Constructor
     *
     * @param string $content The content
     * @return void
     */
    public function __construct($content)
    {
        $this->setContent($content);
    }

    /**
     * Set the content in markdown format
     *
     * @param string $content The content
     * @return \Greengrape\View\Content
     */
    public function setContent($content)
    {
        $this->_content = $content;
    }

    /**
     * Get the content in markdown format
     *
     * @return string
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * Render the content
     *
     * This parses the content via markdown
     *
     * @param string $content Optional content
     * @return string
     */
    public function render($content = null)
    {
        if ($content === null) {
            $content = $this->getContent();
        }

        $markdownParser = new MarkdownParser();

        return $markdownParser->transformMarkdown($content);
    }
}
