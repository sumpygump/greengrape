<?php
/**
 * Item test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests\Navigation;

use Greengrape\Navigation\Item;
use Greengrape\Exception\GreengrapeException;

/**
 * Item Test
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class ItemTest extends \BaseTestCase
{
    /**
     * Setup before tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->_object = new Item('foobar', 'foobar', '/mybase/');
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
        $item = new Item();
    }

    public function testConstructDefault(): void
    {
        $item = new Item('foobar', 'foobar/');

        $this->assertTrue($item instanceof Item);
        $this->assertEquals('Foobar', $item->getText());
        $this->assertEquals('foobar/', $item->getHref());
    }

    public function testSetText(): void
    {
        $this->_object->setText('xyz');

        $this->assertEquals('Xyz', $this->_object->getText());
    }

    public function testSetTextWithNumberDot(): void
    {
        $this->_object->setText('01.xyz');

        $this->assertEquals('Xyz', $this->_object->getText());
    }

    public function testSetTextWithHyphen(): void
    {
        $this->_object->setText('abc-xyz');

        $this->assertEquals('Abc Xyz', $this->_object->getText());
    }

    public function testSetTextWithHyphenAndUppercase(): void
    {
        $this->_object->setText('abc-Xyz');

        $this->assertEquals('Abc-Xyz', $this->_object->getText());
    }

    public function testSetTextWithWords(): void
    {
        $this->_object->setText('When a strong man armed keepeth his palace, his goods are in peace');

        $this->assertEquals('When A Strong Man Armed Keepeth His Palace, His Goods Are In Peace', $this->_object->getText());
    }

    /**
     * testSetTextArray
     *
     * @return void
     */
    public function testSetTextArray(): void
    {
        $this->expectException(GreengrapeException::class);
        $this->_object->setText(array('foobar'));
    }

    /**
     * testSetTextNumberDotBlank
     *
     * @return void
     */
    public function testSetTextNumberDotBlank(): void
    {
        $this->expectException(GreengrapeException::class);
        $this->_object->setText('1.');
    }

    public function testSetTextNumberDotNumber(): void
    {
        $this->_object->setText('18.1');

        $this->assertEquals('1', $this->_object->getText());
    }

    public function testSetHref(): void
    {
        $this->_object->setHref('application/');

        $this->assertEquals('application/', $this->_object->getHref());
        $this->assertEquals('application/', $this->_object->getRawHref());
    }

    public function testSetHrefWithNumberDot(): void
    {
        $this->_object->setHref('18.application/');

        $this->assertEquals('application/', $this->_object->getHref());
        $this->assertEquals('18.application/', $this->_object->getRawHref());
    }

    /**
     * testSetHrefArray
     *
     * @return void
     */
    public function testSetHrefArray(): void
    {
        $this->expectException(GreengrapeException::class);
        $this->_object->setHref(array('foobar'));

        $this->assertEquals('foobar', $this->_object->getHref());
    }

    /**
     * testSetHrefInt
     *
     * @return void
     */
    public function testSetHrefInt(): void
    {
        $this->expectException(GreengrapeException::class);
        $this->_object->setHref(1);
    }

    public function testGetHrefWithIncludeBase(): void
    {
        $this->_object->setHref('happy-day/');

        $this->assertEquals('/mybase/happy-day/', $this->_object->getHref(true));
    }

    public function testGetHrefRootWithIncludeBase(): void
    {
        $this->_object->setHref('/');

        $this->assertEquals('/mybase/', $this->_object->getHref(true));
    }

    public function testSetActiveEmpty(): void
    {
        $this->_object->setActive();

        $this->assertTrue($this->_object->getActive());
    }

    public function testSetActiveFalse(): void
    {
        $this->_object->setActive(false);

        $this->assertFalse($this->_object->getActive());
    }

    public function testGetBaseUrlEmpty(): void
    {
        $this->assertEquals('/mybase', $this->_object->getBaseUrl());
    }
}
