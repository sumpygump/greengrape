<?php

namespace Greengrape;

use Greengrape\Exception\GreengrapeException;

class Csp
{
    const DIRECTIVES = [
        "child-src",
        "connect-src",
        "default-src",
        "font-src",
        "frame-src",
        "img-src",
        "manifest-src",
        "media-src",
        "prefetch-src",
        "object-src",
        "script-src",
        "script-src-elem",
        "script-src-attr",
        "style-src",
        "style-src-elem",
        "style-src-attr",
        "worker-src",
        "base-uri",
        "plugin-types",
        "sandbox",
        "form-action",
        "frame-ancestors",
        "navigate-to",
        "report-uri",
        "report-to",
    ];

    /**
     * Default policies set
     *
     * These are policies suggested by https://csp-evaluator.withgoogle.com/
     *
     * @var array
     */
    public $policies = [
        "base-uri" => "'self'",
        "default-src" => "'self'",
        "img-src" => "'self'",
        "object-src" => "'none'",
        "script-src" => "'self' http: https: 'strict-dynamic'",
        "style-src" => "'self' 'unsafe-inline'",
    ];

    private $use_nonce = false;

    private $nonce = '';

    public function __construct($csp_config = [])
    {
        if (!$csp_config instanceof ArrayAccess
            && !is_array($csp_config)
        ) {
            $csp_config = [];
        }

        if ($csp_config == null) {
            $csp_config = [];
        }

        if (isset($csp_config['use-nonce'])) {
            $this->use_nonce = (bool) $csp_config['use-nonce'];
            unset($csp_config['use-nonce']);
        }

        if ($this->use_nonce) {
            $this->nonce = $this->generateNonce(16);
            $nonce_policy = sprintf(" 'nonce-%s'", $this->nonce);

            if (!isset($csp_config['script-src'])) {
                // Pull from default
                $csp_config['script-src'] = $this->policies['script-src'];
            }

            // Adding 'unsafe-inline' too (ignored by browsers supporting
            // nonces/hashes) to be backward compatible with older browsers.
            if (is_array($csp_config['script-src'])) {
                $csp_config['script-src'][] = $nonce_policy;
                if (!in_array("'unsafe-inline'", $csp_config['script-src'])) {
                    $csp_config['script-src'][] = "'unsafe-inline'";
                }
            } else {
                $csp_config['script-src'] .= $nonce_policy;
                if (strpos($csp_config['script-src'], 'unsafe-inline') === false) {
                    $csp_config['script-src'] .= " 'unsafe-inline'";
                }
            }

        }

        $this->policies = array_merge($this->policies, $csp_config);
        $this->removeInvalidDirectives();
    }

    public function removeInvalidDirectives()
    {
        $valid_policies = [];
        foreach ($this->policies as $directive => $value) {
            if (in_array($directive, self::DIRECTIVES)) {
                $valid_policies[$directive] = $value;
            }
        }

        $this->policies = $valid_policies;
    }

    /**
     * Generate a random string, using a cryptographically secure
     * pseudorandom number generator (random_int)
     *
     * For PHP 7, random_int is a PHP core function
     * For PHP 5.x, depends on https://github.com/paragonie/random_compat
     *
     * @param int $length How many characters do we want?
     * @param string $keyspace A string of all possible characters to select from
     * @return string
     */
    public function generateNonce(
        int $length = 64,
        string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
    ) {
        if ($length < 1) {
            throw new \RangeException("Length must be a positive integer");
        }

        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;

        for ($i = 0; $i < $length; ++$i) {
            $pieces[] = $keyspace[random_int(0, $max)];
        }

        return implode('', $pieces);
    }

    /**
     * Get previously generated nonce
     *
     * @return string
     */
    public function getNonce()
    {
        return $this->nonce;
    }

    /**
     * Get all policies as a string prepared for the header
     *
     * @return string
     */
    public function getAllPoliciesString()
    {
        $policies = [];

        foreach ($this->policies as $directive => $value) {
            $directive = strtolower($directive);

            if (is_array($value)) {
                $value = implode(' ', $value);
            }

            $policies[] = sprintf("%s %s;", $directive, $value);
        }

        return implode(' ', $policies);
    }

    /**
     * Render out the content security policy header
     *
     * @return void
     */
    public function render()
    {
        if (headers_sent() || PHP_SAPI == 'cli') {
            throw new GreengrapeException("Headers already sent, cannot send CSP header");
        }

        header("Content-Security-Policy: " . $this->getAllPoliciesString());
    }
}
