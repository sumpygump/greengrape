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
     * The title of the page
     *
     * @var mixed
     */
    protected $_title;

    /**
     * The content of the page
     *
     * @var mixed
     */
    protected $_content;

    /**
     * Render the content in layout
     *
     * @param mixed $content
     * @return void
     */
    public function render($content, $vars = array())
    {
        $loader = new Twig_Loader_String();
        $twig   = new Twig_Environment($loader);

        $twig->addGlobal('asset', $this->getAssetManager());
        $twig->addGlobal('layout', $this);

        $this->_title = 'Greengrape';
        $this->_content = $content;

        $layoutContent = file_get_contents($this->getFile());

        return $twig->render($layoutContent, $vars);
    }

    /**
     * Get the title
     *
     * @return void
     */
    public function title()
    {
        return $this->_title;
    }

    /**
     * Get the content for this page
     *
     * @return void
     */
    public function content()
    {
        return $this->_content;
    }
}
