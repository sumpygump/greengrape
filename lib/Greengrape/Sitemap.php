<?php
/**
 * Sitemap class file
 *
 * @package Greengrape
 */

namespace Greengrape;

use Greengrape\Navigation\Item as NavigationItem;

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
     * Constructor
     *
     * @param string $contentDir Content directory path
     * @return void
     */
    public function __construct($contentDir)
    {
        $this->setContentDir($contentDir);

        $this->_map = $this->createMap();
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
     * Get content location for given URL
     *
     * @param string $url Url
     * @return string|null
     */
    public function getLocationForUrl($url)
    {
        if (array_key_exists($url, $this->_map)) {
            $location = new Location($this->_map[$url]);
            return $location;
        }

        return new Location($url);
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
                $isDir = true;
            } else {
                $isDir = false;
            }

            // Remove the common first part
            $file = str_replace($this->getContentDir() . '/', '', $file);

            $url = str_replace('.md', '', $file);
            $url = NavigationItem::translateOrderedName($url);

            if ($url == 'index') {
                // If we're left with just index, change to home page
                $map['/'] = $file;
                continue;
            }

            if ($isDir) {
                $map[$url . '/'] = $file;
                $map[$url] = array('canonical' => $url . '/');
                continue;
            }

            // If the last segment is 'index', add an entry for the
            // file without the word 'index'
            $urlSegments = explode('/', $url);

            if (end($urlSegments) == 'index') {
                $url = str_replace('index', '', $url);

                $map[$url] = $file;

                $map[rtrim($url, '/')] = array('canonical' => $url);
                continue;
            }

            $map[$url] = $file;
        }

        return $map;
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
