<?php
/**
 * Greengrape Layout file
 *
 * @package Greengrape
 */

namespace Greengrape\View;

use Greengrape\View\Template;
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

        if ($reset) {
            $this->_title = $title;
            return $this;
        }

        if (trim($this->_title) == '') {
            $this->_title = $title;
        } else {
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
}
