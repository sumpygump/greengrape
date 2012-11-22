<?php

namespace Greengrape;

use Greengrape\View;

class Kernel
{
    protected $_config = array();

    public function __construct($config)
    {
        $this->setConfig($config);
    }

    public function setConfig($config)
    {
        if (!isset($config['theme'])) {
            $config['theme'] = 'fulcrum';
        }

        $this->_config = $config;
    }

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

    public function execute()
    {
        $themesDir = APP_PATH . DIRECTORY_SEPARATOR . 'themes';
        $themePath = realpath($themesDir . DIRECTORY_SEPARATOR . $this->getConfig('theme'));

        $view = new View($themePath);

        $contentDir = APP_PATH . DIRECTORY_SEPARATOR . 'content';
        echo $view->renderFile($contentDir . DIRECTORY_SEPARATOR . 'index.md');
    }
}
