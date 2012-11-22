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

    /**
     * Path to theme files
     *
     * @var string
     */
    protected $_path = '';

    /**
     * Asset manager
     *
     * @var \Greengrape\View\AssetManager
     */
    protected $_assetManager;

    /**
     * Constructor
     *
     * @param string $name Theme name
     * @return void
     */
    public function __construct($name)
    {
        $this->setThemeName($name);

        $themesDir = APP_PATH . DIRECTORY_SEPARATOR . 'themes';
        $themePath = $themesDir . DIRECTORY_SEPARATOR . $this->getThemeName();

        if (!file_exists($themePath)) {
            throw new \Exception("Theme '" . $this->getThemeName() . "' not found. (Looking in path '" . $themePath . "')");
        }

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
