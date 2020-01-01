<?php
/**
 * Greengrame view class file
 *
 * @package Greengrape
 */

namespace Greengrape;

use Greengrape\View\Theme;
use Greengrape\View\Content;
use Greengrape\View\ContentPartial;
use Greengrape\View\Layout;
use Greengrape\Exception\NotFoundException;
use Greengrape\Navigation\Collection;
use Greengrape\Navigation\Item;

/**
 * View
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class View
{
    /**
     * Theme object
     *
     * @var \Greengrape\View\Theme
     */
    protected $_theme;

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
     * Active navigation item
     *
     * @var \Greengrape\NavigationItem
     */
    protected $_activeNavigationItem;

    /**
     * Active sub navigation item
     *
     * @var mixed
     */
    protected $_activeSubNavigationItem;

    /**
     * View params that should be passed to the layout
     *
     * @var array
     */
    protected $_params = array();

    /**
     * Content dir
     *
     * @var string
     */
    protected $_contentDir = '';

    /**
     * Constructor
     *
     * @param string $themePath Base of theme path
     * @return void
     */
    public function __construct($theme)
    {
        $this->setTheme($theme);
    }

    /**
     * Set theme object
     *
     * @param \Greengrape\View\Theme $theme Theme
     * @return \Greengrape\View
     */
    public function setTheme(Theme $theme)
    {
        $this->_theme = $theme;
        return $this;
    }

    /**
     * Get theme object
     *
     * @return \Greengrape\View\Theme
     */
    public function getTheme()
    {
        return $this->_theme;
    }

    /**
     * Get layout
     *
     * @return \Greengrape\View\Layout
     */
    public function getLayout()
    {
        $layoutFile = $this->getTheme()->getPath('layout.html');

        if (!file_exists($layoutFile)) {
            throw new NotFoundException("Layout file not found: '$layoutFile'");
        }

        $layout = new Layout($layoutFile, $this->getTheme());
        $layout->setNavigationItems($this->getNavigationItems());
        $layout->setSubNavigationItems($this->getSubNavigationItems());
        $layout->setParams($this->getParams());

        return $layout;
    }

    /**
     * Set content dir (The root of where all the content files are)
     *
     * @param string $dir Directory
     * @return \Greengrape\View
     */
    public function setContentDir($dir)
    {
        $this->_contentDir = $dir;
        return $this;
    }

    /**
     * Get the content directory
     *
     * @return string
     */
    public function getContentDir()
    {
        if ($this->_contentDir == '') {
            return APP_PATH . DIRECTORY_SEPARATOR . 'content';
        }

        return $this->_contentDir;
    }

    /**
     * Get params
     *
     * @param array $params Params
     * @return \Greengrape\View
     */
    public function setParams($params)
    {
        $this->_params = $params;
        return $this;
    }

    /**
     * Get params
     *
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * Set main navigation items
     *
     * @param Greengrape\Navigation\Collection $navigationItems Navigation items
     * @return \Greengrape\View
     */
    public function setNavigationItems(Collection $navigationItems)
    {
        $this->_navigationItems = $navigationItems;
        return $this;
    }

    /**
     * Get Main navigation items
     *
     * @return Greengrape\Navigation\Collection
     */
    public function getNavigationItems()
    {
        return $this->_navigationItems;
    }

    /**
     * Set active navigation item
     *
     * From here we'll be able to pull information about the current main level 
     * navigation
     *
     * @param \Greengrape\Navigation\Item $item Item
     * @return \Greengrape\View
     */
    public function setActiveNavigationItem(Item $item)
    {
        $this->_activeNavigationItem = $item;
        return $this;
    }

    /**
     * Get active navigation item (if any was set)
     *
     * @return \Greengrape\Navigation\Item
     */
    public function getActiveNavigationItem()
    {
        return $this->_activeNavigationItem;
    }

    /**
     * Set the active subnavigation item
     *
     * @param \Greengrape\Navigation\Item $item Navigation item
     * @return \Greengrape\View
     */
    public function setActiveSubNavigationItem(Item $item)
    {
        $this->_activeSubNavigationItem = $item;
        return $this;
    }

    /**
     * Get active subnavigation item
     *
     * @return \Greengrape\Navigation\Item
     */
    public function getActiveSubNavigationItem()
    {
        return $this->_activeSubNavigationItem;
    }

    /**
     * Set sub navigation items
     *
     * @param Greengrape\Navigation\Collection $items Items
     * @return \Greengrape\View
     */
    public function setSubNavigationItems(Collection $items)
    {
        $this->_subNavigationItems = $items;
        return $this;
    }

    /**
     * Get subnavigation items
     *
     * @return Greengrape\Navigation\Collection
     */
    public function getSubNavigationItems()
    {
        return $this->_subNavigationItems;
    }

    /**
     * Render content inside the layout
     *
     * Look for the file in the content directory
     *
     * @param string $content Content string
     * @return string
     */
    public function renderContentFile($file)
    {
        $file = $this->getContentDir() . DIRECTORY_SEPARATOR . $file;

        $content = new Content($file, $this);

        return $this->render($content, $this->getParams()->toArray());
    }

    /**
     * Render partial
     *
     * @param mixed $file
     * @return void
     */
    public function renderPartial($file)
    {
        $file = $this->getContentDir() . DIRECTORY_SEPARATOR . $file;
        $content = new ContentPartial($file, $this);
        return $content->render();
    }

    /**
     * Render content inside layout
     *
     * @param Content $content Content object
     * @return string
     */
    public function render(Content $content, $vars = array())
    {
        $layout = $this->getLayout();

        if ($content->getName() != 'index') {
            if ($item = $this->getActiveNavigationItem()) {
                $layout->setTitle($item->getText());
            }

            if ($subItem = $this->getActiveSubNavigationItem()) {
                $layout->setTitle($subItem->getText());
            }
        }

        $layout->setTitle($content->getTitle());

        // Fetch all the metadata from the content and set it to the layout so 
        // it can be accessed by the layout view template.
        foreach ($content->getMetadata() as $name => $value) {
            $layout->setParam($name, $value);
        }

        return $layout->render($content->render(null, array('site' => $vars)), $vars);
    }
}
