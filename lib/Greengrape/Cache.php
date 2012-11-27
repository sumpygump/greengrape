<?php
/**
 * Cache class file
 *
 * @package Greengrape
 */

namespace Greengrape;

use Greengrape\Exception\GreengrapeException;

/**
 * Cache
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class Cache
{
    /**
     * Whether to allow this script to exit
     *
     * Turn off for unit tests
     *
     * @var bool
     */
    public static $allowExit = true;

    /**
     * Whether cache is enabled
     *
     * @var bool
     */
    protected $_enabled = true;

    /**
     * Location of cache directory
     *
     * @var string
     */
    protected $_cacheDir = '';

    /**
     * Current cache capturing filename
     *
     * @var string
     */
    protected $_cacheFilename = '';

    /**
     * Constructor
     *
     * @param string $cacheDir Cache directory
     * @return void
     */
    public function __construct($cacheDir)
    {
        $this->setDirectory($cacheDir);
    }

    /**
     * Disable the cache
     *
     * @return \Greengrape\Cache
     */
    public function disable()
    {
        $this->_enabled = false;
        return $this;
    }

    /**
     * Enable the cache
     *
     * @return \Greengrape\Cache
     */
    public function enable()
    {
        $this->_enabled = true;
        return $this;
    }

    /**
     * Set cache directory
     *
     * @param string $cacheDir Cache directory
     * @return \Greengrape\Cache
     */
    public function setDirectory($cacheDir)
    {
        if (!file_exists($cacheDir)) {
            throw new GreengrapeException("Cannot set cache dir. Path does not exist: '$cacheDir'.");
        }

        if (!is_writable($cacheDir)) {
            throw new GreengrapeException("Cannot set cache dir. Path not writable: '$cacheDir'.");
        }

        $this->_cacheDir = $cacheDir;
        return $this;
    }

    /**
     * Get cache directory
     *
     * @return string
     */
    public function getDirectory()
    {
        return $this->_cacheDir;
    }

    /**
     * Start the cache
     *
     * @param string $uri URI
     * @return bool
     */
    public function start($uri)
    {
        // If not enabled, do nothing
        if (!$this->_enabled) {
            return false;
        }

        // Save cacheFilename indicating we are going to capture output
        $this->_cacheFilename = $this->getCacheFilename($uri);

        if (file_exists($this->_cacheFilename)) {
            echo "<!-- Cached file -->\n";
            include $this->_cacheFilename;
            self::safeExit();
            return true;
        }

        ob_start();
        return true;
    }

    /**
     * End cache capture
     *
     * @return bool
     */
    public function end()
    {
        // If not enabled, do nothing
        // If cacheFilename is not set, we are not actively capturing output to 
        // save to the cache file, so do nothing.
        if (!$this->_enabled || !$this->_cacheFilename) {
            return false;
        }

        file_put_contents($this->_cacheFilename, ob_get_contents());
        ob_end_flush();
        return true;
    }

    /**
     * Get the name of the cache filename for a given URI
     *
     * @param string $uri URI
     * @return string
     */
    public function getCacheFilename($uri)
    {
        return $this->getDirectory() . DIRECTORY_SEPARATOR . md5(serialize($uri)) . '.cache';
    }

    /**
     * Clear the cache file for a certain file or for all the cache files
     *
     * @param string $uri URI
     * @return void
     */
    public function clear($uri = '')
    {
        if ($uri == '') {
            $files = glob($this->getDirectory() . DIRECTORY_SEPARATOR . '*.cache');
            foreach ($files as $file) {
                unlink($file);
            }
        }

        $filename = $this->getCacheFilename($uri);

        if (file_exists($filename)) {
            unlink($filename);
        }
    }

    /**
     * Only exit if it is allowed
     *
     * Useful for unit testing
     *
     * @return void
     */
    public static function safeExit()
    {
        if (self::$allowExit) {
            exit(0);
        }
    }
}
