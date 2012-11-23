<?php
/**
 * Greengrame view class file
 *
 * @package Greengrape
 */

namespace Greengrape;

use Greengrape\View\Content;
use Greengrape\View\Layout;

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
     * Active navigation item
     *
     * @var \Greengrape\NavigationItem
     */
    protected $_activeNavigationItem;

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
    public function setTheme($theme)
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
            throw new \Exception("Layout file not found: '$layoutFile'");
        }

        $layout = new Layout($layoutFile, $this->getTheme());
        $layout->setNavigationItems($this->getNavigationItems());

        return $layout;
    }

    /**
     * Set Navigation items
     *
     * @param mixed $navigationItems
     * @return void
     */
    public function setNavigationItems($navigationItems)
    {
        $this->_navigationItems = $navigationItems;
        return $this;
    }

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
     * @param \Greengrape\NavigationItem $item Item
     * @return \Greengrape\View
     */
    public function setActiveNavigationItem($item)
    {
        $this->_activeNavigationItem = $item;
        return $this;
    }

    /**
     * Get active navigation item (if any was set)
     *
     * @return void
     */
    public function getActiveNavigationItem()
    {
        return $this->_activeNavigationItem;
    }

    /**
     * Render content inside the layout
     *
     * @param string $content Content string
     * @return string
     */
    public function renderFile($file)
    {
        $content = new Content($file, $this->getTheme());

        return $this->render($content);
    }

    /**
     * Render content inside layout
     *
     * @param Content $content Content object
     * @return string
     */
    public function render(Content $content)
    {
        $layout = $this->getLayout();

        if ($item = $this->getActiveNavigationItem()) {
            $layout->setTitle($item->getText());
        }

        $layout->setTitle($content->getTitle());

        return $layout->render($content->render());
    }
}
