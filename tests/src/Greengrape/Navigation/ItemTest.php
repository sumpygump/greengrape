<?php
/**
 * Item test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests\Navigation;

use Greengrape\Tests\BaseTestCase;
use Greengrape\Navigation\Item;
use Greengrape\Exception\GreengrapeException;

/**
 * Item Test
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class ItemTest extends BaseTestCase
{
    /**
     * Setup before tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->object = new Item('foobar', 'foobar', '/mybase/');
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
        $this->object->setText('xyz');

        $this->assertEquals('Xyz', $this->object->getText());
    }

    public function testSetTextWithNumberDot(): void
    {
        $this->object->setText('01.xyz');

        $this->assertEquals('Xyz', $this->object->getText());
    }

    public function testSetTextWithHyphen(): void
    {
        $this->object->setText('abc-xyz');

        $this->assertEquals('Abc Xyz', $this->object->getText());
    }

    public function testSetTextWithHyphenAndUppercase(): void
    {
        $this->object->setText('abc-Xyz');

        $this->assertEquals('Abc-Xyz', $this->object->getText());
    }

    public function testSetTextWithWords(): void
    {
        $this->object->setText(
            'When a strong man armed keepeth his palace, his goods are in peace'
        );

        $this->assertEquals(
            'When A Strong Man Armed Keepeth His Palace, His Goods Are In Peace',
            $this->object->getText()
        );
    }

    /**
     * testSetTextArray
     *
     * @return void
     */
    public function testSetTextArray(): void
    {
        $this->expectException(GreengrapeException::class);
        $this->object->setText(array('foobar'));
    }

    /**
     * testSetTextNumberDotBlank
     *
     * @return void
     */
    public function testSetTextNumberDotBlank(): void
    {
        $this->expectException(GreengrapeException::class);
        $this->object->setText('1.');
    }

    public function testSetTextNumberDotNumber(): void
    {
        $this->object->setText('18.1');

        $this->assertEquals('1', $this->object->getText());
    }

    public function testSetHref(): void
    {
        $this->object->setHref('application/');

        $this->assertEquals('application/', $this->object->getHref());
        $this->assertEquals('application/', $this->object->getRawHref());
    }

    public function testSetHrefWithNumberDot(): void
    {
        $this->object->setHref('18.application/');

        $this->assertEquals('application/', $this->object->getHref());
        $this->assertEquals('18.application/', $this->object->getRawHref());
    }

    /**
     * testSetHrefArray
     *
     * @return void
     */
    public function testSetHrefArray(): void
    {
        $this->expectException(GreengrapeException::class);
        $this->object->setHref(array('foobar'));

        $this->assertEquals('foobar', $this->object->getHref());
    }

    /**
     * testSetHrefInt
     *
     * @return void
     */
    public function testSetHrefInt(): void
    {
        $this->expectException(GreengrapeException::class);
        $this->object->setHref(1);
    }

    public function testGetHrefWithIncludeBase(): void
    {
        $this->object->setHref('happy-day/');

        $this->assertEquals('/mybase/happy-day/', $this->object->getHref(true));
    }

    public function testGetHrefRootWithIncludeBase(): void
    {
        $this->object->setHref('/');

        $this->assertEquals('/mybase/', $this->object->getHref(true));
    }

    public function testSetActiveEmpty(): void
    {
        $this->object->setActive();

        $this->assertTrue($this->object->getActive());
    }

    public function testSetActiveFalse(): void
    {
        $this->object->setActive(false);

        $this->assertFalse($this->object->getActive());
    }

    public function testGetBaseUrlEmpty(): void
    {
        $this->assertEquals('/mybase', $this->object->getBaseUrl());
    }
}
