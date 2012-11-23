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
     * Constructor
     *
     * @param string $themePath Base of theme path
     * @return void
     */
    public function __construct($theme)
    {
        $this->setTheme($theme);
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

        return $layout;
    }

    /**
     * Render content inside the layout
     *
     * @param string $content Content string
     * @return string
     */
    public function render($file)
    {
        $content = new Content($file, $this->getTheme());

        $layout = $this->getLayout();
        return $layout->render($content->render());
    }
}
