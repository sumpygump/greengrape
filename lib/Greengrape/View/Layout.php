<?php
/**
 * Greengrape Layout file
 *
 * @package Greengrape
 */

namespace Greengrape\View;

use Greengrape\View\AssetManager;
use \Twig_Environment;
use \Twig_Loader_String;

/**
 * Layout
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class Layout
{
    /**
     * Layout filename
     *
     * @var string
     */
    protected $_layoutFile = '';

    protected $_title;
    protected $_content;

    /**
     * Constructor
     *
     * @param string $filename Filename to layout file
     * @return void
     */
    public function __construct($filename)
    {
        $this->setLayoutFile($filename);
    }

    /**
     * Set the layout file name
     *
     * @param string $filename Filename
     * @return \Greengrape\View\Layout
     */
    public function setLayoutFile($filename)
    {
        $this->_layoutFile = $filename;
        return $this;
    }

    /**
     * Get the layout file name
     *
     * @return string
     */
    public function getLayoutFile()
    {
        return $this->_layoutFile;
    }

    public function getAssetManager()
    {
        return new AssetManager('fulcrum');
    }

    /**
     * Render the content in layout
     *
     * @param mixed $content
     * @return void
     */
    public function render($content)
    {
        $loader = new Twig_Loader_String();
        $twig   = new Twig_Environment($loader);

        $twig->addGlobal('asset', $this->getAssetManager());
        $twig->addGlobal('layout', $this);

        $this->_title = 'Greengrape';
        $this->_content = $content;

        $layoutContent = file_get_contents($this->getLayoutFile());

        return $twig->render($layoutContent,
            array(
                'title' => 'Greengrape',
                'content' => $content,
            )
        );
    }

    public function title()
    {
        return $this->_title;
    }

    public function content()
    {
        return $this->_content;
    }
}
