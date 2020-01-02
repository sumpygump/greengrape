<?php
/**
 * Item test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests\Navigation;

use Greengrape\Navigation\Item;

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
    public function setUp()
    {
        $this->_object = new Item('foobar', 'foobar', '/mybase/');
    }

    /**
     * Tear down after tests
     *
     * @return void
     */
    public function tearDown()
    {
    }

    /**
     * Test constructor
     *
     * @expectedException ArgumentCountError
     * @return void
     */
    public function testConstructNoArgs()
    {
        $item = new Item();
    }

    public function testConstructDefault()
    {
        $item = new Item('foobar', 'foobar/');

        $this->assertTrue($item instanceof Item);
        $this->assertEquals('Foobar', $item->getText());
        $this->assertEquals('foobar/', $item->getHref());
    }

    public function testSetText()
    {
        $this->_object->setText('xyz');

        $this->assertEquals('Xyz', $this->_object->getText());
    }

    public function testSetTextWithNumberDot()
    {
        $this->_object->setText('01.xyz');

        $this->assertEquals('Xyz', $this->_object->getText());
    }

    public function testSetTextWithHyphen()
    {
        $this->_object->setText('abc-xyz');

        $this->assertEquals('Abc Xyz', $this->_object->getText());
    }

    public function testSetTextWithHyphenAndUppercase()
    {
        $this->_object->setText('abc-Xyz');

        $this->assertEquals('Abc-Xyz', $this->_object->getText());
    }

    public function testSetTextWithWords()
    {
        $this->_object->setText('When a strong man armed keepeth his palace, his goods are in peace');

        $this->assertEquals('When A Strong Man Armed Keepeth His Palace, His Goods Are In Peace', $this->_object->getText());
    }

    /**
     * testSetTextArray
     *
     * @expectedException Greengrape\Exception\GreengrapeException
     * @return void
     */
    public function testSetTextArray()
    {
        $this->_object->setText(array('foobar'));
    }

    /**
     * testSetTextNumberDotBlank
     *
     * @expectedException Greengrape\Exception\GreengrapeException
     * @return void
     */
    public function testSetTextNumberDotBlank()
    {
        $this->_object->setText('1.');
    }

    public function testSetTextNumberDotNumber()
    {
        $this->_object->setText('18.1');

        $this->assertEquals('1', $this->_object->getText());
    }

    public function testSetHref()
    {
        $this->_object->setHref('application/');

        $this->assertEquals('application/', $this->_object->getHref());
        $this->assertEquals('application/', $this->_object->getRawHref());
    }

    public function testSetHrefWithNumberDot()
    {
        $this->_object->setHref('18.application/');

        $this->assertEquals('application/', $this->_object->getHref());
        $this->assertEquals('18.application/', $this->_object->getRawHref());
    }

    /**
     * testSetHrefArray
     *
     * @expectedException Greengrape\Exception\GreengrapeException
     * @return void
     */
    public function testSetHrefArray()
    {
        $this->_object->setHref(array('foobar'));

        $this->assertEquals('foobar', $this->_object->getHref());
    }

    /**
     * testSetHrefInt
     *
     * @expectedException Greengrape\Exception\GreengrapeException
     * @return void
     */
    public function testSetHrefInt()
    {
        $this->_object->setHref(1);
    }

    public function testGetHrefWithIncludeBase()
    {
        $this->_object->setHref('happy-day/');

        $this->assertEquals('/mybase/happy-day/', $this->_object->getHref(true));
    }

    public function testGetHrefRootWithIncludeBase()
    {
        $this->_object->setHref('/');

        $this->assertEquals('/mybase/', $this->_object->getHref(true));
    }

    public function testSetActiveEmpty()
    {
        $this->_object->setActive();

        $this->assertTrue($this->_object->getActive());
    }

    public function testSetActiveFalse()
    {
        $this->_object->setActive(false);

        $this->assertFalse($this->_object->getActive());
    }

    public function testGetBaseUrlEmpty()
    {
        $this->assertEquals('/mybase', $this->_object->getBaseUrl());
    }
}
