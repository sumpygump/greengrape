<?php
/**
 * Request class file
 *
 * @package Greengrape
 */

namespace Greengrape;

/**
 * Request class
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class Request
{
    /**
     * Data for request values
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Base URL
     *
     * @var string
     */
    protected $_baseUrl = '';

    /**
     * Constructor
     *
     * @param array $requestInput Request values
     * @return void
     */
    public function __construct($requestInput = null)
    {
        if (null === $requestInput) {
            $requestInput = $_SERVER + $_GET;
        }

        $this->_data = $requestInput;
        $this->_baseUrl = $this->_detectWwwRoot();
    }

    /**
     * Get a value from the request
     *
     * @param string $name Name of key to fetch
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * Get a value from the request
     *
     * @param string $name Name of key to fetch
     * @return mixed
     */
    public function get($name)
    {
        if (isset($this->_data[$name])) {
            return $this->_data[$name];
        }

        return null;
    }

    /**
     * Get the requested file
     *
     * @return string
     */
    public function getRequestedFile()
    {
        $uriParts = parse_url($this->getRequestUri());
        $path     = $uriParts['path'];

        return str_replace($this->getBaseUrl(), '', $path);
    }

    /**
     * Get the request URI
     *
     * @return string
     */
    public function getRequestUri()
    {
        return $this->get('REQUEST_URI');
    }

    /**
     * Get the base URL
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_baseUrl;
    }

    /**
     * Detect the base url
     *
     * @return string
     */
    protected function _detectWwwRoot()
    {
        $baseUrl        = '';
        $filename       = $this->get('SCRIPT_FILENAME', '');
        $scriptName     = $this->get('SCRIPT_NAME');
        $phpSelf        = $this->get('PHP_SELF');
        $origScriptName = $this->get('ORIG_SCRIPT_NAME');

        if ($scriptName !== null && basename($scriptName) === $filename) {
            $baseUrl = $scriptName;
        } elseif ($phpSelf !== null && basename($phpSelf) === $filename) {
            $baseUrl = $phpSelf;
        } elseif ($origScriptName !== null && basename($origScriptName) === $filename) {
            // 1and1 shared hosting compatibility.
            $baseUrl = $origScriptName;
        } else {
            // Backtrack up the SCRIPT_FILENAME to find the portion
            // matching PHP_SELF.

            $baseUrl  = '/';
            $basename = basename($filename);
            if ($basename) {
                $path     = ($phpSelf ? trim($phpSelf, '/') : '');
                $baseUrl .= substr($path, 0, strpos($path, $basename)) . $basename;
            }
        }

        // Does the base URL have anything in common with the request URI?
        $requestUri = $this->getRequestUri();

        // Full base URL matches.
        if (0 === strpos($requestUri, $baseUrl)) {
            return $baseUrl;
        }

        // Directory portion of base path matches.
        $baseDir = str_replace('\\', '/', dirname($baseUrl));
        if (0 === strpos($requestUri, $baseDir)) {
            return $baseDir;
        }

        $truncatedRequestUri = $requestUri;

        if (false !== ($pos = strpos($requestUri, '?'))) {
            $truncatedRequestUri = substr($requestUri, 0, $pos);
        }

        $basename = basename($baseUrl);

        // No match whatsoever
        if (empty($basename) || false === strpos($truncatedRequestUri, $basename)) {
            return '';
        }

        // If using mod_rewrite or ISAPI_Rewrite strip the script filename
        // out of the base path. $pos !== 0 makes sure it is not matching a
        // value from PATH_INFO or QUERY_STRING.
        if (strlen($requestUri) >= strlen($baseUrl)
            && (false !== ($pos = strpos($requestUri, $baseUrl)) && $pos !== 0)
        ) {
            $baseUrl = substr($requestUri, 0, $pos + strlen($baseUrl));
        }

        return $baseUrl;
    }
}
