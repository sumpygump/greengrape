<?php
/**
 * Greengrape kernel class file
 *
 * @package Greengrape
 */

namespace Greengrape;

use Greengrape\Request;
use Greengrape\Sitemap;
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
     * Constructor
     *
     * @param array $config Configuration settings
     * @return void
     */
    public function __construct($config)
    {
        $this->setConfig($config);
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
     * Execute the request
     *
     * @return void
     */
    public function execute()
    {
        $request = new Request();

        $uri = $request->getRequestedFile();

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

        $navigationItems = $sitemap->getMainNavigation();
        foreach ($navigationItems as &$item) {
            if ($uri == $item->getHref()) {
                $item->setIsActive(true);
            }
        }
        $view->setNavigationItems($navigationItems);

        echo $view->renderFile($this->getContentDir() . DIRECTORY_SEPARATOR . $location);
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
