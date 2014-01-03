<?php
/**
 * Greengrape kernel class file
 *
 * @package Greengrape
 */

namespace Greengrape;

use Greengrape\Request;
use Greengrape\Sitemap;
use Greengrape\Cache;
use Greengrape\Navigation\Collection as NavigationCollection;
use Greengrape\View;
use Greengrape\View\Theme;
use Greengrape\Exception\GreengrapeException;

/**
 * Kernel class
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class Kernel
{
    /**
     * Configuration
     *
     * @var array
     */
    protected $_config = array();

    /**
     * Cache object
     *
     * @var \Greengrape\Cache
     */
    protected $_cache;

    /**
     * Whether to allow this script to exit
     *
     * Turn off for unit tests
     *
     * @var bool
     */
    public static $allowExit = true;

    /**
     * Constructor
     *
     * @param array $config Configuration settings
     * @return void
     */
    public function __construct($config)
    {
        $this->setConfig($config);

        $this->setCache(new Cache(APP_PATH . '/cache/content'));

        if (!$this->getConfig('enable_cache')) {
            $this->getCache()->disable();
        }
    }

    /**
     * Set the config
     *
     * @param array $config Configuration settings
     * @return \Greengrape\Kernel
     */
    public function setConfig($config)
    {
        if (!isset($config['theme']) || trim($config['theme']) == '') {
            $config['theme'] = 'grapeseed';
        }

        $this->_config = $config;

        return $this;
    }

    /**
     * Get config
     *
     * If no arguments it will return the entire configuration array, otherwise 
     * it will return the setting for the given option parameter
     *
     * @param string $param Param name
     * @return mixed
     */
    public function getConfig($param = null)
    {
        if (null === $param) {
            return $this->_config;
        }

        if (!isset($this->_config[$param])) {
            return null;
        }

        return $this->_config[$param];
    }

    /**
     * Set cache
     *
     * @param \Greengrape\Cache $cache Cache object
     * @return \Greengrame\Kernel
     */
    public function setCache($cache)
    {
        $this->_cache = $cache;
        return $this;
    }

    /**
     * Get cache
     *
     * @return \Greengrape\Cache
     */
    public function getCache()
    {
        return $this->_cache;
    }

    /**
     * Execute the request
     *
     * @return void
     */
    public function execute()
    {
        $request = new Request();

        $uri = $request->getRequestedFile();

        // Special option to clear cache for this file with ?cache=clear
        if ($request->cache == 'clear') {
            $this->getCache()->clear($uri);
        }
        $this->getCache()->start($uri);

        $sitemap = new Sitemap($this->getContentDir(), $request->getBaseUrl());

        $location = $sitemap->getLocationForUrl($uri);

        // If canonical is set, we should redirect thither instead.
        if ($location->getCanonical()) {
            $this->redirect($request->getBaseUrl('/') . $location->getCanonical());
        }

        $theme = $this->makeTheme($request);

        $view = new View($theme);
        $view->setContentDir($this->getContentDir());
        $view->setParams($this->getConfig());

        $this->setupNavigationItems($request, $uri, $view);

        echo $view->renderContentFile($location);
        $this->getCache()->end();
    }

    /**
     * Make the theme object
     *
     * @param Greengrape\Request $request Request object
     * @return Greengrape\View\Theme
     */
    public function makeTheme($request)
    {
        if ($request->preview_theme) {
            $themeName = $request->preview_theme;
        } else {
            $themeName = $this->getConfig('theme');
        }

        $theme = new Theme($themeName, $request->getBaseUrl());
        $theme->setDefaultTitle($this->getConfig('sitename'));

        return $theme;
    }

    /**
     * Set up the navigation items and assign them to the view
     *
     * @param \Greengrape\Sitemap $sitemap Sitemap object
     * @param string $uri Current URI
     * @param \Greengrape\View $view View object
     * @return void
     */
    public function setupNavigationItems($request, $uri, $view)
    {
        $mainNavigationCollection = new NavigationCollection($this->getContentDir(), $request->getBaseUrl());
        foreach ($mainNavigationCollection as $item) {
            // If the first part of the URI matches this item's href then this 
            // should be the active navigation item
            if (strpos($uri, $item->getHref()) === 0) {
                $item->setIsActive(true);
                $view->setActiveNavigationItem($item);
            }
        }
        $view->setNavigationItems($mainNavigationCollection);

        if (!$view->getActiveNavigationItem()) {
            // If we don't have an active navigation item, don't try to get the 
            // sub navigation
            return false;
        }

        $subNavigationCollection = new NavigationCollection($this->getContentDir(), $request->getBaseUrl(), $view->getActiveNavigationItem());
        foreach ($subNavigationCollection as $subItem) {
            // If the first part of the URI matches this item's href then this 
            // should be the active navigation item
            if (strpos($uri, $subItem->getHref()) === 0) {
                $subItem->setIsActive(true);
                $view->setActiveSubNavigationItem($subItem);
            }
        }
        $view->setSubNavigationItems($subNavigationCollection);
    }

    /**
     * Get content dir
     *
     * @return string
     */
    public function getContentDir()
    {
        return APP_PATH . DIRECTORY_SEPARATOR . 'content';
    }

    /**
     * Redirect to a URL
     *
     * @param string $url URL for redirect
     * @return void
     */
    public function redirect($url)
    {
        if (headers_sent() || PHP_SAPI == 'cli') {
            throw new GreengrapeException("Headers already sent, cannot redirect! (to '$url')");
        }

        header("Location: " . $url);

        $this->safeExit();
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
