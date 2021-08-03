<?php
/**
 * Handler test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests;

use Greengrape\Config;
use Greengrape\Csp;
use Greengrape\Exception\GreengrapeException;

/**
 * Handler Test
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class CspTest extends \BaseTestCase
{
    /**
     * Setup before tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $config = new Config();
        $config['csp'] = [];
        $this->_object = new Csp($config['csp']);
    }

    public function testConstruct(): void
    {
        $this->assertTrue($this->_object instanceof Csp);
    }

    public function testConstructWithInvalidArg(): void
    {
        $this->_object = new Csp(1);
        $this->assertTrue($this->_object instanceof Csp);
    }

    public function testConstructEmptyArray(): void
    {
        $this->_object = new Csp([]);
        $this->assertTrue($this->_object instanceof Csp);
    }

    public function testUseNonceFalse(): void
    {
        $config = ['use-nonce' => 0];
        $this->_object = new Csp($config);

        $this->assertEmpty($this->_object->getNonce());
        $this->assertNotContains('use-nonce', array_keys($this->_object->policies));
    }

    public function testUseNonceTrue(): void
    {
        $config = ['use-nonce' => 1];
        $this->_object = new Csp($config);

        $this->assertNotEmpty($this->_object->getNonce());
        $this->assertNotContains('use-nonce', array_keys($this->_object->policies));
    }

    public function testConstructScriptSrc(): void
    {
        $config = ['use-nonce' => 1, 'script-src' => 'self'];
        $this->_object = new Csp($config);

        $this->assertNotEmpty($this->_object->getNonce());
        $this->assertContains('script-src', array_keys($this->_object->policies));
    }

    public function testGenerateNonce(): void
    {
        $nonce = $this->_object->generateNonce(2);
        $this->assertEquals(2, strlen($nonce));
    }

    /**
     * testGenerateNonceMustBeGreaterThanZero
     *
     * @return void
     */
    public function testGenerateNonceMustBeGreaterThanZero(): void
    {
        $this->expectException(\RangeException::class);
        $nonce = $this->_object->generateNonce(0);
    }

    public function testGetAllPoliciesString(): void
    {
        $actual = $this->_object->getAllPoliciesString();
        $expected = "base-uri 'self'; default-src 'self'; img-src 'self'; object-src 'none'; script-src 'self' http: https: 'strict-dynamic'; style-src 'self' 'unsafe-inline';";
        $this->assertEquals($expected, $actual);
    }

    public function testGetAllPoliciesStringWithArrayDirectiveInConfig(): void
    {
        $config = ['script-src' => ['ab', 'cd']];
        $this->_object = new Csp($config);
        $actual = $this->_object->getAllPoliciesString();
        $expected = "base-uri 'self'; default-src 'self'; img-src 'self'; object-src 'none'; script-src ab cd; style-src 'self' 'unsafe-inline';";
        $this->assertEquals($expected, $actual);
    }

    public function testGetAllPoliciesOverridesDefaultDirectives(): void
    {
        $config = ['default-src' => "'none'"];
        $this->_object = new Csp($config);
        $actual = $this->_object->getAllPoliciesString();
        $expected = "base-uri 'self'; default-src 'none'; img-src 'self'; object-src 'none'; script-src 'self' http: https: 'strict-dynamic'; style-src 'self' 'unsafe-inline';";
        $this->assertEquals($expected, $actual);
    }

    public function testGetAllPoliciesIncludeNonceWhenRequested(): void
    {
        $config = ['use-nonce' => true];
        $this->_object = new Csp($config);
        $actual = $this->_object->getAllPoliciesString();
        $expected = "base-uri 'self'; default-src 'self'; img-src 'self'; object-src 'none'; script-src 'self' http: https: 'strict-dynamic' 'nonce-" . $this->_object->getNonce() . "' 'unsafe-inline'; style-src 'self' 'unsafe-inline';";
        $this->assertEquals($expected, $actual);
    }

    public function testGetAllPoliciesOnlyOutputsValidDirectives(): void
    {
        $config = ['cheeseburger-toppings' => 'all'];
        $this->_object = new Csp($config);
        $actual = $this->_object->getAllPoliciesString();
        $this->assertStringNotContainsString('cheeseburger-toppings', $actual);
    }

    /**
     * testRender
     *
     * @return void
     */
    public function testRender(): void
    {
        $this->expectException(GreengrapeException::class);
        $this->_object->render();
    }
}
