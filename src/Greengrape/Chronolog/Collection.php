<?php
/**
 * Chronolog Collection
 *
 * @package Greengrape
 */

namespace Greengrape\Chronolog;

use \Iterator;
use Greengrape\View;
use Greengrape\View\Content;

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

    /**
     * Root dir for this collection
     *
     * @var string
     */
    protected $_rootPath = '';

    /**
     * View object
     *
     * @var View
     */
    protected $_view;

    /**
     * Constructor
     *
     * @param string $rootPath Realpath to collection root
     * @param string $baseUrl Base URL (in order to construct hrefs properly)
     * @return void
     */
    public function __construct($rootPath, $view)
    {
        $this->_rootPath = $rootPath;
        $this->_view = $view;

        $this->populate();
    }

    /**
     * Populate the collection by traversing the filesystem at the root item
     *
     * @return void
     */
    public function populate()
    {
        $paths = $this->getChildren();

        $params = $this->getView()->getParams();
        $glob_numeric = !isset($params['glob'])
            || (isset($params['glob']) && $params['glob'] == 'numeric');

        foreach ($paths as $path) {
            // get 2013-04-01-foo from 2013-04-01-foo.md
            $name = pathinfo($path, PATHINFO_FILENAME);

            if ($glob_numeric && false == preg_match('/^[0-9]/', $name)) {
                // only include items that start with a number
                continue;
            }

            $entry = new Content($path, $this->getView());

            $this->_items[] = $entry;
        }
    }

    /**
     * Reverse the sort order
     *
     * @return void
     */
    public function reverse()
    {
        // Reverse chronological sort order
        $this->_items = array_reverse($this->_items);
    }

    /**
     * Add items to collection
     *
     * TODO: This should validate the items are correct types
     *
     * @param array $items Array of Chronolog Items
     * @return Greengrape\Chronolog\Collection
     */
    public function addItems($items)
    {
        foreach ($items as $item) {
            $this->_items[] = $item;
        }

        return $this;
    }

    /**
     * Get view object
     *
     * @return View
     */
    public function getView()
    {
        return $this->_view;
    }

    /**
     * Get child chronlog entry files from specified root
     *
     * @return array
     */
    protected function getChildren()
    {
        $rootPath = $this->getRootPath();

        return glob(rtrim($rootPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*');
    }

    /**
     * Get the root path
     *
     * @return string
     */
    protected function getRootPath()
    {
        return $this->_rootPath;
    }

    /**
     * Return items array
     *
     * @return void
     */
    public function toArray()
    {
        return $this->_items;
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

