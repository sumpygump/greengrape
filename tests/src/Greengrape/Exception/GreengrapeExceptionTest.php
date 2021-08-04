<?php
/**
 * GreengrapeException test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests\Exception;

use Greengrape\Tests\BaseTestCase;
use Greengrape\Exception\GreengrapeException;

/**
 * GreengrapeException Test
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class GreengrapeExceptionTest extends BaseTestCase
{
    /**
     * Setup before tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->object = new GreengrapeException();
    }

    /**
     * Test constructor
     *
     * @return void
     */
    public function testConstruct()
    {
        $this->object = new GreengrapeException();

        $this->assertTrue($this->object instanceof GreengrapeException);
    }
}
