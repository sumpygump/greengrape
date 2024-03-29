<?php
/**
 * Content class file
 *
 * @package Greengrape
 */

namespace Greengrape\View;

use Michelf\MarkdownExtra;
use Greengrape\Exception\NotFoundException;
use Greengrape\Exception\GreengrapeException;
use Greengrape\Chronolog\Collection as EntryCollection;
use Greengrape\Navigation\Item as NavItem;
use Greengrape\View;

/**
 * Content
 *
 * This defines a content block in markdown
 * In the future we would have separate content classes for different formats,
 * but right now we only support markdown
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 */
class Content
{
    const TYPE_PAGE = 'page';
    const TYPE_ENTRY = 'entry';
    const TYPE_ENTRIES = 'entries';
    const TYPE_CHRONOLOG = 'chronolog';

    /**
     * File to load
     *
     * @var string
     */
    protected $file = '';

    /**
     * The content (in markdown format)
     *
     * @var string
     */
    protected $content = '';

    /**
     * View object
     *
     * @var \Greengrape\View
     */
    protected $view;

    /**
     * Title
     *
     * @var string
     */
    protected $title = '';

    /**
     * Metadata
     *
     * @var array<string, mixed>
     */
    protected $metadata = [];

    /**
     * Template filename
     *
     * @var string
     */
    protected $defaultTemplateFile = 'main.html';

    /**
     * Template
     *
     * @var Template
     */
    protected $template;

    /**
     * Constructor
     *
     * @param string $file The file with the content to load
     * @param View $view The view object
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
        $this->file = $file;
        return $this;
    }

    /**
     * Get the content file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set the view object
     *
     * @param View $view View object
     * @return Content
     */
    public function setView($view)
    {
        $this->view = $view;
        return $this;
    }

    /**
     * Get the view object
     *
     * @return \Greengrape\View
     */
    public function getView()
    {
        if (null === $this->view) {
            throw new GreengrapeException('View not set.');
        }

        return $this->view;
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
        $this->template = $template;
        return $this;
    }

    /**
     * Get the template object
     *
     * @return \Greengrape\View\Template
     */
    public function getTemplate()
    {
        if (null == $this->template) {
            $this->setTemplateFile($this->defaultTemplateFile);
        }

        return $this->template;
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
        try {
            $this->setTemplate(new Template($templateFile, $this->getTheme()));
        } catch (\Exception $e) {
            // TODO: accumulate errors that we are trying to recover from to
            // diagnose issues that are silently "corrected"
            $templateFile = $this->getTheme()->getPath('templates/default.html');
            $this->setTemplate(new Template($templateFile, $this->getTheme()));
        }

        return $this;
    }

