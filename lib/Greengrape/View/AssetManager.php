<?php

namespace Greengrape\View;

class AssetManager
{
    protected $_themeWebBase = '';

    public function __construct($themeName)
    {
        $this->setThemeWebBase($themeName);
    }

    public function setThemeWebBase($themeName)
    {
        $this->_themeWebBase = "themes/$themeName/";
    }

    public function getThemeWebBase()
    {
        return $this->_themeWebBase;
    }

    /**
     * Shortcut method to getFilePath()
     *
     * @param string $file Filename
     * @return string
     */
    public function file($file)
    {
        return $this->getFilePath($file);
    }

    /**
     * Get the web path to an asset
     *
     * @param string $file Asset file
     * @return string
     */
    public function getFilePath($file)
    {
        $filename = $this->getThemeWebBase() . $file;

        return $filename;
    }
}
