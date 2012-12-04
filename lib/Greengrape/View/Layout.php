<?php
/**
 * Greengrape Layout file
 *
 * @package Greengrape
 */

namespace Greengrape\View;

use Greengrape\View\Template;
use Greengrape\View\Content;
use Greengrape\Navigation\Collection;
use Greengrape\Navigation\Item;
use \Twig_Environment;
use \Twig_Loader_String;

/**
 * Layout
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class Layout extends Template
{
    /**
     * The content of the page
     *
     * @var mixed
     */
    protected $_content;

    /**
     * Title of page
     *
     * @var string
     */
    protected $_title = '';

    /**
     * Navigation items
     *
     * @var array
     */
    protected $_navigationItems = array();

    /**
     * Sub navigation items
     *
     * @var array
     */
    protected $_subNavigationItems = array();

    /**
     * Params
     *
     * @var array
     */
    protected $_params = array();

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
        return $this->_title;
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
            $this->_title = $title;
            return $this;
        }

        // Don't add to the title if it was blank
        if ($title == '') {
            return $this;
        }

        if (trim($this->_title) == '') {
            // If the current title is blank, we're replacing
            $this->_title = $title;
        } else {
            // Otherwise prepend with separator
            $this->_title = $title . ' | ' . $this->_title;
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
        $this->_content = $content;
        return $this;
    }

    /**
     * Get the main content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->_content;
    }

    /**
     * Set Params
     *
     * @param array $params Params
     * @return \Greengrape\View\Layout
     */
    public function setParams($params)
    {
        $this->_params = $params;
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
        if (!isset($this->_params[$key])) {
            return null;
        }

        return $this->_params[$key];
    }

    /**
     * Magic call method to handle fetching from params
     *
     * @param string $method Name of method called
     * @param array $args Arguments with invocation
     * @return void
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
     * @param array $vars Variables to pass to be rendered by the layout
     * @return string Rendered HTML
     */
    public function render($content, $vars = array())
    {
        $this->setContent($content);

        $loader = new Twig_Loader_String();
        $twig   = new Twig_Environment($loader);

        $twig->addGlobal('asset', $this->getAssetManager());
        $twig->addGlobal('layout', $this);

        $layoutContent = file_get_contents($this->getFile());

        return $twig->render($layoutContent, $vars);
    }

    /**
     * Set navigation items
     *
     * @param Greengrape\Navigation\Collection $navigationItems Array of navigation items
     * @return \Greengrape\View\Layout
     */
    public function setNavigationItems($navigationItems)
    {
        $this->_navigationItems = $navigationItems;
        return $this;
    }

    /**
     * Get navigation items
     *
     * @return array
     */
    public function getNavigationItems()
    {
        return $this->_navigationItems;
    }

    /**
     * Set sub navigation items
     *
     * @param array $navigationItems Array of navigation items
     * @return \Greengrape\View\Layout
     */
    public function setSubNavigationItems($navigationItems)
    {
        $this->_subNavigationItems = $navigationItems;
        return $this;
    }

    /**
     * Get sub navigation items
     *
     * @return array
     */
    public function getSubNavigationItems()
    {
        return $this->_subNavigationItems;
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

        $vars = array(
            'navigation' => $this->getNavigationItems(),
        );

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

        $vars = array(
            'navigation' => $this->getSubNavigationItems(),
        );

        return $template->render('', $vars);
    }
}
