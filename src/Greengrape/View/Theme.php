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
    protected $name = 'grapeseed';

    /**
     * Path to theme files
     *
     * @var string
     */
    protected $path = '';

    /**
     * Asset manager
     *
     * @var \Greengrape\View\AssetManager
     */
    protected $assetManager;

    /**
     * Default site title
     *
     * @var string
     */
    protected $title = '';

    /**
     * A list of required theme files
     *
     * @var array<int, string>
     */
    protected $requiredThemeFiles = [
        'layout.html',
        'templates/main.html',
        'templates/default.html',
        'templates/404.html',
        'templates/error.html',
    ];

    /**
     * Storage for required theme files missing from this theme
     *
     * @var array<int, string>
     */
    protected $missingThemeFiles = [];

    /**
     * Constructor
     *
     * @param string $name Theme name
     * @param string $baseUrl The base URL of site
     * @param null|string $themesDir Directory where themes live
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
            throw new NotFoundException(
                "Theme '" . $this->getName() . "' not found. (Looking in path '"
                . $themePath . "')"
            );
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
        foreach ($this->requiredThemeFiles as $file) {
            $fullFilePath = $this->getPath() . DIRECTORY_SEPARATOR . $file;

            if (!file_exists($fullFilePath)) {
                $this->missingThemeFiles[] = $file;
            }
        }

        return !((bool) count($this->missingThemeFiles));
    }

    /**
     * Get missing theme files list
     *
     * @return array<int, string>
     */
    public function getMissingThemeFiles()
    {
        return $this->missingThemeFiles;
    }

    /**
     * Set theme name
     *
     * @param string $name Theme name
     * @return \Greengrape\View\Theme
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get theme name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set asset manager
     *
     * @param \Greengrape\View\AssetManager $manager Asset manager
     * @return \Greengrape\View\Theme
     */
    public function setAssetManager($manager)
    {
        $this->assetManager = $manager;
        return $this;
    }

    /**
     * Get asset manager
     *
     * @return \Greengrape\View\AssetManager
     */
    public function getAssetManager()
    {
        return $this->assetManager;
    }

    /**
     * Set the theme path
     *
     * @param string $path Full path to theme base directory
     * @return Theme
     */
    public function setPath($path)
    {
        $this->path = $path;
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
            return $this->path;
        }

        return $this->path . DIRECTORY_SEPARATOR . $file;
    }

    /**
     * Set the default title
     *
     * @param string $title Title
     * @return Theme
     */
    public function setDefaultTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get the defalt title
     *
     * @return string Default title
     */
    public function getDefaultTitle()
    {
        if ($this->title == '') {
            return '[Greengrape]';
        }

        return $this->title;
    }
}
