<?php
/**
 * Base Test Case class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Base Test Case
 *
 * @uses PHPUnit_Framework_TestCase
 * @package Greengrape
 * @subpackage Tests
 * @author Jansen Price <jansen.price@gmail.com>
 */
class BaseTestCase extends TestCase
{
    /**
     * Storage of object being tested
     *
     * @var object
     */
    protected $object;
}
