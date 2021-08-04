<?php
/**
 * Kernel test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests;

use Greengrape\Kernel;
use Greengrape\Config;
use Greengrape\Exception\GreengrapeException;

/**
 * Kernel Test
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class KernelTest extends BaseTestCase
{
    /**
     * setUpBeforeClass
     *
     * @return void
     */
    public static function setUpBeforeClass(): void
    {
        self::createConfigIni();
    }

    /**
     * Setup before tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $config = new Config('testconfig.ini');

        $this->object = new Kernel($config);
    }

    /**
     * tearDownAfterClass
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        self::deleteConfigIni();
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
        $kernel = new Kernel();
    }

    public function testConstruct(): void
    {
        $config = new Config('testconfig.ini');

        $kernel = new Kernel($config);

        $this->assertTrue($kernel instanceof Kernel);
    }

    public function testSetConfig(): void
    {
        $config = array(
            'a' => 'b',
        );

        $this->object->setConfig($config);

        $this->assertEquals('b', $this->object->getConfig('a'));
        $this->assertEquals('grapeseed', $this->object->getConfig('theme'));
    }

    public function testGetConfig(): void
    {
        $config = array(
            'a' => 'b',
        );

        $this->object->setConfig($config);

        $config['theme'] = 'grapeseed';

        $this->assertEquals($config, $this->object->getConfig());
        $this->assertNull($this->object->getConfig('notsetvalue'));
    }

    public function testExecute(): void
    {
        Kernel::$allowExit = false;
        ob_start();
        $this->object->execute();
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertStringContainsString('<title>', $contents);
        $this->assertStringContainsString('</body>', $contents);
    }

    /**
     * testRedirect
     *
     * @return void
     */
    public function testRedirect(): void
    {
        $this->expectException(GreengrapeException::class);
        $this->object->redirect('0');
    }

    public function testSafeExit(): void
    {
        Kernel::$allowExit = false;
        Kernel::safeExit();

        $this->assertTrue(true);
    }

    /**
     * createConfigIni
     *
     * @param string $contents
     * @return void
     */
    public static function createConfigIni($contents = ''): void
    {
        $filename = 'testconfig.ini';

        if ($contents == '') {
            $contents = "foo=bar\nenable_cache=false";
        }

        file_put_contents($filename, $contents);
    }

    /**
     * deleteConfigIni
     *
     * @return void
     */
    public static function deleteConfigIni(): void
    {
        $filename = 'testconfig.ini';
        unlink($filename);
    }
}
