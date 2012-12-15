<?php
/**
 * Content class file
 *
 * @package Greengrape
 */

namespace Greengrape\View;

use Greengrape\MarkdownExtendedParser;
use Greengrape\Exception\NotFoundException;
use Greengrape\Exception\GreengrapeException;

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
     * View object
     *
     * @var \Greengrape\View
     */
    protected $_view;

    /**
     * Title
     *
     * @var string
     */
    protected $_title = '';

    /**
     * Metadata
     *
     * @var array
     */
    protected $_metadata = array();

    /**
     * Template filename
     *
     * @var string
     */
    protected $_defaultTemplateFile = 'main.html';

    /**
     * Constructor
     *
     * @param string $file The file with the content to load
     * @param Greengrape\View The view object
     * @return void
     */
    public function __construct($file = null, $view = null)
    {
        $this->setView($view);

        if (null !== $file && $file !== '') {
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
     * Set the view object
     *
     * @param \Greengrape\View $theme Theme object
     * @return \Greengrape\View\Content
     */
    public function setView($view)
    {
        $this->_view = $view;
        return $this;
    }

    /**
     * Get the view object
     *
     * @return \Greengrape\View
     */
    public function getView()
    {
        if (null === $this->_view) {
            throw new GreengrapeException('View not set.');
        }

        return $this->_view;
    }

    /**
     * Get the theme (from the view object)
     *
     * @return \Greengrape\View\Theme
     */
    public function getTheme()
    {
        return $this->getView()->getTheme();
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
            $this->setTemplateFile($this->_defaultTemplateFile);
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

        $fileContents = trim(file_get_contents($this->getFile()));

        $metadata = $this->readMetadata($fileContents);

        if (isset($metadata['title'])) {
            $this->setTitle($metadata['title']);
        }

        $this->setTemplateFile($metadata['template']);
        $this->setMetadata($metadata);
        $this->setContent($fileContents);
    }

    /**
     * Read the meta data from the contents
     *
     * @param string $contents Contents from content file
     * @return array Array of meta data
     */
    public function readMetadata(&$contents)
    {
        $defaults = array(
            'template' => $this->_defaultTemplateFile,
        );

        if (!preg_match('/^---\s*\v(.*)\v---\s*\v/s', $contents, $matches)) {
            return $defaults;
        }

        $pos = strlen($matches[0]);

        $contents = substr($contents, $pos);

        $metadata = parse_ini_string($matches[1]);

        return array_merge($defaults, $metadata);
    }

    /**
     * Set the meta data
     *
     * @param array $metadata
     * @return Greengrape\View\Content
     */
    public function setMetadata($metadata)
    {
        $this->_metadata = $metadata;
        return $this;
    }

    /**
     * Get metadata
     *
     * @param string $key A specific metadata item to return (optional)
     * @return array
     */
    public function getMetadata($key = null)
    {
        if (null === $key) {
            return $this->_metadata;
        }

        if (array_key_exists($key, $this->_metadata)) {
            return $this->_metadata[$key];
        }
    }

    /**
     * Set title
     *
     * @param string $title Title
     * @return \Greengrape\View\Content
     */
    public function setTitle($title)
    {
        $this->_title = $title;
        return $this;
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

        $markdownParser = new MarkdownExtendedParser();

        $content = $this->filterMarkdown($content);

        $htmlContent = $markdownParser->transformMarkdown($content);

        $vars = $this->getMetadata();

        // Handle any asides (partials)
        $asides = $this->getMetadata('aside');
        if (is_array($asides)) {
            unset($vars['aside']);
            $vars['aside'] = array();
            foreach ($asides as $aside) {
                $vars['aside'][$aside] = $this->getView()->renderPartial($aside);
            }
        }

        return $this->getTemplate()->render($htmlContent, $vars);
    }

    public function renderSubContent($file)
    {
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
            '[$1](' . $baseUrl . '$2)', // links inline
            '[$1]: ' . $baseUrl . '$2', // links referenced
            '![$1](' . $baseUrl . 'assets', // image inline
            '[$1]: ' . $baseUrl . 'assets', // images reference
        );

        $content = preg_replace($patterns, $replacements, $content);

        return $content;
    }
}
