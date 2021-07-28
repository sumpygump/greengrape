<?php
/**
 * Cache class file
 *
 * @package Greengrape
 */

namespace Greengrape;

use Greengrape\Exception\GreengrapeException;

/**
 * Cache Manager
 *
 * Provides a simple caching layer for greengrape framework server. If setting
 * in config.ini is set to `cache = true` then all requests will check if an
 * existing cached file exists and if so it will use it. If not it will
 * generate the file based on making an md5hash of the request input.
 *
 * The cache directory is in cache/content/
 *
 * The cache can be cleared for an individual request by adding the query
 * parameter `?cache=clear` to the URL.
 *
 * The cache can be cleared locally by running the script `bin/clear-cache`
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 */
class Cache
{
    /**
     * Csp object
     *
     * @var Csp
     */
    public $csp;

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
     * Set Csp object
     *
     * @param Csp $csp
     * @return Cache
     */
    public function setCsp(Csp $csp)
    {
        $this->csp = $csp;
        return $this;
    }

    /**
     * Render the CSP headers
     *
     * @return bool
     */
    public function renderCsp()
    {
        if (!$this->csp) {
            // If there is no csp object, then ignore
            return false;
        }

        return $this->csp->render();
    }

    /**
     * Get CSP nonce
     *
     * @return string
     */
    public function getCspNonce()
    {
        if (!$this->csp) {
            // If there is no csp object, then ignore
            return '';
        }

        return $this->csp->getNonce();
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
            $this->renderCsp();

            $contents = file_get_contents($this->_cacheFilename);

            // We need the nonces to still work from cached files so replace nonce placeholder
            $contents = str_replace('REPLACE_WITH_NONCE', $this->getCspNonce(), $contents);

            echo "<!-- Cached file -->\n" . $contents;
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

        $contents = ob_get_contents();

        // We need the nonces to still work when served from cached version so
        // replace the current nonce
        $contents = str_replace($this->getCspNonce(), 'REPLACE_WITH_NONCE', $contents);

        file_put_contents($this->_cacheFilename, $contents);
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
