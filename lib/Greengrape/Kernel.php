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
use Greengrape\View;
use Greengrape\View\Theme;

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
     * Constructor
     *
     * @param array $config Configuration settings
     * @return void
     */
    public function __construct($config)
    {
        $this->setConfig($config);

        $this->setCache(new Cache(APP_PATH . '/cache/content'));
    }

    /**
     * Set the config
     *
     * @param array $config Configuration settings
     * @return \Greengrape\Kernel
     */
    public function setConfig($config)
    {
        if (!isset($config['theme'])) {
            $config['theme'] = 'fulcrum';
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
            $redirectUrl = $request->getBaseUrl('/') . $location->getCanonical();
            header("Location: " . $redirectUrl);
            exit(1);
        }

        $theme = new Theme($this->getConfig('theme'), $request->getBaseUrl());
        $view  = new View($theme);

        $this->setupNavigationItems($sitemap, $uri, $view);

        echo $view->renderFile($this->getContentDir() . DIRECTORY_SEPARATOR . $location);
        $this->getCache()->end();
    }

    /**
     * Set up the navigation items and assign them to the view
     *
     * @param \Greengrape\Sitemap $sitemap Sitemap object
     * @param string $uri Current URI
     * @param \Greengrape\View $view View object
     * @return void
     */
    public function setupNavigationItems($sitemap, $uri, $view)
    {
        $navigationItems = $sitemap->getMainNavigation();
        foreach ($navigationItems as &$item) {
            // If the first part of the URI matches this item's href then this 
            // should be the active navigation item
            if (strpos($uri, $item->getHref()) === 0) {
                $item->setIsActive(true);
                $view->setActiveNavigationItem($item);
            }
        }
        $view->setNavigationItems($navigationItems);

        $subNavigationItems = $sitemap->createSubNavigationItems($view->getActiveNavigationItem());
        foreach ($subNavigationItems as &$subItem) {
            // If the first part of the URI matches this items' href then this 
            // should be the active navigation item
            if (strpos($uri, $subItem->getHref()) === 0) {
                $subItem->setIsActive(true);
                $view->setActiveSubNavigationItem($subItem);
            }
        }
        $view->setSubNavigationItems($subNavigationItems);
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
}
