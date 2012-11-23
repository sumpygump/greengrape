<?php
/**
 * Sitemap class file
 *
 * @package Greengrape
 */

namespace Greengrape;

use Greengrape\NavigationItem;

/**
 * Sitemap
 *
 * Detects the map of available content files from content directory
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class Sitemap
{
    /**
     * Content directory
     *
     * @var string
     */
    protected $_contentDir = '';

    /**
     * Map of URLs to files
     *
     * @var array
     */
    protected $_map = array();

    /**
     * Main navigation
     *
     * @var array
     */
    protected $_mainNavigation = array();

    /**
     * Base URL
     *
     * @var string
     */
    protected $_baseUrl = '/';

    /**
     * Constructor
     *
     * @param string $contentDir Content directory path
     * @return void
     */
    public function __construct($contentDir, $baseUrl = '/')
    {
        $this->setContentDir($contentDir);
        $this->setBaseUrl($baseUrl);

        $this->_map = $this->createMap();
        $this->_mainNavigation = $this->createMainNavigation();
    }

    /**
     * Set content directory
     *
     * @param string $contentDir Content directory
     * @return \Greengrape\Sitemap
     */
    public function setContentDir($contentDir)
    {
        $this->_contentDir = $contentDir;
        return $this;
    }

    /**
     * Get content directory
     *
     * @return string
     */
    public function getContentDir()
    {
        return $this->_contentDir;
    }

    /**
     * Set Base URL
     *
     * @param string $url URL
     * @return \Greengrape\Sitemap
     */
    public function setBaseUrl($url)
    {
        $this->_baseUrl = $url;
        return $this;
    }

    /**
     * Get the base URL
     *
     * @return string
     */
    public function getBaseUrl($file = '')
    {
        if ($file == '') {
            return $this->_baseUrl;
        }

        return $this->_baseUrl . $file;
    }

    /**
     * Get content location for given URL
     *
     * @param string $url Url
     * @return string|null
     */
    public function getLocationForUrl($url)
    {
        $url = strtolower($url);
        if (array_key_exists($url, $this->_map)) {
            $location = new Location($this->_map[$url]);
            return $location;
        }

        return new Location($url);
    }

    /**
     * Get main navigation items
     *
     * @return void
     */
    public function getMainNavigation()
    {
        return $this->_mainNavigation;
    }

    /**
     * Create map of available folders and files in the content directory
     *
     * @return array
     */
    public function createMap()
    {
        $files = self::rglob($this->getContentDir() . DIRECTORY_SEPARATOR . '*');

        $map = array();
        
        foreach ($files as $file) {
            if (is_dir($file)) {
                continue;
            }

            $file = str_replace($this->getContentDir() . '/', '', $file);

            $url = str_replace('.md', '', $file);
            $url = strtolower($url);

            if ($url == 'index') {
                $url = '/';
            }

            // If the last segment is 'index', add an entry for the
            // file without the word 'index'
            $urlSegments = explode('/', $url);
            if (end($urlSegments) == 'index') {
                $alternateUrlA = str_replace('index', '', $url);
                $map[$alternateUrlA] = $file;

                $alternateUrlB = str_replace('/index', '', $url);
                $map[$alternateUrlB] = array('canonical' => $alternateUrlA);
            } else {
                $map[$url] = $file;
            }
        }

        return $map;
    }

    /**
     * Create main navigation
     *
     * @return array
     */
    public function createMainNavigation()
    {
        $path = $this->getContentDir() . DIRECTORY_SEPARATOR . '*';
        $items = glob($path, GLOB_ONLYDIR);

        $mainNavigation = array();
        foreach ($items as $item) {
            $item = str_replace($this->getContentDir() . '/', '', $item);
            $mainNavigation[] = new NavigationItem(ucfirst($item), $item . '/', $this->getBaseUrl());
        }

        if (!empty($mainNavigation)) {
            $home = new NavigationItem('Home', '/', $this->getBaseUrl());
            array_unshift($mainNavigation, $home);
        }

        return $mainNavigation;
    }

    /**
     * Recursive Glob
     * 
     * @param string $pattern Pattern
     * @param int $flags Flags to pass to glob
     * @param string $path Path to glob in
     * @return array
     */
    public static function rglob($pattern, $flags = 0, $path = '')
    {
        if (!$path && ($dir = dirname($pattern)) != '.') {
            if ($dir == '\\' || $dir == '/') {
                // This gets into infinite loop
                return array();
            }
            return self::rglob(
                basename($pattern),
                $flags, $dir . DIRECTORY_SEPARATOR
            );
        }

        $paths = glob($path . '*', GLOB_ONLYDIR | GLOB_NOSORT);
        $files = glob($path . $pattern, $flags);

        foreach ($paths as $p) {
            $files = array_merge(
                $files, self::rglob($pattern, $flags, $p . DIRECTORY_SEPARATOR)
            );
        }

        return $files;
    }
}
