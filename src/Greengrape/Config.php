<?php
/**
 * Config class file
 *
 * @package Greengrape
 */

namespace Greengrape;

use \ArrayAccess;
use Greengrape\Exception\GreengrapeException;

/**
 * Config
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 */
class Config implements ArrayAccess
{
    /**
     * Data storage for config settings
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Default config settings
     *
     * @var array
     */
    protected $_defaults = array(
        'sitename'     => '[Greengrape]',
        'theme'        => 'grapeseed',
        'enable_cache' => true,
        'debug'        => false,
    );

    /**
     * Constructor
     *
     * @param string $configFile Filename
     * @return void
     */
    public function __construct($configFile = '')
    {
        $this->_data = $this->_defaults;
        if ($configFile != '') {
            $this->loadFile($configFile);
        }
    }

    /**
     * Load file
     *
     * @param string $filename Filename to ini file
     * @return \Greengrape\Config
     */
    public function loadFile($filename)
    {
        if (!file_exists($filename)) {
            throw new GreengrapeException("Config file does not exist or is not readable: '$filename'");
        }

        $raw = parse_ini_file($filename, true);

        $this->_data = array_merge($this->_data, $raw);
        return $this;
    }

    /**
     * Get config setting
     *
     * @param string $key Setting name
     * @return mixed
     */
    public function get($key)
    {
        if (!$this->offsetExists($key)) {
            return null;
        }

        return $this->_data[$key];
    }

    /**
     * Set a config value
     *
     * @param string $key Key
     * @param mixed $value Value
     * @return void
     */
    public function set($key, $value)
    {
        if (is_null($key) || !is_scalar($key)) {
            return false;
        }

        $this->_data[$key] = $value;
    }

    /**
     * Offset get
     *
     * ArrayAccess interface
     *
     * @param string $offset Array key offset
     * @param mixed $value Value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Offset exists
     *
     * ArrayAccess interface
     *
     * @param string $offset Array key
     * @return bool
     */
    public function offsetExists($offset)
    {
        if (!is_scalar($offset)) {
            return false;
        }

        return isset($this->_data[$offset]);
    }

    /**
     * Offset unset
     *
     * ArrayAccess interface
     *
     * @param string $offset Array key
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->_data[$offset]);
    }

    /**
     * Offset get
     *
     * @param string $offset Array key
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Convert to array
     *
     * @return array
     */
    public function toArray()
    {
        return $this->_data;
    }
}
