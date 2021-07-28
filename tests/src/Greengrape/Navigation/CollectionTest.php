<?php
/**
 * Collection test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests\Navigation;

use Greengrape\Navigation\Collection;

/**
 * Collection Test
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class CollectionTest extends \BaseTestCase
{
    /**
     * Setup before tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->_createTestContentDir();
        //$this->_object = new Collection();
    }

    /**
     * Tear down after tests
     *
     * @return void
     */
    public function tearDown(): void
    {
        passthru('rm -rf _testContent');
    }

    /**
     * Test constructor
     *
     * @return void
     */
    public function testConstructNoArgs()
    {
        $this->expectException(\ArgumentCountError::class);
        // @phpstan-ignore-next-line
        $collection = new Collection();
    }

    public function testConstructDefault()
    {
        $collection = new Collection('foo', 'bar');

        $this->assertTrue($collection instanceof Collection);
        $this->assertEquals(array(), $collection->toArray());
    }

    public function testPopulate()
    {
        $collection = new Collection('_testContent', '/momo/meme/');

        $items = $collection->toArray();

        $this->assertTrue($items[0] instanceof \Greengrape\Navigation\Item);
        $this->assertEquals('Home', $items[0]->getText());
        $this->assertTrue($items[1] instanceof \Greengrape\Navigation\Item);
        $this->assertEquals('Foo', $items[1]->getText());
    }

    public function testPopulateWithRootItemRoot()
    {
        $rootItem = new \Greengrape\Navigation\Item('root', '/', '/momo/meme/');

        // It should be empty array, because the root item is the root of the
        // site, so the main navigation is the same as the sub navigation
        $collection = new Collection('_testContent', '/momo/meme/', $rootItem);
        $this->assertEquals(array(), $collection->toArray());
    }

    public function testPopulateWithRootItem()
    {
        $rootItem = new \Greengrape\Navigation\Item('foo', 'foo/', '/momo/meme/');

        $collection = new Collection('_testContent', '/momo/meme/', $rootItem);

        $items = $collection->toArray();

        $this->assertTrue($items[0] instanceof \Greengrape\Navigation\Item);
        $this->assertEquals('Sub1', $items[0]->getText());
        $this->assertTrue($items[1] instanceof \Greengrape\Navigation\Item);
        $this->assertEquals('Sub2', $items[1]->getText());
    }

    public function testArrayIterate()
    {
        $collection = new Collection('_testContent', '/momo/meme/');

        // We'll iterate through the collection and save it to an array, the
        // contents of which we can test
        $items = array();
        foreach ($collection as $key => $item) {
            $items[] = $item;
        }

        $this->assertTrue($items[0] instanceof \Greengrape\Navigation\Item);
        $this->assertEquals('Home', $items[0]->getText());
        $this->assertTrue($items[1] instanceof \Greengrape\Navigation\Item);
        $this->assertEquals('Foo', $items[1]->getText());
    }

    protected function _createTestContentDir()
    {
        $contentDir = '_testContent';
        mkdir($contentDir);

        mkdir($contentDir . DIRECTORY_SEPARATOR . 'foo');
        mkdir($contentDir . DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR . 'sub1');
        mkdir($contentDir . DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR . 'sub2');
        mkdir($contentDir . DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR . '_subhidden');
        mkdir($contentDir . DIRECTORY_SEPARATOR . '_hidden');

        file_put_contents($contentDir . DIRECTORY_SEPARATOR . 'index.md', '#Hello There');
    }
}
