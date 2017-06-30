<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
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

        self::assertEquals('foo', $layout->getContent());
    }

    /**
     * Test Callable Content
     */
    public function testSetCallableContent()
    {
        $layout = new Layout();
        $layout->setContent(
            function () {
                return 'foo';
            }
        );

        self::assertEquals('foo', $layout->getContent());
    }

    /**
     * Test Callable Content throw any Exception
     */
    public function testSetInvalidCallableContent()
    {
        $layout = new Layout();
        $layout->setContent(
            function () {
                throw new \Exception('foo');
            }
        );

        self::assertEquals('foo', $layout->getContent());
    }

    /**
     * Helper Breadcrumbs
     */
    public function testHelperBreadcrumbs()
    {
        $layout = new Layout();

        $layout->breadCrumbs(['foo' => 'bar']);

        self::assertEqualsArray(['foo' => 'bar'], $layout->breadCrumbs());
    }

    /**
     * Helper Link
     */
    public function testHelperLink()
    {
        $layout = new Layout();

        $layout->link(['href' => 'foo.css', 'rel' => "stylesheet", 'media' => "all"]);
        $layout->link(['href' => 'favicon.ico', 'rel' => 'shortcut icon']);

        $result = $layout->link();

        self::assertEquals(
            '<link href="foo.css" rel="stylesheet" media="all"/>' .
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

        self::assertEquals(
            '<meta name="keywords" content="foo, bar, baz, qux"/>' .
            '<meta name="description" content="foo bar baz qux"/>',
            str_replace(["\t", "\n", "\r"], '', $result)
        );
    }

    /**
     * Helper Meta
     */
    public function testHelperMetaIsInvalid()
    {
        $layout = new Layout();

        self::assertNull($layout->meta(null, 'content'));
    }

    /**
     * Helper Meta
     */
    public function testHelperMetaIsEmpty()
    {
        $layout = new Layout();

        self::assertEmpty($layout->meta());
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

        self::assertEquals('baz :: foo :: bar', $result);
    }
}
