<?php
/**
 * Sitemap class file
 *
 * @package Greengrape
 */

namespace Greengrape;

use Greengrape\Navigation\Item as NavigationItem;
use Greengrape\Exception\GreengrapeException;

/**
 * Sitemap
 *
 * Detects the map of available content files from content directory
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 */
class Sitemap
{
    /**
     * Content directory
     *
     * @var string
     */
    protected $contentDir = '';

    /**
     * Map of URLs to files
     *
     * @var array<string, string>
     */
    protected $map = [];

    /**
     * Constructor
     *
     * @param string $contentDir Content directory path
     * @return void
     */
    public function __construct($contentDir)
    {
        $this->setContentDir($contentDir);

        $this->map = $this->createMap();
    }

    /**
     * Set content directory
     *
     * @param string $contentDir Content directory
     * @return \Greengrape\Sitemap
     */
    public function setContentDir($contentDir)
    {
        if (!is_string($contentDir)) {
            throw new GreengrapeException('Content dir must be a string');
        }

        $this->contentDir = $contentDir;
        return $this;
    }

    /**
     * Get content directory
     *
     * @return string
     */
    public function getContentDir()
    {
        return $this->contentDir;
    }

    /**
     * Get content location for given URL
     *
     * @param string $url Url
     * @return Location
     */
    public function getLocationForUrl($url)
    {
        if (array_key_exists($url, $this->map)) {
            $location = new Location($this->map[$url]);
            return $location;
        }

        return new Location($url);
    }

    /**
     * Get a count of the number of items in the map
     *
     * @return int
     */
    public function getCountMapItems()
    {
        return count($this->map);
    }

    /**
     * Create map of available folders and files in the content directory
     *
     * @return array<string, string>
     */
    public function createMap()
    {
        $files = self::rglob($this->getContentDir() . DIRECTORY_SEPARATOR . '*');

        $map = [];

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
                $map[$url] = ['canonical' => $url . '/'];
                continue;
            }

            // If the last segment is 'index', add an entry for the
            // file without the word 'index'
            $urlSegments = explode('/', $url);

            if (end($urlSegments) == 'index') {
                $url = str_replace('index', '', $url);

                $map[$url] = $file;

                $map[rtrim($url, '/')] = ['canonical' => $url];
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
     * @return array<int, string>
     */
    public static function rglob($pattern, $flags = 0, $path = '')
    {
        if (!$path && ($dir = dirname($pattern)) != '.') {
            if ($dir == '\\' || $dir == '/') {
                // This gets into infinite loop
                return [];
            }
            return self::rglob(
                basename($pattern),
                $flags,
                $dir . DIRECTORY_SEPARATOR
            );
        }

        $paths = glob($path . '*', GLOB_ONLYDIR | GLOB_NOSORT);
        $files = glob($path . $pattern, $flags);

        foreach ($paths as $p) {
            $files = array_merge(
                $files,
                self::rglob($pattern, $flags, $p . DIRECTORY_SEPARATOR)
            );
        }

        return $files;
    }
}
