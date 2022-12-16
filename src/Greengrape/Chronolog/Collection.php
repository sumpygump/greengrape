<?php

/**
 * Chronolog Collection
 *
 * @package Greengrape
 */

namespace Greengrape\Chronolog;

use Iterator;
use Greengrape\View;
use Greengrape\View\Content;

/**
 * Collection
 *
 * @implements Iterator<int, Content>
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 */
class Collection implements Iterator
{
    /**
     * Cursor position in items array
     *
     * @var int
     */
    private $cursor = 0;

    /**
     * Items
     *
     * @var array<int, Content>
     */
    protected $items = [];

    /**
     * Root dir for this collection
     *
     * @var string
     */
    protected $rootPath = '';

    /**
     * View object
     *
     * @var View
     */
    protected $view;

    /**
     * Constructor
     *
     * @param string $rootPath Realpath to collection root
     * @param View $view View object
     * @return void
     */
    public function __construct($rootPath, $view)
    {
        $this->rootPath = $rootPath;
        $this->view = $view;

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

            $this->items[] = $entry;
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
        $this->items = array_reverse($this->items);
    }

    /**
     * Add items to collection
     *
     * TODO: This should validate the items are correct types
     *
     * @param array<int, Content> $items Array of Chronolog Items
     * @return Collection
     */
    public function addItems($items)
    {
        foreach ($items as $item) {
            $this->items[] = $item;
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
        return $this->view;
    }

    /**
     * Get child chronlog entry files from specified root
     *
     * @return array<int, string>
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
        return $this->rootPath;
    }

    /**
     * Return items array
     *
     * @return array<int, Content>
     */
    public function toArray()
    {
        return $this->items;
    }

    /**
     * Fetch current item
     *
     * For Iterator interface
     *
     * @return Content
     */
    public function current(): mixed
    {
        return $this->items[$this->cursor];
    }

    /**
     * Get cursor
     *
     * For Iterator interface
     *
     * @return int
     */
    public function key(): mixed
    {
        return $this->cursor;
    }

    /**
     * Move to next item
     *
     * For Iterator interface
     *
     * @return void
     */
    public function next(): void
    {
        ++$this->cursor;
    }

    /**
     * Rewind back to first item
     *
     * @return void
     */
    public function rewind(): void
    {
        $this->cursor = 0;
    }

    /**
     * Whether current item is valid
     *
     * For Iterator interface
     *
     * @return bool
     */
    public function valid(): bool
    {
        return isset($this->items[$this->cursor]);
    }
}
