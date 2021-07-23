<?php
/**
 * AssetManager test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests\View;

use Greengrape\View\AssetManager;

/**
 * AssetManager Test
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class AssetManagerTest extends \BaseTestCase
{
    /**
     * Setup before tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->_object = new AssetManager('testtheme');
    }

    /**
     * Test constructor
     *
     * @return void
     */
    public function testConstructNoArgs()
    {
        $this->expectException(\ArgumentCountError::class);
        $assetManager = new AssetManager();
    }

    public function testConstructDefault()
    {
        $assetManager = new AssetManager('foobar1');

        $this->assertTrue($assetManager instanceof AssetManager);
    }

    public function testSetBaseUrl()
    {
        $this->_object->setBaseUrl('/anotherBase');

        $this->assertEquals('/anotherBase/', $this->_object->getBaseUrl());
        $this->assertEquals('/anotherBase/a', $this->_object->getBaseUrl('a'));
    }

    public function testSetBaseUrlSlashAtEnd()
    {
        $this->_object->setBaseUrl('/anotherBase/');

        $this->assertEquals('/anotherBase/', $this->_object->getBaseUrl());
        $this->assertEquals('/anotherBase/test1', $this->_object->getBaseUrl('test1'));
    }

    public function testGetThemeBaseUrl()
    {
        $this->assertEquals('/themes/testtheme/', $this->_object->getThemeBaseUrl());
    }

    public function testFile()
    {
        $this->assertEquals('/themes/testtheme/css/a1.css', $this->_object->file('css/a1'));
    }

    public function testGetFilePathNoAssetRoot()
    {
        $file = $this->_object->getFilePath('a1');

        $this->assertEquals('/themes/testtheme/a1', $file);
    }

    public function testGetFilePathWithExtension()
    {
        $file = $this->_object->getFilePath('a1.css');

        $this->assertEquals('/themes/testtheme/a1.css', $file);
    }

    public function testGetFilePathWithUnknownExtension()
    {
        $file = $this->_object->getFilePath('a1.partytime');

        $this->assertEquals('/themes/testtheme/a1.partytime', $file);
    }

    public function testGetFilePathWithUnsupportedAssetRoot()
    {
        $file = $this->_object->getFilePath('ggg/a2');

        $this->assertEquals('/themes/testtheme/ggg/a2', $file);
    }
}
