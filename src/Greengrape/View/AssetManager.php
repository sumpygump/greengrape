<?php
/**
 * Asset Manager class file
 *
 * @package Greengrape
 */

namespace Greengrape\View;

/**
 * AssetManager
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 */
class AssetManager
{
    /**
     * Base URL
     *
     * @var string
     */
    protected $baseUrl = '';

    /**
     * The web base url for this theme
     *
     * @var string
     */
    protected $themeBaseUrl = '';

    /**
     * List of supported asset dirs in the themes
     *
     * @var array<int, string>
     */
    protected static $supportedAssetDirs = ['js', 'css', 'img'];

    /**
     * Constructor
     *
     * @param string $themeName Name of theme
     * @param string $baseUrl Base URL of site
     * @return void
     */
    public function __construct($themeName, $baseUrl = '/')
    {
        $baseUrl = rtrim($baseUrl, '/');

        $this->setBaseUrl($baseUrl);
        $this->setThemeBaseUrl($this->getBaseUrl('/themes/' . $themeName));
    }

    /**
     * Set base url
     *
     * @param string $url Base (web root) URL
     * @return AssetManager
     */
    public function setBaseUrl($url)
    {
        $this->baseUrl = $url;
        return $this;
    }

    /**
     * Get the base URL
     *
     * @param string $file File to append to base path
     * @return string
     */
    public function getBaseUrl($file = '')
    {
        $baseUrl = rtrim($this->baseUrl, '/') . '/';

        if ($file == '') {
            return $baseUrl;
        }

        return $baseUrl . ltrim($file, '/');
    }

    /**
     * Set the web base url
     *
     * @param string $themeName Theme name
     * @return AssetManager
     */
    public function setThemeBaseUrl($themeName)
    {
        $this->themeBaseUrl = "$themeName/";
        return $this;
    }

    /**
     * Get the web base url for set theme
     *
     * @return string
     */
    public function getThemeBaseUrl()
    {
        return $this->themeBaseUrl;
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
        $assetDir  = self::detectAssetDir($file);
        $extension = self::getExtension($file);

        if ($assetDir != '' && $extension == '') {
            // Append the extension for an asset that doesn't have an
            // extension based on the asset dir (js, css).
            $file .= '.' . $assetDir;
        }

        $filename = $this->getThemeBaseUrl() . $file;

        return $filename;
    }

    /**
     * Get the extension for a given filename
     *
     * @param string $filename Filename
     * @return string
     */
    public static function getExtension($filename)
    {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }

    /**
     * Detect asset type (css, js) based on folder requested
     *
     * This is expecting that all file in 'js/' should default to ending in '.js'
     *
     * @param string $filepath Path to a file
     * @return string
     */
    public static function detectAssetDir($filepath)
    {
        if (strpos($filepath, '/') === false) {
            return '';
        }

        $pathParts = explode('/', $filepath);
        $assetDir = strtolower($pathParts[0]);

        if (!in_array($assetDir, self::$supportedAssetDirs)) {
            return '';
        }

        return $assetDir;
    }
}
