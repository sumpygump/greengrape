<?php

/**
 * Config class file
 *
 * @package Greengrape
 */

namespace Greengrape;

use ArrayAccess;
use Greengrape\Exception\GreengrapeException;

/**
 * Config
 *
 * Provides a simple config object where the params are accessible like an array.
 *
 * ```
 * $config = new Config();
 * $config->set('foo', 'bar');
 *
 * $value = $config['foo'];  // Result is 'bar'
 * ```
 *
 * Can read values from an ini file as argument of constructor
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @implements ArrayAccess<string, mixed>
 */
class Config implements ArrayAccess
{
    /**
     * Data storage for config settings
     *
     * @var array<string, mixed>
     */
    protected $data = [];

    /**
     * Default config settings
     *
     * @var array<string, string|bool>
     */
    protected $defaults = [
        'sitename' => '[Greengrape]',
        'theme' => 'grapeseed',
        'enable_cache' => true,
        'debug' => false,
    ];

    /**
     * Constructor
     *
     * @param string $configFile Filename
     * @return void
     */
    public function __construct($configFile = '')
    {
        $this->data = $this->defaults;
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

        $this->data = array_merge($this->data, $raw);
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

        return $this->data[$key];
    }

    /**
     * Set a config value
     *
     * @param mixed $key Key
     * @param mixed $value Value
     * @return bool
     */
    public function set($key, $value)
    {
        if (is_null($key) || !is_scalar($key)) {
            return false;
        }

        $this->data[$key] = $value;
        return true;
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
    public function offsetSet($offset, $value): void
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
    public function offsetExists(mixed $offset): bool
    {
        if (!is_scalar($offset)) {
            return false;
        }

        return isset($this->data[$offset]);
    }

    /**
     * Offset unset
     *
     * ArrayAccess interface
     *
     * @param string $offset Array key
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
    }

    /**
     * Offset get
     *
     * @param string $offset Array key
     * @return mixed
     */
    public function offsetGet($offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * Convert to array
     *
     * @return array<string, mixed>
     */
    public function toArray()
    {
        return $this->data;
    }
}
