<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Layout;

use Bluz\Tests\TestCase;
use Bluz\Layout\Layout;

/**
 * LayoutTest
 *
 * @package  Bluz\Tests\View
 *
 * @author   Anton Shevchuk
 * @created  22.08.2014 17:09
 */
class LayoutTest extends TestCase
{
    /**
     * Test Content
     */
    public function testSetContent()
    {
        $layout = new Layout();
        $layout->setContent('foo');

        $this->assertEquals('foo', $layout->getContent());
    }

    /**
     * Test Callable Content
     */
    public function testSetCallableContent()
    {
        $layout = new Layout();
        $layout->setContent(function () {
            return 'foo';
        });

        $this->assertEquals('foo', $layout->getContent());
    }

    /**
     * Test Callable Content throw any Exception
     */
    public function testSetInvalidCallableContent()
    {
        $layout = new Layout();
        $layout->setContent(function () {
            throw new \Exception('foo');
        });

        $this->assertEquals('foo', $layout->getContent());
    }

    /**
     * Helper Breadcrumbs
     */
    public function testHelperBreadcrumbs()
    {
        $layout = new Layout();

        $layout->breadCrumbs(['foo' => 'bar']);

        $this->assertEqualsArray(['foo' => 'bar'], $layout->breadCrumbs());
    }
    /**
     * Helper Link
     */
    public function testHelperLink()
    {
        $layout = new Layout();

        $layout->link(['href'=>'foo.css', 'rel' => "stylesheet", 'media' => "all"]);
        $layout->link(['href'=>'favicon.ico', 'rel' => 'shortcut icon']);

        $result = $layout->link();

        $this->assertEquals(
            '<link href="foo.css" rel="stylesheet" media="all"/>'.
            '<link href="favicon.ico" rel="shortcut icon"/>',
            str_replace(["\t", "\n", "\r"], '', $result)
        );
    }

    /**
     * Helper Meta
     */
    public function testHelperMeta()
    {
        $layout = new Layout();

        $layout->meta('keywords', 'foo, bar, baz, qux');
        $layout->meta('description', 'foo bar baz qux');

        $result = $layout->meta();

        $this->assertEquals(
            '<meta name="keywords" content="foo, bar, baz, qux"/>'.
            '<meta name="description" content="foo bar baz qux"/>',
            str_replace(["\t", "\n", "\r"], '', $result)
        );
    }

    /**
     * Helper Meta with Array
     */
    public function testHelperMetaArray()
    {
        $layout = new Layout();

        $layout->meta(
            [
                'name' => 'keywords',
                'content' => 'foo, bar, baz, qux'
            ]
        );

        $layout->meta(
            [
                'name' => 'description',
                'content' => 'foo bar baz qux'
            ]
        );

        $result = $layout->meta();

        $this->assertEquals(
            '<meta name="keywords" content="foo, bar, baz, qux"/>'.
            '<meta name="description" content="foo bar baz qux"/>',
            str_replace(["\t", "\n", "\r"], '', $result)
        );
    }

    /**
     * Helper Title
     */
    public function testHelperTitle()
    {
        $layout = new Layout();

        $layout->title('foo');
        $layout->title('bar', Layout::POS_APPEND);
        $layout->title('baz', Layout::POS_PREPEND);

        $result = $layout->title();

        $this->assertEquals('baz :: foo :: bar', $result);
    }
}
