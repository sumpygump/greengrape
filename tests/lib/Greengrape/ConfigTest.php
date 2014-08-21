<?php

namespace Greengrape\Tests;

use Greengrape\Config;

class ConfigTest extends \BaseTestCase
{
    public function setUp()
    {
        $this->createConfigIni();
        $this->_object = new Config('testconfig.ini');
    }

    public function tearDown()
    {
        $this->deleteConfigIni();
    }

    /**
     * testConstruct
     *
     * @return void
     */
    public function testConstruct()
    {
        $config = new Config();

        $this->assertTrue($config instanceof Config);
    }

    /**
     * testConstructFileNoExist
     *
     * @expectedException Greengrape\Exception\GreengrapeException
     * @return void
     */
    public function testConstructFileNoExist()
    {
        $config = new Config('fake.ini');
    }

    public function testConstructRealFile()
    {
        $filename = 'testconfig.ini';
        $config = new Config($filename);

        $this->assertEquals('bar', $config['foo']);
        $this->assertEquals('[Greengrape]', $config['sitename']);
    }

    public function testGetExistingValue()
    {
        $this->assertEquals('bar', $this->_object->get('foo'));
    }

    public function testGetNoExist()
    {
        $this->assertNull($this->_object->get('chocolate'));
    }

    public function testGetWithObject()
    {
        $object = new \StdClass();
        $this->assertNull($this->_object->get($object));
    }

    public function testGetWithArray()
    {
        $array = array('1');
        $this->assertNull($this->_object->get($array));
    }

    public function testSet()
    {
        $this->_object->set('pie', 'pumpkin');
        $this->assertEquals('pumpkin', $this->_object->get('pie'));
    }

    public function testSetNull()
    {
        $this->_object->set(null, 'ok');

        $this->assertNull($this->_object->get(null));
    }

    public function testSetWithObject()
    {
        $object = new \StdClass();
        $this->assertFalse($this->_object->set($object, '11'));
    }

    public function testSetWithArray()
    {
        $array = array('11');
        $this->assertFalse($this->_object->set($array, '22'));
    }

    public function testSetAsArray()
    {
        $this->_object['soup'] = 'hot';

        $this->assertEquals('hot', $this->_object->get('soup'));
    }

    public function testSetNextAsArray()
    {
        $this->_object[] = 'next';
        $this->assertNull($this->_object[0]);
    }

    public function testOffsetUnset()
    {
        unset($this->_object['foo']);

        $this->assertNull($this->_object->get('foo'));
    }

    public function testOffsetUnsetOnNoExist()
    {
        unset($this->_object['hair']);
        $this->assertEquals('bar', $this->_object->get('foo'));
    }

    public function createConfigIni($contents = '')
    {
        $filename = 'testconfig.ini';

        if ($contents == '') {
            $contents = "foo=bar";
        }

        file_put_contents($filename, $contents);
    }

    public function deleteConfigIni()
    {
        $filename = 'testconfig.ini';
        unlink($filename);
    }
}
