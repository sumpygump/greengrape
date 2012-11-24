<?php
/**
 * Content class file
 *
 * @package Greengrape
 */

namespace Greengrape\View;

use dflydev\markdown\MarkdownParser;
use Greengrape\Exception\NotFoundException;

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
     * File to load
     *
     * @var string
     */
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
     * Title
     *
     * @var string
     */
    protected $_title = '';

    /**
     * Constructor
     *
     * @param string $file The file with the content to load
     * @return void
     */
    public function __construct($file = '', $theme = null)
    {
        $this->setTheme($theme);

        if ($file != '') {
            $this->setFile($file);
            $this->readFile();
        }
    }

    /**
     * Set the content file
     *
     * @param string $file Filename
     * @return \Greengrape\View\Content
     */
    public function setFile($file)
    {
        $this->_file = $file;
        return $this;
    }

    /**
     * Get the content file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->_file;
    }

    /**
     * Set the theme object
     *
     * @param \Greengrape\View\Theme $theme Theme object
     * @return \Greengrape\View\Content
     */
    public function setTheme($theme)
    {
        $this->_theme = $theme;
        return $this;
    }

    /**
     * Get the theme object
     *
     * @return \Greengrape\View\Theme
     */
    public function getTheme()
    {
        return $this->_theme;
    }

    /**
     * Set the template object
     *
     * @param \Greengrape\View\Template $template Template for this content
     * @return \Greengrape\View\Content
     */
    public function setTemplate($template)
    {
        $this->_template = $template;
        return $this;
    }

    /**
     * Get the template object
     *
     * @return \Greengrape\View\Template
     */
    public function getTemplate()
    {
        if (null == $this->_template) {
            $this->setTemplateFile('default.html');
        }

        return $this->_template;
    }

    /**
     * Set template file
     *
     * This will set a new template object with the given template file
     *
     * @param string $file Filename
     * @return \Greengrape\View\Content
     */
    public function setTemplateFile($file)
    {
        $templateFile = $this->getTheme()->getPath('templates/' . $file);
        $this->setTemplate(new Template($templateFile, $this->getTheme()));

        return $this;
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
     * Read the content file
     *
     * This will retrieve the content from the file
     *
     * In the future this will extract metadata from the file as well
     *
     * @return void
     */
    public function readFile()
    {
        if (!file_exists($this->getFile())) {
            throw new NotFoundException("Content file not found: '" . $this->getFile() . "'");
        }

        $fileContents = file_get_contents($this->getFile());

        $metadata = array(
            'template' => 'default.html',
        );

        $this->setTemplateFile($metadata['template']);
        $this->setContent($fileContents);
    }

    /**
     * Get the title (if any was set)
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Render the content
     *
     * This parses the content via markdown
     *
     * @param string $content Optional content to render instead
     * @return string Rendered HTML (via markdown)
     */
    public function render($content = null)
    {
        if ($content === null) {
            $content = $this->getContent();
        }

        $markdownParser = new MarkdownParser();

        $content = $this->filterMarkdown($content);

        $htmlContent = $markdownParser->transformMarkdown($content);
        return $this->getTemplate()->render($htmlContent);
    }

    /**
     * Run any filters required before the markdown content is parsed
     *
     * Here is a list of replacements that occur in order:
     *
     * 1. Change links to include the baseUrl
     *    [zzz](blah...) becomes [zzz](/baseUrl/blah...)
     *    But not links that start with 'http'
     *
     * 2. Change links referenced later to include the baseUrl
     *    [zzz]: pageref... becomes [zzz]: /baseurl/pageref...
     *    But not links that start with 'http'
     *
     * 3. Change image insertions to include the baseUrl
     *    ![zzz](assets/img/foo.jpg) becomes ![zzz](/baseurl/assets/img/foo.jpg)
     *
     * 4. Same as previous but for referenced images
     *    [zzz]: assets/... becomes [zzz]: /baseurl/assets...
     *
     * @param mixed $content
     * @return void
     */
    public function filterMarkdown($content)
    {
        $baseUrl = $this->getTheme()->getAssetManager()->getBaseUrl();

        $patterns = array(
            '/\[(.*)\]\(((?!http)[^\)]+)\)/', // links inline
            '/\[(.*)\]\W*:\W*((?!http)[^\W]+)/', // links referenced
            '/!\[(.*)\]\(assets/', // images inline
            '/\[(.*)\]\W*:\W*assets/', // images reference
        );

        $replacements = array(
            '[$1](' . $baseUrl . '/$2)', // links inline
            '[$1]: ' . $baseUrl . '/$2', // links referenced
            '![$1](' . $baseUrl . '/assets', // image inline
            '[$1]: ' . $baseUrl . '/assets', // images reference
        );

        $content = preg_replace($patterns, $replacements, $content);

        return $content;
    }
}