    /**
     * Set the content in markdown format
     *
     * @param string $content The content
     * @return Content
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Get the content in markdown format
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Get blurb content
     *
     * @return string Transformed content
     */
    public function getBlurb()
    {
        $content = $this->getContent();
        $content = strip_tags($content);

        $content = wordwrap($content);

        // Replace multiple blank lines with just one blank line
        $content = preg_replace('/\r?\n(\s*\r?\n){2,}/', "\n\n", $content);
        $content = explode("\n", $content);

        $content = implode("\n", array_slice($content, 0, 5));

        return $this->transform(rtrim($content) . '...');
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

        if (is_dir($this->getFile())) {
            $this->setMetadata(['title' => $this->getFile()]);
            $this->setContent('');
            return;
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
     * @return array<string, string> Array of meta data
     */
    public function readMetadata(&$contents)
    {
        $defaults = [
            'template' => $this->defaultTemplateFile,
            'type' => self::TYPE_PAGE,
        ];

        // The U modifier makes the regex ungreedy so it will only capture the
        // first front-matter that appears in the file
        if (!preg_match('/^---\s*\v(.*)\v---(?:$|\s|\s\v)/sU', $contents, $matches)) {
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
     * @param array<string, mixed> $metadata
     * @return Content
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * Get metadata
     *
     * @param string $key A specific metadata item to return (optional)
     * @param mixed $default Default value to return if not exists
     * @return array<string, mixed>|string
     */
    public function getMetadata($key = null, $default = null)
    {
        if (null === $key) {
            return $this->metadata;
        }

        if (array_key_exists($key, $this->metadata)) {
            return $this->metadata[$key];
        }

        return $default;
    }

    /**
     * Set title
     *
     * @param string $title Title
     * @return \Greengrape\View\Content
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get the name of this content, which is the filename sans extension
     *
     * @return string
     */
    public function getName()
    {
        $path = $this->getFile();
        $name = pathinfo($path, PATHINFO_FILENAME);

        return $name;
    }

    /**
     * Get the title (if any was set)
     *
     * @return string
     */
    public function getTitle()
    {
        if (!$this->title) {
            return $this->getName();
        }

        return $this->title;
    }

    /**
     * Get URL for this content
     *
     * @return string
     */
    public function getUrl()
    {
        $path = $this->getFile();
        $name = $this->getName();

        // The base URL tells us the web root of the application
        $baseUrl = $this->getTheme()->getAssetManager()->getBaseUrl();

        // The content dir gives the full path to the directory of this
        // collection.
        $contentDir = $this->getView()->getContentDir();

        // Strip off the content dir from the full path so we're left with the
        // relative web root of the file
        $webPath = dirname(str_replace($contentDir . DIRECTORY_SEPARATOR, '', $path));
        $webPath = NavItem::translateOrderedName($webPath);

        // The url is the baseUrl + '/' + webPath + '/' + name of object
        $url = rtrim($baseUrl, '/') . '/' . $webPath . '/' . $name;

        return $url;
    }

    /**
     * Render the content
     *
     * This parses the content via markdown
     *
     * @param string $content Optional content to render instead
     * @param array<string, mixed> $params Context params to pass to view engine
     * @return string Rendered HTML (via markdown)
     */
    public function render($content = null, $params = [])
    {
        if ($content === null) {
            $content = $this->getContent();
        }

        $pageType = $this->getMetadata('type');

        $htmlContent = $this->transform($content);
        $vars = $params;

        $metadata = $this->getMetadata();
        if (!is_string($metadata)) {
            $vars = $metadata + $params;
        }

        // Chronolog page type is a listing of entries
        if ($pageType == self::TYPE_CHRONOLOG
            || $pageType == self::TYPE_ENTRIES
            || $pageType == self::TYPE_ENTRY
        ) {
            $root = dirname($this->file);
            if (is_string($this->getMetadata('entriesroot'))) {
                $root = $root . '/' . $this->getMetadata('entriesroot');
            }
            $entries = new EntryCollection($root, $this->getView());

            if ($this->getMetadata('direction', 'desc') == 'desc') {
                $entries->reverse();
            }

            $vars['entries'] = $entries;
        }

        // Handle any asides (partials)
        $asides = $this->getMetadata('aside');
        if (is_array($asides)) {
            unset($vars['aside']);
            $vars['aside'] = [];
            foreach ($asides as $aside) {
                $vars['aside'][$aside] = $this->getView()->renderPartial($aside);
            }
        }

        return $this->getTemplate()->render($htmlContent, $vars);
    }

    /**
     * Transform (using markdown engine)
     *
     * @param string $content Content
     * @return string
     */
    public function transform($content)
    {
        $content = $this->filterMarkdown($content);
        $htmlContent = MarkdownExtra::defaultTransform($content);

        return $htmlContent;
    }

    /**
     * Run any filters required before the markdown content is parsed
     *
     * Here is a list of replacements that occur in order:
     *
     * 1. Change links to include the baseUrl
     *    [zzz](blah...) becomes [zzz](/baseUrl/blah...)
     *    But not links that start with 'http' or '#'
     *
     * 2. Change links referenced later to include the baseUrl
     *    [zzz]: pageref... becomes [zzz]: /baseurl/pageref...
     *    But not links that start with 'http' or '#'
     *
     * 3. Change image insertions to include the baseUrl
     *    ![zzz](assets/img/foo.jpg) becomes ![zzz](/baseurl/assets/img/foo.jpg)
     *
     * 4. Same as previous but for referenced images
     *    [zzz]: assets/... becomes [zzz]: /baseurl/assets...
     *
     * @param mixed $content
     * @return string
     */
    public function filterMarkdown($content)
    {
        $baseUrl = $this->getTheme()->getAssetManager()->getBaseUrl();

        $patterns = [
            '/\[(.*)\]\(((?!http|#)[^\)]+)\)/', // links inline
            '/\[((?!\^)[^\]]+)\]\W*:\W*((?!http|#)[^\W]+)/', // links referenced
            '/!\[(.*)\]\(assets/', // images inline
            '/\[(.*)\]\W*:\W*assets/', // images reference
        ];

        $replacements = [
            '[$1](' . $baseUrl . '$2)', // links inline
            '[$1]: ' . $baseUrl . '$2', // links referenced
            '![$1](' . $baseUrl . 'assets', // image inline
            '[$1]: ' . $baseUrl . 'assets', // images reference
        ];

        $content = preg_replace($patterns, $replacements, $content);

        return $content;
    }
}
