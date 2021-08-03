<?php

namespace Greengrape\Tests;

use Greengrape\Config;
use Greengrape\Exception\GreengrapeException;

class ConfigTest extends \BaseTestCase
{
    public function setUp(): void
    {
        $this->createConfigIni();
        $this->_object = new Config('testconfig.ini');
    }

    public function tearDown(): void
    {
        $this->deleteConfigIni();
    }

    /**
     * testConstruct
     *
     * @return void
     */
    public function testConstruct(): void
    {
        $config = new Config();

        $this->assertTrue($config instanceof Config);
    }

    /**
     * testConstructFileNoExist
     *
     * @return void
     */
    public function testConstructFileNoExist(): void
    {
        $this->expectException(GreengrapeException::class);
        $config = new Config('fake.ini');
    }

    public function testConstructRealFile(): void
    {
        $filename = 'testconfig.ini';
        $config = new Config($filename);

        $this->assertEquals('bar', $config['foo']);
        $this->assertEquals('[Greengrape]', $config['sitename']);
    }

    public function testGetExistingValue(): void
    {
        $this->assertEquals('bar', $this->_object->get('foo'));
    }

    public function testGetNoExist(): void
    {
        $this->assertNull($this->_object->get('chocolate'));
    }

    public function testGetWithObject(): void
    {
        $object = new \StdClass();
        $this->assertNull($this->_object->get($object));
    }

    public function testGetWithArray(): void
    {
        $array = array('1');
        $this->assertNull($this->_object->get($array));
    }

    public function testSet(): void
    {
        $this->_object->set('pie', 'pumpkin');
        $this->assertEquals('pumpkin', $this->_object->get('pie'));
    }

    public function testSetNull(): void
    {
        $this->_object->set(null, 'ok');
        $this->assertNull($this->_object[null]);
    }

    public function testSetWithObject(): void
    {
        $object = new \StdClass();
        $this->assertFalse($this->_object->set($object, '11'));
    }

    public function testSetWithArray(): void
    {
        $array = array('11');
        $this->assertFalse($this->_object->set($array, '22'));
    }

    public function testSetAsArray(): void
    {
        $this->_object['soup'] = 'hot';

        $this->assertEquals('hot', $this->_object->get('soup'));
    }

    public function testSetNextAsArray(): void
    {
        $this->_object[] = 'next';
        $this->assertNull($this->_object[0]);
    }

    public function testOffsetUnset(): void
    {
        unset($this->_object['foo']);

        $this->assertNull($this->_object->get('foo'));
    }

    public function testOffsetUnsetOnNoExist(): void
    {
        unset($this->_object['hair']);
        $this->assertEquals('bar', $this->_object->get('foo'));
    }

    /**
     * createConfigIni
     *
     * @param string $contents Contents to populate in ini file
     * @return void
     */
    public function createConfigIni($contents = ''): void
    {
        $filename = 'testconfig.ini';

        if ($contents == '') {
            $contents = "foo=bar";
        }

        file_put_contents($filename, $contents);
    }

    public function deleteConfigIni(): void
    {
        $filename = 'testconfig.ini';
        unlink($filename);
    }
}
