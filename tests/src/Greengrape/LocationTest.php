<?php
/**
 * Location test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests;

use Greengrape\Location;

/**
 * Location Test
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class LocationTest extends BaseTestCase
{
    /**
     * Setup before tests
     *
     * @return void
     */
    public function setUp(): void
    {
        //$this->object = new Location();
    }

    /**
     * Test constructor
     *
     * @return void
     */
    public function testConstructNoArgs(): void
    {
        $this->expectException(\ArgumentCountError::class);
        // @phpstan-ignore-next-line
        $location = new Location();
    }

    public function testConstructEmptyArray(): void
    {
        $location = new Location(array());

        $this->assertEquals('', $location->getFile());
    }

    public function testConstructArrayWithValue(): void
    {
        $location = new Location(array('foo' => 'bar'));

        $this->assertEquals('bar', $location->getFile());
    }

    public function testConstructInteger(): void
    {
        $location = new Location(2);

        $this->assertEquals('2', $location->getFile());
    }

    public function testConstructString(): void
    {
        $location = new Location('foobar');

        $this->assertEquals('foobar', $location->getFile());
    }

    public function testConstructArrayWithCanonical(): void
    {
        $location = new Location(array('canonical' => 'maka'));

        $this->assertEquals('', $location->getFile());
        $this->assertEquals('maka', $location->getCanonical());
    }

    public function testToString(): void
    {
        $location = new Location('foobar');

        $this->assertEquals('foobar', $location->__toString());
    }
}
