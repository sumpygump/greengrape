<?php
/**
 * Template class file
 *
 * @package Greengrape
 */

namespace Greengrape\View;

use Greengrape\View\AssetManager;
use \Twig_Environment;
use \Twig_Loader_String;

/**
 * Template
 *
 * This represents a template file that can be rendered via Twig
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class Template
{
    /**
     * Template file
     *
     * @var string
     */
    protected $_templateFile = 'default.html';

    /**
     * Theme object
     *
     * @var \Greengrape\View\Theme
     */
    protected $_theme;

    /**
     * Constructor
     *
     * @param string $filename Filename to template file
     * @param \Greengrape\View\Theme $theme Theme object
     * @return void
     */
    public function __construct($filename, $theme)
    {
        $this->setFile($filename);
        $this->setTheme($theme);
    }

    /**
     * Set template file
     *
     * @param string $filename Template file name
     * @return \Greengrape\View\Template
     */
    public function setFile($filename)
    {
        $this->_templateFile = $filename;
        return $this;
    }

    /**
     * Get template file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->_templateFile;
    }

    /**
     * Set theme object
     *
     * @param \Greengrape\View\Theme $theme Theme object
     * @return \Greengrape\View\Template
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
     * Get the asset manager
     *
     * @return \Greengrape\View\AssetManager
     */
    public function getAssetManager()
    {
        return $this->getTheme()->getAssetManager();
    }

    /**
     * Render content in template
     *
     * @param string $content Main content to render
     * @param array $vars Variables to pass to Twig template
     * @return string Rendered HTML
     */
    public function render($content, $vars = array())
    {
        $vars['content'] = $content;

        $loader = new Twig_Loader_String();
        $twig   = new Twig_Environment($loader);

        $twig->addGlobal('asset', $this->getAssetManager());

        $templateContent = file_get_contents($this->getFile());

        return $twig->render($templateContent, $vars);
    }
}
