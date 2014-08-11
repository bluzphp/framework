<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\View;

use Bluz\Http\Request;
use Bluz\Router\Router;
use Bluz\Tests\TestCase;
use Bluz\View\View;

/**
 * ViewTest
 *
 * @package  Bluz\Tests\View
 *
 * @author   Anton Shevchuk
 * @created  11.08.2014 10:15
 */
class ViewTest extends TestCase
{
    /**
     * Working with View container
     *
     * @covers \Bluz\View\View::__set
     * @covers \Bluz\View\View::__get
     * @covers \Bluz\View\View::__isset
     * @covers \Bluz\View\View::__unset
     */
    public function testViewContainer()
    {
        $view = new View();

        $view->foo = 'bar';
        $view->baz = 'qux';

        unset($view->baz);

        $this->assertTrue(isset($view->foo));
        $this->assertEquals('bar', $view->foo);
        $this->assertNull($view->baz);
    }

    /**
     * Test Data
     *
     * @covers \Bluz\View\View::setData
     * @covers \Bluz\View\View::getData
     * @covers \Bluz\View\View::mergeData
     */
    public function testData()
    {
        $view = new View();
        $view->setData(['foo' => '---']);
        $view->mergeData(['foo' => 'bar', 'baz' => 'qux']);

        $this->assertEquals('bar', $view->foo);
        $this->assertEquals('qux', $view->baz);
        $this->assertEqualsArray(['foo' => 'bar', 'baz' => 'qux'], $view->getData());
    }

    /**
     * Test Serialization
     */
    public function testSerialization()
    {
        $view = new View();

        $view->foo = 'bar';
        $view->baz = 'qux';

        $view = unserialize(serialize($view));

        $this->assertEquals('bar', $view->foo);
        $this->assertEquals('qux', $view->baz);
    }

    /**
     * Test JSON serialization
     *
     * @covers \Bluz\View\View::jsonSerialize
     */
    public function testJson()
    {
        $view = new View();

        $view->foo = 'bar';
        $view->baz = 'qux';

        $view = json_decode(json_encode($view));

        $this->assertEquals('bar', $view->foo);
        $this->assertEquals('qux', $view->baz);
    }

    /**
     * Helper Ahref
     */
    public function testViewHelperAhref()
    {
        $view = $this->getApp()->getView();

        $this->assertEmpty($view->ahref('text', null));
        $this->assertEquals('<a href="test/test" >text</a>', $view->ahref('text', 'test/test'));
    }

    /**
     * Helper Api
     *  - this API call is not exists -> call Exception helper
     */
    public function testViewHelperApi()
    {
        $view = $this->getApp()->getView();

        $this->assertEmpty($view->api('test', 'test'));
    }

    /**
     * Helper Attributes
     */
    public function testViewHelperAttributes()
    {
        $view = $this->getApp()->getView();

        $this->assertEmpty($view->attributes([]));

        $result = $view->attributes(['foo' => 'bar', 'baz' => null, 'qux']);

        $this->assertEquals('foo="bar" qux="qux"', $result);
    }

    /**
     * Helper BaseUrl
     */
    public function testViewHelperBaseUrl()
    {
        $view = $this->getApp()->getView();

        $this->assertEquals('/', $view->baseUrl());
        $this->assertEquals('/about.html', $view->baseUrl('/about.html'));
    }

    /**
     * Helper Breadcrumbs
     */
    public function testViewHelperBreadcrumbs()
    {
        $view = $this->getApp()->getView();

        $view->breadCrumbs(['foo' => 'bar']);

        $this->assertEqualsArray(['foo' => 'bar'], $view->breadCrumbs());
    }

    /**
     * Helper Checkbox
     */
    public function testViewHelperCheckbox()
    {
        $view = $this->getApp()->getView();

        $result = $view->checkbox('test', 1, true, ['class' => 'foo']);

        $this->assertEquals('<input class="foo" checked="checked" value="1" name="test" type="checkbox"/>', $result);
    }

    /**
     * Helper Controller
     */
    public function testViewHelperController()
    {
        $view = $this->getApp()->getView();

        $this->assertEquals(Router::DEFAULT_CONTROLLER, $view->controller());
        $this->assertTrue($view->controller(Router::DEFAULT_CONTROLLER));
    }

    /**
     * Helper Dispatch
     *  - this Controller is not exists -> call Exception helper
     */
    public function testViewHelperDispatch()
    {
        $view = $this->getApp()->getView();

        $this->assertEmpty($view->dispatch('test', 'test'));
    }

    /**
     * Helper Exception
     *  - should be empty for disabled debug
     */
    public function testViewHelperException()
    {
        $view = $this->getApp()->getView();

        $this->assertEmpty($view->exception(new \Exception()));
    }

    /**
     * Helper Script
     */
    public function testViewHelperHeadScript()
    {
        $view = $this->getApp()->getView();

        $view->headScript('foo.js');
        $view->headScript('bar.js');


        $result = $view->headScript();

        $this->assertEquals(
            '<script src="/foo.js"></script>'.
            '<script src="/bar.js"></script>',
            str_replace(["\t", "\n", "\r"], '', $result)
        );
    }

