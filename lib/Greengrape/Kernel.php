<?php
/**
 *
 */

namespace Greengrape;

use Greengrape\View;
use Greengrape\View\Theme;

/**
 * Kernel
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
     * @param mixed $config
     * @return void
     */
    public function __construct($config)
    {
        $this->setConfig($config);
    }

    /**
     * Set the config
     *
     * @param mixed $config
     * @return void
     */
    public function setConfig($config)
    {
        if (!isset($config['theme'])) {
            $config['theme'] = 'fulcrum';
        }

        $this->_config = $config;
    }

    /**
     * Get config
     *
     * @param mixed $param
     * @return void
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
     * Execute
     *
     * @return void
     */
    public function execute()
    {
        $theme = new Theme($this->getConfig('theme'));

        $view = new View($theme);

        $contentDir = APP_PATH . DIRECTORY_SEPARATOR . 'content';
        echo $view->render($contentDir . DIRECTORY_SEPARATOR . 'index.md');
    }
}
