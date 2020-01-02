<?php
/**
 * Handler test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests;

use Greengrape\Config;
use Greengrape\Csp;

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
    public function setUp()
    {
        $config = new Config();
        $config['csp'] = [];
        $this->_object = new Csp($config['csp']);
    }

    /**
     * Tear down after tests
     *
     * @return void
     */
    public function tearDown()
    {
    }

    public function testConstruct()
    {
        $this->assertTrue($this->_object instanceof Csp);
    }

    public function testConstructWithInvalidArg()
    {
        $this->_object = new Csp(1);
        $this->assertTrue($this->_object instanceof Csp);
    }

    public function testConstructEmptyArray()
    {
        $this->_object = new Csp([]);
        $this->assertTrue($this->_object instanceof Csp);
    }

    public function testUseNonceFalse()
    {
        $config = ['use-nonce' => 0];
        $this->_object = new Csp($config);

        $this->assertEmpty($this->_object->getNonce());
        $this->assertNotContains('use-nonce', array_keys($this->_object->policies));
    }

    public function testUseNonceTrue()
    {
        $config = ['use-nonce' => 1];
        $this->_object = new Csp($config);

        $this->assertNotEmpty($this->_object->getNonce());
        $this->assertNotContains('use-nonce', array_keys($this->_object->policies));
    }

    public function testConstructScriptSrc()
    {
        $config = ['use-nonce' => 1, 'script-src' => 'self'];
        $this->_object = new Csp($config);

        $this->assertNotEmpty($this->_object->getNonce());
        $this->assertContains('script-src', array_keys($this->_object->policies));
    }

    public function testGenerateNonce()
    {
        $nonce = $this->_object->generateNonce(2);
        $this->assertEquals(2, strlen($nonce));
    }

    /**
     * testGenerateNonceMustBeGreaterThanZero
     *
     * @expectedException RangeException
     * @return void
     */
    public function testGenerateNonceMustBeGreaterThanZero()
    {
        $nonce = $this->_object->generateNonce(0);
    }

    public function testGetAllPoliciesString()
    {
        $actual = $this->_object->getAllPoliciesString();
        $expected = "base-uri 'self'; default-src 'self'; img-src 'self'; object-src 'none'; script-src 'self' http: https: 'strict-dynamic'; style-src 'self';";
        $this->assertEquals($expected, $actual);
    }

    public function testGetAllPoliciesStringWithArrayDirectiveInConfig()
    {
        $config = ['script-src' => ['ab', 'cd']];
        $this->_object = new Csp($config);
        $actual = $this->_object->getAllPoliciesString();
        $expected = "base-uri 'self'; default-src 'self'; img-src 'self'; object-src 'none'; script-src ab cd; style-src 'self';";
        $this->assertEquals($expected, $actual);
    }

    public function testGetAllPoliciesOverridesDefaultDirectives()
    {
        $config = ['default-src' => "'none'"];
        $this->_object = new Csp($config);
        $actual = $this->_object->getAllPoliciesString();
        $expected = "base-uri 'self'; default-src 'none'; img-src 'self'; object-src 'none'; script-src 'self' http: https: 'strict-dynamic'; style-src 'self';";
        $this->assertEquals($expected, $actual);
    }

    public function testGetAllPoliciesIncludeNonceWhenRequested()
    {
        $config = ['use-nonce' => true];
        $this->_object = new Csp($config);
        $actual = $this->_object->getAllPoliciesString();
        $expected = "base-uri 'self'; default-src 'self'; img-src 'self'; object-src 'none'; script-src 'self' http: https: 'strict-dynamic' 'nonce-" . $this->_object->getNonce() . "' 'unsafe-inline'; style-src 'self';";
        $this->assertEquals($expected, $actual);
    }

    public function testGetAllPoliciesOnlyOutputsValidDirectives()
    {
        $config = ['cheeseburger-toppings' => 'all'];
        $this->_object = new Csp($config);
        $actual = $this->_object->getAllPoliciesString();
        $this->assertNotContains('cheeseburger-toppings', $actual);
    }

    /**
     * testRender
     *
     * @expectedException \Greengrape\Exception\GreengrapeException
     * @return void
     */
    public function testRender()
    {
        $this->_object->render();
    }
}
