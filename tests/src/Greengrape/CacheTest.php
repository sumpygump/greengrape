<?php
/**
 * Cache test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests;

use \Greengrape\Cache;
use \Greengrape\Exception\GreengrapeException;

/**
 * CacheTest
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class CacheTest extends BaseTestCase
{
    /**
     * Setup before tests
     *
     * @return void
     */
    public function setUp(): void
    {
        Cache::$allowExit = false;

        if (!file_exists('testCache')) {
            mkdir('testCache');
        }

        $this->object = new Cache('testCache');
    }

    /**
     * Tear down after tests
     *
     * @return void
     */
    public function tearDown(): void
    {
        if ($this->object instanceof Cache) {
            // clean up and hanging starts, just in case
            //$this->object->end();
        }

        $cmd = 'rm -rf testCache';
        passthru($cmd);
    }

    /**
     * testConstructNoArgs
     *
     * @return void
     */
    public function testConstructNoArgs()
    {
        $this->expectException(\ArgumentCountError::class);
        // @phpstan-ignore-next-line
        $cache = new Cache();
    }

    /**
     * testConstructFakeDir
     *
     * @return void
     */
    public function testConstructFakeDir()
    {
        $this->expectException(GreengrapeException::class);
        $cache = new Cache('fake');
    }

    public function testDisable(): void
    {
        $object = $this->object->disable();

        $this->assertEquals($this->object, $object);
        $this->assertFalse($this->object->start('ff'));
    }

    public function testEnable(): void
    {
        $object = $this->object->enable();

        ob_start();
        $this->assertEquals($this->object, $object);
        $this->assertTrue($this->object->start('ff'));
        ob_end_flush();

        $this->object->end();
    }

    /**
     * testSetDirectoryWhenPathIsntWritable
     *
     * @return void
     */
    public function testSetDirectoryWhenPathIsntWritable()
    {
        $this->expectException(GreengrapeException::class);
        $dir = '/';
        $this->object->setDirectory($dir);
    }

    public function testGetDirectory(): void
    {
        // This was set in setUp
        $dir = $this->object->getDirectory();

        $this->assertEquals('testCache', $dir);
    }

    public function testStartWithCachedFile(): void
    {
        // File is testCache/62738ce64df4624255f73c97123b596f.cache

        // Start the test output buffer capturerer
        ob_start();

        // Start cache capture
        $this->object->start('test1');

        // This content will be captured by the cache buffer
        echo 'sometestcontent';

        // End the cache capture
        $r = $this->object->end();

        // Retrieve contents from the output buffer
        $contents = ob_get_contents();

        // Clean the test output buffer capturer
        // (when the cache saved the contents to file it also output
        // them, so the test output buffer will capture the same content)
        ob_end_clean();

        $this->assertTrue($r);

        $filename     = 'testCache/62738ce64df4624255f73c97123b596f.cache';
        $fileContents = file_get_contents($filename);

        $this->assertEquals('sometestcontent', $fileContents, 'filecontents');
        $this->assertEquals('sometestcontent', $contents, 'contents');

        // Now that we have a cached file, let's run start again and ensure
        // that it reads the file, outputs it and stops

        // Start the test output buffer capturerer
        ob_start();

        // Start cache capture
        $this->object->start('test1');

        // Retrieve contents from the output buffer
        $contents = ob_get_contents();

        ob_end_clean();

        $expected = "<!-- Cached file -->\nsometestcontent";
        $this->assertEquals($expected, $contents);
    }

    public function testEndWhenDisabled(): void
    {
        $this->object->disable();

        $result = $this->object->end();
        $this->assertFalse($result);
    }

    public function testEndWhenNoFileToCache(): void
    {
        $result = $this->object->end();
        $this->assertFalse($result);
    }

    public function testClear(): void
    {
        // First populate the cache with some content
        ob_start();
        $this->object->start('t1');
        echo 'sometestcontent';
        $r = $this->object->end();
        ob_end_clean();

        ob_start();
        $this->object->start('t2');
        echo 'sometestcontent';
        $r = $this->object->end();
        ob_end_clean();

        // Now check that there are files in cache dir
        $files = glob('testCache/*.cache');
        $this->assertTrue((count($files) > 0));

        // Clear the cache
        $this->object->clear();

        // Now assert that there are no files in cache dir
        $files = glob('testCache/*.cache');
        $this->assertTrue((count($files) == 0));
    }

    public function testClearSingleFile(): void
    {
        // First populate the cache with some content
        ob_start();
        $this->object->start('t1');
        echo 'sometestcontent';
        $r = $this->object->end();
        ob_end_clean();

        ob_start();
        $this->object->start('t2');
        echo 'sometestcontent';
        $r = $this->object->end();
        ob_end_clean();

        // Ensure cache file for t2 request exists
        $this->assertTrue(file_exists('testCache/afe8d66c5d85259b61ed1b762f8b6a75.cache'));

        // Clear cache for t2
        $this->object->clear('t2');

        // Assert cache file for t2 request doesn't exist
        $this->assertFalse(file_exists('testCache/afe8d66c5d85259b61ed1b762f8b6a75.cache'));
    }
}
