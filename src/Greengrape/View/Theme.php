<?php
/**
 * Theme class file
 *
 * @package Greengrape
 */

namespace Greengrape\View;

use Greengrape\View\AssetManager;
use Greengrape\Exception\NotFoundException;

/**
 * Theme class
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 */
class Theme
{
    /**
     * Name of this theme
     *
     * The default theme is grapeseed
     *
     * @var string
     */
    protected $_name = 'grapeseed';

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
     * Default site title
     *
     * @var string
     */
    protected $_title = '';

    /**
     * A list of required theme files
     *
     * @var array
     */
    protected $_requiredThemeFiles = array(
        'layout.html',
        'templates/main.html',
        'templates/default.html',
        'templates/404.html',
        'templates/error.html',
    );

    /**
     * Storage for required theme files missing from this theme
     *
     * @var array
     */
    protected $_missingThemeFiles = array();

    /**
     * Constructor
     *
     * @param string $name Theme name
     * @return void
     */
    public function __construct($name, $baseUrl = '/', $themesDir = null)
    {
        $this->setName($name);

        if (null === $themesDir) {
            $themesDir = APP_PATH . DIRECTORY_SEPARATOR . 'themes';
        }

        $themePath = $themesDir . DIRECTORY_SEPARATOR . $this->getName();

        if (!file_exists($themePath)) {
            throw new NotFoundException("Theme '" . $this->getName() . "' not found. (Looking in path '" . $themePath . "')");
        }

        $this->setPath($themePath);

        $this->setAssetManager(new AssetManager($this->getName(), $baseUrl));
    }

    /**
     * Validate required files
     *
     * @return bool
     */
    public function validateRequiredFiles()
    {
        foreach ($this->_requiredThemeFiles as $file) {
            $fullFilePath = $this->getPath() . DIRECTORY_SEPARATOR . $file;

            if (!file_exists($fullFilePath)) {
                $this->_missingThemeFiles[] = $file;
            }
        }

        return !((bool) count($this->_missingThemeFiles));
    }

    /**
     * Get missing theme files list
     *
     * @return array
     */
    public function getMissingThemeFiles()
    {
        return $this->_missingThemeFiles;
    }

    /**
     * Set theme name
     *
     * @param string $name Theme name
     * @return \Greengrape\View\Theme
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * Get theme name
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Set asset manager
     *
     * @param \Greengrape\View\AssetManager $manager Asset manager
     * @return \Greengrape\View\Theme
     */
    public function setAssetManager($manager)
    {
        $this->_assetManager = $manager;
        return $this;
    }

    /**
     * Get asset manager
     *
     * @return \Greengrape\View\AssetManager
     */
    public function getAssetManager()
    {
        return $this->_assetManager;
    }

    /**
     * Set the theme path
     *
     * @param string $path Full path to theme base directory
     * @return Theme
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

    /**
     * Set the default title
     *
     * @param string $title Title
     * @return Theme
     */
    public function setDefaultTitle($title)
    {
        $this->_title = $title;
        return $this;
    }

    /**
     * Get the defalt title
     *
     * @return string Default title
     */
    public function getDefaultTitle()
    {
        if ($this->_title == '') {
            return '[Greengrape]';
        }

        return $this->_title;
    }
}
