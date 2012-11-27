<?php
/**
 * Navigation Collection
 *
 * @package Greengrape
 */

namespace Greengrape\Navigation;

use \Iterator;
use Greengrape\Navigation\Item;

/**
 * Collection
 *
 * @uses Iterator
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class Collection implements Iterator
{
    /**
     * Cursor position in items array
     *
     * @var int
     */
    private $_cursor = 0;

    /**
     * Items
     *
     * @var array
     */
    protected $_items = array();

    protected $_rootItem;
    protected $_contentDir = '';
    protected $_baseUrl = '';

    /**
     * Constructor
     *
     * @param string $contentDir Realpath to content directory
     * @param string $baseUrl Base URL (in order to construct hrefs properly)
     * @param Greengrape\Navigation\Items $rootItem Root item
     * @return void
     */
    public function __construct($contentDir, $baseUrl, $rootItem = null)
    {
        $this->_contentDir = $contentDir;
        $this->_baseUrl = $baseUrl;
        $this->_rootItem = $rootItem;

        $this->populate();
    }

    /**
     * Populate the collection by traversing the filesystem at the root item
     *
     * @return void
     */
    public function populate()
    {
        $paths = $this->_getChildren();

        foreach ($paths as $path) {
            $path = str_replace($this->_contentDir . DIRECTORY_SEPARATOR, '', $path);

            $basename = basename($path);

            if (substr($basename, 0, 1) == '_') {
                // skip items that start with underscore, they are hidden
                continue;
            }

            $item = new Item($basename, $path . '/', $this->_baseUrl);
            $this->_items[] = $item;
        }

        // If we are at the actual site root, we need to add an item to Home
        if (!$this->_rootItem && !empty($this->_items)) {
            $home = new Item('Home', '/', $this->_baseUrl);
            array_unshift($this->_items, $home);
        }
    }

    /**
     * Get child folders from specified root
     *
     * @return array
     */
    protected function _getChildren()
    {
        // If the root is the home, don't return any children
        if ($this->_rootItem && $this->_rootItem->getHref() == '/') {
            return array();
        }

        $rootPath = $this->_getRootPath();

        return glob(rtrim($rootPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
    }

    /**
     * Get the root path
     *
     * @return string
     */
    protected function _getRootPath()
    {
        // If there is no root item, the root is the content dir
        if (null === $this->_rootItem) {
            return $this->_contentDir;
        }

        return $this->_contentDir . DIRECTORY_SEPARATOR
            . $this->_rootItem->getRawHref();
    }

    /**
     * Fetch current item
     *
     * For Iterator interface
     *
     * @return void
     */
    public function current()
    {
        return $this->_items[$this->_cursor];
    }

    /**
     * Get cursor
     *
     * For Iterator interface
     *
     * @return int
     */
    public function key()
    {
        return $this->_cursor;
    }

    /**
     * Move to next item
     *
     * For Iterator interface
     *
     * @return void
     */
    public function next()
    {
        ++$this->_cursor;
    }

    /**
     * Rewind back to first item
     *
     * @return void
     */
    public function rewind()
    {
        $this->_cursor = 0;
    }

    /**
     * Whether current item is valid
     *
     * For Iterator interface
     *
     * @return void
     */
    public function valid()
    {
        return isset($this->_items[$this->_cursor]);
    }
}
