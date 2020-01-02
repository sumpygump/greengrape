<?php
/**
 * Location class file
 *
 * @package Greengrape
 */

namespace Greengrape;

/**
 * Location class
 *
 * Represents a location on the sitemap
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class Location
{
    /**
     * Filename to content file
     *
     * @var string
     */
    protected $_file = '';

    /**
     * Canonical URL
     *
     * @var string
     */
    protected $_canonical = '';

    /**
     * Constructor
     *
     * @param mixed $input Input for location
     * @return void
     */
    public function __construct($input)
    {
        if (is_array($input) && isset($input['canonical'])) {
            $this->setCanonical($input['canonical']);
        } else {
            $this->setFile($input);
        }
    }

    /**
     * Set canonical
     *
     * @param string $canonical Canonical URL
     * @return \Greengrape\Location
     */
    public function setCanonical($canonical)
    {
        $this->_canonical = $canonical;
        return $this;
    }

    /**
     * Get canonical URL
     *
     * @return string
     */
    public function getCanonical()
    {
        return $this->_canonical;
    }

    /**
     * Set file
     *
     * @param string $file Filename for content
     * @return \Greengrape\Location
     */
    public function setFile($file)
    {
        if (is_array($file)) {
            $file = reset($file);
        }
        $this->_file = (string) $file;
        return $this;
    }

    /**
     * Get content filename
     *
     * @return string
     */
    public function getFile()
    {
        // Don't return the leading slash
        return ltrim($this->_file, '/');
    }

    /**
     * To string magic method
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getFile();
    }
}
