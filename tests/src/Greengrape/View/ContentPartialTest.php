<?php
/**
 * ContentPartial test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests\View;

use Greengrape\View\ContentPartial;

/**
 * ContentPartial Test
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class ContentPartialTest extends \BaseTestCase
{
    /**
     * Setup before tests
     *
     * @return void
     */
    public function setUp()
    {
        $this->_object = new ContentPartial();
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
     * @return void
     */
    public function testConstruct()
    {
        $this->assertTrue($this->_object instanceof ContentPartial);
    }
}
