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
 */
class Location
{
    /**
     * Filename to content file
     *
     * @var string
     */
    protected $file = '';

    /**
     * Canonical URL
     *
     * @var string
     */
    protected $canonical = '';

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
     * @return Location
     */
    public function setCanonical($canonical)
    {
        $this->canonical = $canonical;
        return $this;
    }

    /**
     * Get canonical URL
     *
     * @return string
     */
    public function getCanonical()
    {
        return $this->canonical;
    }

    /**
     * Set file
     *
     * @param string|array<int, string> $file Filename for content
     * @return Location
     */
    public function setFile($file)
    {
        if (is_array($file)) {
            $file = reset($file);
        }
        $this->file = (string) $file;
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
        return ltrim($this->file, '/');
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
