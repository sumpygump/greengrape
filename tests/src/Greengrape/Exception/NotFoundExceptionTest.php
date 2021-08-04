<?php
/**
 * NotFoundException test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests\Exception;

use Greengrape\Tests\BaseTestCase;
use Greengrape\Exception\NotFoundException;

/**
 * NotFoundException Test
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class NotFoundExceptionTest extends BaseTestCase
{
    /**
     * Setup before tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->object = new NotFoundException();
    }

    /**
     * Test constructor
     *
     * @return void
     */
    public function testConstruct()
    {
        $this->assertTrue($this->object instanceof NotFoundException);
    }
}
