<?php
/**
 * Greengrape Layout file
 *
 * @package Greengrape
 */

namespace Greengrape\View;

use Greengrape\Navigation\Collection;
use Greengrape\View\Template;
use \Twig\Environment as Twig_Environment;
use \Twig\Loader\ArrayLoader as Twig_Loader_ArrayLoader;
use \Twig\Extension\DebugExtension as Twig_DebugExtension;

/**
 * Layout
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 */
class Layout extends Template
{
    /**
     * The content of the page
     *
     * @var mixed
     */
    protected $content;

    /**
     * Title of page
     *
     * @var string
     */
    protected $title = '';

    /**
     * Navigation items
     *
     * @var array<string, string>
     */
    protected $navigationItems = [];

    /**
     * Sub navigation items
     *
     * @var array<string, string>
     */
    protected $subNavigationItems = [];

    /**
     * Params
     *
     * @var array<string, mixed>
     */
    protected $params = [];

    /**
     * Constructor
     *
     * @param string $file Layout file
     * @param \Greengrape\View\Theme $theme Theme object
     * @return void
     */
    public function __construct($file, $theme)
    {
        parent::__construct($file, $theme);

        $this->setTitle($this->getTheme()->getDefaultTitle());
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set title
     *
     * @param mixed $title New title (prepends)
     * @param bool $reset Whether to reset the title
     * @return \Greengrape\View\Layout
     */
    public function setTitle($title, $reset = false)
    {
        $title = trim($title);

        // Allow to completely reset the title
        if ($reset) {
            $this->title = $title;
            return $this;
        }

        // Don't add to the title if it was blank
        if ($title == '') {
            return $this;
        }

        if (trim($this->title) == '') {
            // If the current title is blank, we're replacing
            $this->title = $title;
        } else {
            // Otherwise prepend with separator
            $this->title = $title . ' | ' . $this->title;
        }

        return $this;
    }

    /**
     * Set main content
     *
     * @param string $content Content
     * @return \Greengrape\View\Layout
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Get the main content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set Params
     *
     * @param array<string, mixed> $params Params
     * @return Layout
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * Set an individual param
     *
     * @param string $paramName Param key
     * @param mixed $value Value of param to set
     * @return \Greengrape\View\Layout
     */
    public function setParam($paramName, $value)
    {
        $this->params[$paramName] = $value;
        return $this;
    }

    /**
     * Get a param by key name
     *
     * @param string $key Key name
     * @return mixed
     */
    public function getParam($key)
    {
        if (!isset($this->params[$key])) {
            return null;
        }

        return $this->params[$key];
    }

    /**
     * Magic call method to handle fetching from params
     *
     * @param string $method Name of method called
     * @param array<int, mixed> $args Arguments with invocation
     * @return mixed
     */
    public function __call($method, $args)
    {
        $key = $method;
        return $this->getParam($key);
    }

    /**
     * Render the content in layout
     *
     * @param string $content The main content area to be rendered
     * @param array<string, mixed> $vars Variables to pass to be rendered by the layout
     * @return string Rendered HTML
     */
    public function render($content, $vars = [])
    {
        $this->setContent($content);

        $loader = new Twig_Loader_ArrayLoader();
        $twig   = new Twig_Environment($loader, ['debug' => true]);
        $twig->addExtension(new Twig_DebugExtension());

        $twig->addGlobal('asset', $this->getAssetManager());
        $twig->addGlobal('layout', $this);

        $layoutContent = file_get_contents($this->getFile());

        $template = $twig->createTemplate($layoutContent);
        return $template->render($vars);
    }

    /**
     * Set navigation items
     *
     * @param Collection|array<string, string> $navigationItems Array of navigation items
     * @return Layout
     */
    public function setNavigationItems(Collection|array|null $navigationItems)
    {
        $this->navigationItems = $navigationItems;
        return $this;
    }

    /**
     * Get navigation items
     *
     * @return array<string, string>
     */
    public function getNavigationItems()
    {
        return $this->navigationItems;
    }

    /**
     * Set sub navigation items
     *
     * @param Collection|array<string, string> $navigationItems Array of navigation items
     * @return \Greengrape\View\Layout
     */
    public function setSubNavigationItems($navigationItems)
    {
        $this->subNavigationItems = $navigationItems;
        return $this;
    }

    /**
     * Get sub navigation items
     *
     * @return array<string, string>
     */
    public function getSubNavigationItems()
    {
        return $this->subNavigationItems;
    }

    /**
     * Get Navigation for rendering
     *
     * @return string
     */
    public function getNavigation()
    {
        if (0 == count($this->getNavigationItems())) {
            return '';
        }

        $templateFile = $this->getTheme()->getPath('templates/_navigation.html');
        $template = new Template($templateFile, $this->getTheme());

        $vars = [
            'navigation' => $this->getNavigationItems(),
        ];

        return $template->render('', $vars);
    }

    /**
     * Get subnavigation for rendering
     *
     * @return string
     */
    public function getSubnavigation()
    {
        if (0 == count($this->getSubNavigationItems())) {
            return '';
        }

        $templateFile = $this->getTheme()->getPath('templates/_subnavigation.html');
        $template = new Template($templateFile, $this->getTheme());

        $vars = [
            'navigation' => $this->getSubNavigationItems(),
        ];

        return $template->render('', $vars);
    }

    /**
     * Get include (Include another template file)
     *
     * @param string $filename Filename of tempalte file
     * @return string
     */
    public function getInclude($filename)
    {
        $templateFile = $this->getTheme()->getPath('templates/' . $filename);
        $template = new Template($templateFile, $this->getTheme());

        $vars = [
            'layout' => $this,
        ];

        return $template->render('', $vars);
    }
}
