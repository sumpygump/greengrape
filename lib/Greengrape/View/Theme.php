<?php

namespace Greengrape\View;

use Greengrape\View\AssetManager;

class Theme
{
    /**
     * Name of this theme
     *
     * The default theme is fulcrum
     *
     * @var string
     */
    protected $_name = 'fulcrum';

    protected $_path = '';

    /**
     * Asset manager
     *
     * @var \Greengrape\View\AssetManager
     */
    protected $_assetManager;

    public function __construct($name)
    {
        $this->setThemeName($name);

        $themesDir = APP_PATH . DIRECTORY_SEPARATOR . 'themes';
        $themePath = realpath($themesDir . DIRECTORY_SEPARATOR . $this->getThemeName());

        $this->setPath($themePath);

        $this->setAssetManager(new AssetManager($this->getThemeName()));
    }

    public function setThemeName($name)
    {
        $this->_name = $name;
        return $this;
    }

    public function getThemeName()
    {
        return $this->_name;
    }

    public function setAssetManager($manager)
    {
        $this->_assetManager = $manager;
        return $this;
    }

    public function getAssetManager()
    {
        return $this->_assetManager;
    }

    /**
     * Set the theme path
     *
     * @param string $path Full path to theme base directory
     * @return \Greengrape\View
     */
    public function setPath($path)
    {
        $this->_path = $path;
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
    public function getPath($file = null)
    {
        if (null === $file) {
            return $this->_path;
        }

        return $this->_path . DIRECTORY_SEPARATOR . $file;
    }
}
