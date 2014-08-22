<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\View;

use Bluz\Tests\TestCase;
use Bluz\View\Layout;

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
}
