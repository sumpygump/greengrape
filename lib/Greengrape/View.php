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
     * Theme base path
     *
     * @var string
     */
    protected $_themePath = '';

    /**
     * Constructor
     *
     * @param string $themePath Base of theme path
     * @return void
     */
    public function __construct($themePath = '')
    {
        $this->setThemePath($themePath);
    }

    /**
     * Set the theme path
     *
     * @param string $path Full path to theme base directory
     * @return \Greengrape\View
     */
    public function setThemePath($path)
    {
        $this->_themePath = $path;
        return $this;
    }

    /**
     * Get theme path
     *
     * Get the base theme path, or if an argument is passed in, get the full 
     * path to that asset within the theme
     *
     * @param string $file Filepath to retrieve
     * @return string
     */
    public function getThemePath($file = null)
    {
        if (null === $file) {
            return $this->_themePath;
        }

        return $this->_themePath . DIRECTORY_SEPARATOR . $file;
    }

    /**
     * Get layout
     *
     * @return \Greengrape\View\Layout
     */
    public function getLayout()
    {
        $layoutFile = $this->getThemePath('layout.html');

        $layout = new Layout($layoutFile);

        return $layout;
    }

    /**
     * Render content inside the layout
     *
     * @param string $content Content string
     * @return string
     */
    public function render($content)
    {
        $content = new Content($content);
        $layout = $this->getLayout();
        return $layout->render($content->render());
    }

    /**
     * Render a file
     *
     * @param string $file Filename
     * @return string
     */
    public function renderFile($file)
    {
        $fileContents = file_get_contents($file);

        return $this->render($fileContents);
    }
}
