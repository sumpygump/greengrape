<?php
/**
 * Navigation item class file
 *
 * @package Greengrape
 */

namespace Greengrape;

/**
 * NavigationItem
 *
 * Represents an item in main or sub navigation
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class NavigationItem
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
     * @return \Greengrape\NavigationItem
     */
    public function setText($text)
    {
        $text = self::translateOrderedName($text);

        // Replace any '-{lowercase letter}', so 'rss-feed' becomes 'rss feed'
        $text = preg_replace('/\-([a-z])/', ' $1', $text);

        // Uppercase first letters of each word, so 'rss feed' becomes 'Rss Feed'
        $text = ucwords($text);

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
     * @return \Greengrape\NavigationItem
     */
    public function setHref($href)
    {
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
            return $this->getBaseUrl('/' . $this->_href);
        }

        return $this->_href;
    }

    /**
     * Set whether this navigation is active
     *
     * @param bool $value active state
     * @return \Greengrape\NavigationItem
     */
    public function setIsActive($value)
    {
        $this->_isActive = (bool) $value;
        return $this;
    }

    /**
     * Get whether link is active
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->_isActive;
    }

    /**
     * Set Base URL
     *
     * @param string $url URL
     * @return \Greengrape\Sitemap
     */
    public function setBaseUrl($url)
    {
        $this->_baseUrl = $url;
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
     * @return void
     */
    public static function translateOrderedName($text)
    {
        // You can affect the order of the items by naming the folders with 
        // n.<name>, where n is a number.
        // this: 01.services, 02.about-us
        if (preg_match('/^([0-9]+)\.(.*)/', $text, $matches)) {
            // We don't need to use this, it is already sorted correctly. 
            // We just need to strip off the numbers and we're good.
            $sort = $matches[1];
            $text = $matches[2];
        }

        return $text;
    }
}
