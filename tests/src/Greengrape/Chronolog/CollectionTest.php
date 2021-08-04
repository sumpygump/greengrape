<?php
/**
 * Handler test class file
 *
 * @package Greengrape
 */

namespace Greengrape\Tests;

use Greengrape\Chronolog\Collection;
use Greengrape\View;
use Greengrape\View\Content;
use Greengrape\View\Theme;

/**
 * Collection Test
 *
 * @package Greengrape
 * @author Jansen Price <jansen.price@gmail.com>
 * @version $Id$
 */
class CollectionTest extends BaseTestCase
{
    /**
     * view
     *
     * @var View
     */
    public $view;

    /**
     * Setup before tests
     *
     * @return void
     */
    public function setUp(): void
    {
        $theme = $this->createMock(Theme::class);
        $theme->method('getPath')
            ->willReturn('foobarx');

        $this->view = $this->createMock(View::class);
        $this->view->method('getTheme')
            ->willReturn($theme);

        mkdir('foobarx');
    }

    public function tearDown(): void
    {
        passthru('rm -rf foobarx');
    }

    public function testConstructNoArgs(): void
    {
        $this->expectException(\ArgumentCountError::class);
        // @phpstan-ignore-next-line
        $collection = new Collection();
    }

    public function testConstructOneArgOnly(): void
    {
        $this->expectException(\ArgumentCountError::class);
        // @phpstan-ignore-next-line
        $collection = new Collection('.');
    }

    public function testConstructOkay(): void
    {
        $collection = new Collection('.', $this->view);
        $this->assertInstanceOf(Collection::class, $collection);
    }

    public function testPopulate(): void
    {
        $example_files = [
            '01-foo.txt',
            '02-bar.txt',
            'three-teehee.txt',
            '04-lemming.txt',
        ];

        foreach ($example_files as $filename) {
            file_put_contents($filename, 'CONTENT');
        }

        $collection = new Collection('.', $this->view);
        $items = $collection->toArray();
        $expected = 3;
        $this->assertEquals($expected, count($items));
        $this->assertInstanceOf(Content::class, $items[0]);

        foreach ($example_files as $filename) {
            unlink($filename);
        }
    }

    public function testReverse(): void
    {
        $example_files = [
            '01-foo.txt',
            '02-bar.txt',
            '04-lemming.txt',
        ];

        foreach ($example_files as $filename) {
            file_put_contents($filename, 'CONTENT');
        }

        $collection = new Collection('.', $this->view);
        $collection->reverse();
        $items = $collection->toArray();
        $this->assertEquals('./04-lemming.txt', $items[0]->getFile());

        foreach ($example_files as $filename) {
            unlink($filename);
        }
    }

    public function testAddItems(): void
    {
        $collection = new Collection('.', $this->view);
        $items = $collection->toArray();
        $this->assertEquals([], $items);

        // @phpstan-ignore-next-line
        $collection->addItems(['x']);
        $items = $collection->toArray();
        $this->assertEquals(['x'], $items);
    }

    public function testIterate(): void
    {
        $example_files = [
            '01-foo.txt',
            '02-bar.txt',
            '04-lemming.txt',
        ];

        foreach ($example_files as $filename) {
            file_put_contents($filename, 'CONTENT-' . $filename);
        }

        $collection = new Collection('.', $this->view);
        $expected = [
            'CONTENT-01-foo.txt',
            'CONTENT-02-bar.txt',
            'CONTENT-04-lemming.txt',
        ];
        $actual = [];
        foreach ($collection as $key => $item) {
            $actual[$key] = $item->getContent();
        }

        $this->assertEquals($expected, $actual);

        foreach ($example_files as $filename) {
            unlink($filename);
        }
    }
}
