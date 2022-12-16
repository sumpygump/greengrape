<?php

/**
 * Navigation Collection
 *
 * @package Greengrape
 */

namespace Greengrape\Navigation;

use Iterator;
use Countable;
use Greengrape\Navigation\Item;

/**
 * Collection
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @implements Iterator<int, Item>
 */
class Collection implements Iterator, Countable
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
     * @var array<int, Item>
     */
    protected $items = [];

    /**
     * Root item
     *
     * @var Item
     */
    protected $rootItem;

    /**
     * Content directory (root dir for all content files)
     *
     * @var string
     */
    protected $contentDir = '';

    /**
     * Base URL (web root - for generating links to navigation items)
     *
     * @var string
     */
    protected $baseUrl = '';

    /**
     * Whether to include the home link in the nav
     *
     * @var bool
     */
    protected $includeHome = true;

    /**
     * Constructor
     *
     * @param string $contentDir Realpath to content directory
     * @param string $baseUrl Base URL (in order to construct hrefs properly)
     * @param Item|null $rootItem Root item
     * @param array<string, bool> $config
     * @return void
     */
    public function __construct($contentDir, $baseUrl, $rootItem = null, $config = [])
    {
        if (isset($config['include_home_in_nav'])) {
            $this->includeHome = (bool) $config['include_home_in_nav'];
        }

        $this->contentDir = $contentDir;
        $this->baseUrl = $baseUrl;
        $this->rootItem = $rootItem;

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

        foreach ($paths as $path) {
            $path = str_replace($this->contentDir . DIRECTORY_SEPARATOR, '', $path);

            $basename = basename($path);

            if (substr($basename, 0, 1) == '_') {
                // skip items that start with underscore, they are hidden
                continue;
            }

            $item = new Item($basename, $path . '/', $this->baseUrl);
            $this->items[] = $item;
        }

        // If we are at the actual site root, we need to add an item to Home
        if ($this->includeHome && empty($this->rootItem) && !empty($this->items)) {
            $home = new Item('Home', '/', $this->baseUrl);
            array_unshift($this->items, $home);
        }
    }

    /**
     * Add items to collection
     *
     * @param array<int, Item> $items Array of Navigation Items
     * @return Collection
     */
    public function addItems($items)
    {
        foreach ($items as $item) {
            $item->setBaseUrl($this->baseUrl);
            $this->items[] = $item;
        }

        return $this;
    }

    /**
     * Get child folders from specified root
     *
     * @return array<int, string>
     */
    protected function getChildren()
    {
        // If the root is the home, don't return any children
        if (!empty($this->rootItem) && $this->rootItem->getHref() == '/') {
            return [];
        }

        $rootPath = $this->getRootPath();

        return glob(rtrim($rootPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
    }

    /**
     * Get the root path
     *
     * @return string
     */
    protected function getRootPath()
    {
        // If there is no root item, the root is the content dir
        if (null === $this->rootItem) {
            return $this->contentDir;
        }

        return $this->contentDir . DIRECTORY_SEPARATOR
            . $this->rootItem->getRawHref();
    }

    /**
     * Return items array
     *
     * @return array<int, Item>
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
     * @return Item
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

    /**
     * Count number of items
     *
     * For Countable interface
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }
}
