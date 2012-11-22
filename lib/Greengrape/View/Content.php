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
    protected $_file = '';

    /**
     * The content (in markdown format)
     *
     * @var string
     */
    protected $_content = '';

    /**
     * Theme object
     *
     * @var \Greengrape\View\Theme
     */
    protected $_theme;

    /**
     * Constructor
     *
     * @param string $file The file with the content to load
     * @return void
     */
    public function __construct($file, $theme = null)
    {
        $this->setFile($file);
        $this->setTheme($theme);

        $this->readFile();
    }

    public function setFile($file)
    {
        $this->_file = $file;
        return $this;
    }

    public function getFile()
    {
        return $this->_file;
    }

    public function setTheme($theme)
    {
        $this->_theme = $theme;
        return $this;
    }

    public function getTheme()
    {
        return $this->_theme;
    }

    public function setTemplate($template)
    {
        $this->_template = $template;
        return $this;
    }

    public function getTemplate()
    {
        return $this->_template;
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

    public function readFile()
    {
        $fileContents = file_get_contents($this->getFile());

        $metadata = array(
            'template' => 'default.html',
        );

        $templateFile = $this->getTheme()->getPath('templates/' . $metadata['template']);

        if (!file_exists($templateFile)) {
            throw new \Exception("Template file not found: '$templateFile'");
        }

        $this->setTemplate(new Template($templateFile, $this->getTheme()));

        $this->setContent($fileContents);
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

        $htmlContent = $markdownParser->transformMarkdown($content);
        return $this->getTemplate()->render($htmlContent);
    }
}
