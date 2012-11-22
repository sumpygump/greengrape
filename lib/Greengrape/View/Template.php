<?php
/**
 *
 */

namespace Greengrape\View;

use Greengrape\View\AssetManager;
use \Twig_Environment;
use \Twig_Loader_String;

class Template
{
    /**
     * Template file
     *
     * @var string
     */
    protected $_templateFile = 'default.html';

    protected $_theme;

    /**
     * Constructor
     *
     * @param mixed $filename
     * @return void
     */
    public function __construct($filename, $theme)
    {
        $this->setFile($filename);
        $this->setTheme($theme);
    }

    public function setFile($filename)
    {
        $this->_templateFile = $filename;
        return $this;
    }

    public function getFile()
    {
        return $this->_templateFile;
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

    public function getAssetManager()
    {
        return $this->getTheme()->getAssetManager();
    }

    /**
     * Render
     *
     * @param mixed $content
     * @param array $vars
     * @return void
     */
    public function render($content, $vars = array())
    {
        $loader = new Twig_Loader_String();
        $twig   = new Twig_Environment($loader);

        $twig->addGlobal('asset', $this->getAssetManager());
        $vars['content'] = $content;

        $templateContent = file_get_contents($this->getFile());

        return $twig->render($templateContent, $vars);
    }
}
