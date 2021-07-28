<?php
/**
 * Greengrape view class file
 *
 * @package Greengrape
 */

namespace Greengrape;

use Greengrape\Config;
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
 */
class View
{
    /**
     * Theme object
     *
     * @var Theme
     */
    protected $_theme;

    /**
     * Navigation items
     *
     * @var Collection|array
     */
    protected $_navigationItems = [];

    /**
     * Sub navigation items
     *
     * @var Collection|array
     */
    protected $_subNavigationItems = [];

    /**
     * Active navigation item
     *
     * @var Item
     */
    protected $_activeNavigationItem;

    /**
     * Active sub navigation item
     *
     * @var Item
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
     * @param string $theme Theme object
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
     * Set params
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
     * Set an individual param
     *
     * @param string $key
     * @param mixed $value
     * @return \Greengrape\View
     */
    public function setParam($key, $value)
    {
        $this->_params[$key] = $value;
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
     * @param Collection $navigationItems Navigation items
     * @return View
     */
    public function setNavigationItems(Collection $navigationItems)
    {
        $this->_navigationItems = $navigationItems;
        return $this;
    }

    /**
     * Get Main navigation items
     *
     * @return Collection
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
     * @param Item $item Item
     * @return View
     */
    public function setActiveNavigationItem(Item $item)
    {
        $this->_activeNavigationItem = $item;
        return $this;
    }

    /**
     * Get active navigation item (if any was set)
     *
     * @return Item
     */
    public function getActiveNavigationItem()
    {
        return $this->_activeNavigationItem;
    }

    /**
     * Set the active subnavigation item
     *
     * @param Item $item Navigation item
     * @return View
     */
    public function setActiveSubNavigationItem(Item $item)
    {
        $this->_activeSubNavigationItem = $item;
        return $this;
    }

    /**
     * Get active subnavigation item
     *
     * @return Item
     */
    public function getActiveSubNavigationItem()
    {
        return $this->_activeSubNavigationItem;
    }

    /**
     * Set sub navigation items
     *
     * @param Collection $items Items
     * @return View
     */
    public function setSubNavigationItems(Collection $items)
    {
        $this->_subNavigationItems = $items;
        return $this;
    }

    /**
     * Get subnavigation items
     *
     * @return Collection
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
     * @param string $file Filename
     * @return string
     */
    public function renderContentFile($file)
    {
        $file = $this->getContentDir() . DIRECTORY_SEPARATOR . $file;
        $content = new Content($file, $this);

        return $this->render($content);
    }

    /**
     * Render partial
     *
     * @param string $file
     * @return string
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

        $params = $this->getParams();
        if ($params instanceof Config) {
            $params = $params->toArray();
        }
        $params = $params + $vars;

        return $layout->render($content->render(null, array('site' => $params)), $params);
    }
}