    /**
     * Helper Style
     */
    public function testViewHelperHeadStyle()
    {
        $view = $this->getApp()->getView();

        $view->headStyle('foo.css');
        $view->headStyle('bar.css');


        $result = $view->headStyle();

        $this->assertEquals(
            '<link href="/foo.css" rel="stylesheet" media="all"/>'.
            '<link href="/bar.css" rel="stylesheet" media="all"/>',
            str_replace(["\t", "\n", "\r"], '', $result)
        );
    }

    /**
     * Helper Link
     */
    public function testViewHelperLink()
    {
        $view = $this->getApp()->getView();

        $view->link(['href'=>'foo.css', 'rel' => "stylesheet", 'media' => "all"]);
        $view->link(['href'=>'favicon.ico', 'rel' => 'shortcut icon']);

        $result = $view->link();

        $this->assertEquals(
            '<link href="foo.css" rel="stylesheet" media="all"/>'.
            '<link href="favicon.ico" rel="shortcut icon"/>',
            str_replace(["\t", "\n", "\r"], '', $result)
        );
    }

    /**
     * Helper Meta
     */
    public function testViewHelperMeta()
    {
        $view = $this->getApp()->getView();

        $view->meta('keywords', 'foo, bar, baz, qux');
        $view->meta('description', 'foo bar baz qux');

        $result = $view->meta();

        $this->assertEquals(
            '<meta name="keywords" content="foo, bar, baz, qux"/>'.
            '<meta name="description" content="foo bar baz qux"/>',
            str_replace(["\t", "\n", "\r"], '', $result)
        );
    }

    /**
     * Helper Module
     */
    public function testViewHelperModule()
    {
        $view = $this->getApp()->getView();

        $this->assertEquals(Router::DEFAULT_MODULE, $view->module());
        $this->assertTrue($view->module(Router::DEFAULT_MODULE));
    }

    /**
     * Helper Radio
     */
    public function testViewHelperRadio()
    {
        $view = $this->getApp()->getView();

        $result = $view->radio('test', 1, true, ['class' => 'foo']);

        $this->assertEquals('<input class="foo" checked="checked" value="1" name="test" type="radio"/>', $result);
    }

    /**
     * Helper Script
     */
    public function testViewHelperScript()
    {
        $view = $this->getApp()->getView();

        $result = $view->script('foo.js');

        $this->assertEquals('<script src="/foo.js"></script>', trim($result));
    }

    /**
     * Helper Select
     */
    public function testViewHelperSelect()
    {
        $view = $this->getApp()->getView();

        $result = $view->select(
            "car",
            [
                "none" => "No Car",
                "class-A" => [
                    'citroen-c1' => 'Citroen C1',
                    'mercedes-benz-a200' => 'Mercedes Benz A200',
                ],
                "class-B" => [
                    'audi-a1' => 'Audi A1',
                    'citroen-c3' => 'Citroen C3',
                ],
            ],
            "none",
            ["id"=>"car"]
        );

        $this->assertEquals(
            '<select id="car" name="car">'.
            '<option value="none" selected="selected">No Car</option>'.
            '<optgroup label="class-A">'.
            '<option value="citroen-c1">Citroen C1</option>'.
            '<option value="mercedes-benz-a200">Mercedes Benz A200</option>'.
            '</optgroup>'.
            '<optgroup label="class-B">'.
            '<option value="audi-a1">Audi A1</option>'.
            '<option value="citroen-c3">Citroen C3</option>'.
            '</optgroup>'.
            '</select>',
            str_replace(["\t", "\n", "\r"], '', $result)
        );
    }

    /**
     * Helper Script
     */
    public function testViewHelperStyle()
    {
        $view = $this->getApp()->getView();

        $result = $view->style('foo.css');

        $this->assertEquals('<link href="/foo.css" rel="stylesheet" media="all"/>', trim($result));
    }

    /**
     * Helper Title
     */
    public function testViewHelperTitle()
    {
        $view = $this->getApp()->getView();

        $view->title('foo');
        $view->title('bar', View::POS_APPEND);
        $view->title('baz', View::POS_PREPEND);

        $result = $view->title();

        $this->assertEquals('baz :: foo :: bar', $result);
    }

    /**
     * Helper Url
     */
    public function testViewHelperUrl()
    {
        $view = $this->getApp()->getView();

        $this->assertEquals('/test/test/foo/bar', $view->url('test', 'test', ['foo' => 'bar']));
    }

    /**
     * Helper User
     */
    public function testViewHelperUser()
    {
        $view = $this->getApp()->getView();

        $this->assertNull($view->user());
    }

    /**
     * Helper Widget
     *  - this Widget is not exists -> call Exception helper
     */
    public function testViewHelperWidget()
    {
        $view = $this->getApp()->getView();

        $this->expectOutputString('');

        $view->widget('test', 'test');
    }
}
