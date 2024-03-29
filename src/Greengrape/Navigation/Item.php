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
    protected $text = '';

    /**
     * The href of the navigation link
     *
     * @var string
     */
    protected $href = '';

    /**
     * The raw path name
     *
     * @var string
     */
    protected $rawHref = '';

    /**
     * Whether the link is currently active
     *
     * @var bool
     */
    protected $isActive = false;

    /**
     * Base URL
     *
     * @var string
     */
    protected $baseUrl = '';

    /**
     * Constructor
     *
     * @param string $text Text of the link
     * @param string $href Href of the link
     * @param string $baseUrl Base URL
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
     * @return Item
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

        $this->text = $text;
        return $this;
    }

    /**
     * Get the link text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set the href of the link
     *
     * @param string $href Href
     * @return Item
     */
    public function setHref($href)
    {
        if (!is_string($href)) {
            throw new GreengrapeException('Navigation Item href must be a string');
        }

        $this->setRawHref($href);

        $href = self::translateOrderedName($href);
        $this->href = $href;
        return $this;
    }

    /**
     * Get the link href
     *
     * @param bool $includeBase Whether to include the base
     * @return string
     */
    public function getHref($includeBase = false)
    {
        if ($includeBase) {
            if ($this->href == '/') {
                // This prevents from doubling up the '/'
                return $this->getBaseUrl('/');
            }

            $pathParts = explode('/', rtrim($this->href, '/'));
            $path = '';
            foreach ($pathParts as $part) {
                $path .= urlencode($part) . '/';
            }

            return $this->getBaseUrl('/' . $path);
        }

        return $this->href;
    }

    /**
     * Set the actual raw folder name
     *
     * @param string $href Folder name
     * @return Item
     */
    public function setRawHref($href)
    {
        $this->rawHref = $href;
        return $this;
    }

    /**
     * Get the actual raw folder name
     *
     * @return string
     */
    public function getRawHref()
    {
        return $this->rawHref;
    }

    /**
     * Set whether this navigation is active
     *
     * @param bool $value active state
     * @return Item
     */
    public function setActive($value = true)
    {
        $this->isActive = (bool) $value;
        return $this;
    }

    /**
     * Get whether link is active
     *
     * @return bool
     */
    public function getActive()
    {
        return $this->isActive;
    }

    /**
     * Set Base URL
     *
     * @param string $url URL
     * @return Item
     */
    public function setBaseUrl($url)
    {
        // base url should not end in a slash, so we'll strip it off it it has
        // one on the end
        $this->baseUrl = rtrim($url, '/');

        return $this;
    }

    /**
     * Get the base URL
     *
     * @param string $file File to append to path
     * @return string
     */
    public function getBaseUrl($file = '')
    {
        if ($file == '') {
            return $this->baseUrl;
        }

        return $this->baseUrl . $file;
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
