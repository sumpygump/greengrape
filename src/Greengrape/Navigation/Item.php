<?php
/**
 * Navigation item class file
 *
 * @package Greengrape
 */

namespace Greengrape\Navigation;

use Greengrape\Exception\GreengrapeException;

/**
 * Navigation Item
 *
 * Represents an item in main or sub navigation
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 */
class Item
{
    /**
     * The text of the navigation link
     *
     * @var string
     */
    protected $_text = '';

    /**
     * The href of the navigation link
     *
     * @var string
     */
    protected $_href = '';

    /**
     * The raw path name
     *
     * @var string
     */
    protected $_rawHref = '';

    /**
     * Whether the link is currently active
     *
     * @var bool
     */
    protected $_isActive = false;

    /**
     * Base URL
     *
     * @var string
     */
    protected $_baseUrl = '';

    /**
     * Constructor
     *
     * @param string $text Text of the link
     * @param string $href Href of the link
     * @return void
     */
    public function __construct($text, $href, $baseUrl = '/')
    {
        $this->setText($text);
        $this->setHref($href);
        $this->setBaseUrl($baseUrl);
    }

    /**
     * Set the text
     *
     * @param string $text Link text
     * @return \Greengrape\Navigation\Item
     */
    public function setText($text)
    {
        if (!is_string($text)) {
            throw new GreengrapeException('Navigation Item text must be a string');
        }

        $text = self::translateOrderedName($text);

        // Replace any '-{lowercase letter}', so 'rss-feed' becomes 'rss feed'
        $text = preg_replace('/\-([a-z])/', ' $1', $text);

        // Uppercase first letters of each word, so 'rss feed' becomes 'Rss Feed'
        $text = ucwords($text);

        if ($text == '') {
            throw new GreengrapeException("Text cannot be blank. Input: '$text'");
        }

        $this->_text = $text;
        return $this;
    }

    /**
     * Get the link text
     *
     * @return string
     */
    public function getText()
    {
        return $this->_text;
    }

    /**
     * Set the href of the link
     *
     * @param string $href Href
     * @return \Greengrape\Navigation\Item
     */
    public function setHref($href)
    {
        if (!is_string($href)) {
            throw new GreengrapeException('Navigation Item href must be a string');
        }

        $this->setRawHref($href);

        $href = self::translateOrderedName($href);
        $this->_href = $href;
        return $this;
    }

    /**
     * Get the link href
     *
     * @return string
     */
    public function getHref($includeBase = false)
    {
        if ($includeBase) {
            if ($this->_href == '/') {
                // This prevents from doubling up the '/'
                return $this->getBaseUrl('/');
            }

            $pathParts = explode('/', rtrim($this->_href, '/'));
            $path = '';
            foreach ($pathParts as $part) {
                $path .= urlencode($part) . '/';
            }

            return $this->getBaseUrl('/' . $path);
        }

        return $this->_href;
    }

    /**
     * Set the actual raw folder name
     *
     * @param string $href Folder name
     * @return \Greengrape\Navigation\Item
     */
    public function setRawHref($href)
    {
        $this->_rawHref = $href;
        return $this;
    }

    /**
     * Get the actual raw folder name
     *
     * @return string
     */
    public function getRawHref()
    {
        return $this->_rawHref;
    }

    /**
     * Set whether this navigation is active
     *
     * @param bool $value active state
     * @return \Greengrape\Navigation\Item
     */
    public function setActive($value = true)
    {
        $this->_isActive = (bool) $value;
        return $this;
    }

    /**
     * Get whether link is active
     *
     * @return bool
     */
    public function getActive()
    {
        return $this->_isActive;
    }

    /**
     * Set Base URL
     *
     * @param string $url URL
     * @return \Greengrape\Navigation\Item
     */
    public function setBaseUrl($url)
    {
        // base url should not end in a slash, so we'll strip it off it it has
        // one on the end
        $this->_baseUrl = rtrim($url, '/');

        return $this;
    }

    /**
     * Get the base URL
     *
     * @return string
     */
    public function getBaseUrl($file = '')
    {
        if ($file == '') {
            return $this->_baseUrl;
        }

        return $this->_baseUrl . $file;
    }

    /**
     * Translate an ordered folder name
     *
     * If the folder starts with number and a dot, we will strip off the number
     * and the dot so it appears more normal in the listing
     *
     * @param string $text Folder name
     * @return string
     */
    public static function translateOrderedName($text)
    {
        // You can affect the order of the items by naming the folders with
        // n.<name>, where n is a number.
        // this: 01.services, 02.about-us
        if (preg_match('/[0-9]+\.(?:.*)/', $text, $matches)) {
            // Strip off the numbers and the dot
            $text = preg_replace('/[0-9]+\./', '', $text);
        }

        return $text;
    }
}
