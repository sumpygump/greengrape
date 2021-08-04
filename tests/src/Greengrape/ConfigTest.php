<?php

namespace Greengrape\Tests;

use Greengrape\Config;
use Greengrape\Exception\GreengrapeException;

class ConfigTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->createConfigIni();
        $this->object = new Config('testconfig.ini');
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
        $this->assertEquals('bar', $this->object->get('foo'));
    }

    public function testGetNoExist(): void
    {
        $this->assertNull($this->object->get('chocolate'));
    }

    public function testGetWithObject(): void
    {
        $object = new \StdClass();
        $this->assertNull($this->object->get($object));
    }

    public function testGetWithArray(): void
    {
        $array = array('1');
        $this->assertNull($this->object->get($array));
    }

    public function testSet(): void
    {
        $this->object->set('pie', 'pumpkin');
        $this->assertEquals('pumpkin', $this->object->get('pie'));
    }

    public function testSetNull(): void
    {
        $this->object->set(null, 'ok');
        $this->assertNull($this->object[null]);
    }

    public function testSetWithObject(): void
    {
        $object = new \StdClass();
        $this->assertFalse($this->object->set($object, '11'));
    }

    public function testSetWithArray(): void
    {
        $array = array('11');
        $this->assertFalse($this->object->set($array, '22'));
    }

    public function testSetAsArray(): void
    {
        $this->object['soup'] = 'hot';

        $this->assertEquals('hot', $this->object->get('soup'));
    }

    public function testSetNextAsArray(): void
    {
        $this->object[] = 'next';
        $this->assertNull($this->object[0]);
    }

    public function testOffsetUnset(): void
    {
        unset($this->object['foo']);

        $this->assertNull($this->object->get('foo'));
    }

    public function testOffsetUnsetOnNoExist(): void
    {
        unset($this->object['hair']);
        $this->assertEquals('bar', $this->object->get('foo'));
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
