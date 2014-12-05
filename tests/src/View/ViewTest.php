<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\View;

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
     * Working with View container over MagicAccess
     */
    public function testMagicMethods()
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
     * Set Data Test
     */
    public function testData()
    {
        $view = new View();
        $view->setFromArray(['foo' => '---']);
        $view->setFromArray(['foo' => 'bar', 'baz' => 'qux']);

        $this->assertEquals('bar', $view->foo);
        $this->assertEquals('qux', $view->baz);
        $this->assertEqualsArray(['foo' => 'bar', 'baz' => 'qux'], $view->toArray());
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
     * Get new View instance
     *
     * @return View
     */
    protected function getView()
    {
        $view = new View();

        // setup default partial path
        $view->addPartialPath($this->getApp()->getPath() . '/layouts/partial');

        return $view;
    }
    
    /**
     * Helper Ahref
     */
    public function testHelperAhref()
    {
        $view = $this->getView();

        $this->assertEmpty($view->ahref('text', null));
        $this->assertEquals('<a href="test/test" >text</a>', $view->ahref('text', 'test/test'));
        $this->assertEquals(
            '<a href="/" class="on">text</a>',
            $view->ahref('text', [Router::DEFAULT_MODULE, Router::DEFAULT_CONTROLLER])
        );
        $this->assertEquals(
            '<a href="/" class="foo on">text</a>',
            $view->ahref('text', [Router::DEFAULT_MODULE, Router::DEFAULT_CONTROLLER], ['class' => 'foo'])
        );
    }

    /**
     * Helper Api
     *  - this API call is not exists -> call Exception helper
     */
    public function testHelperApi()
    {
        $view = $this->getView();

        $this->assertEmpty($view->api('test', 'test'));
    }

    /**
     * Helper Attributes
     */
    public function testHelperAttributes()
    {
        $view = $this->getView();

        $this->assertEmpty($view->attributes([]));

        $result = $view->attributes(['foo' => 'bar', 'baz' => null, 'qux']);

        $this->assertEquals('foo="bar" qux="qux"', $result);
    }

    /**
     * Helper BaseUrl
     */
    public function testHelperBaseUrl()
    {
        $view = $this->getView();

        $this->assertEquals('/', $view->baseUrl());
        $this->assertEquals('/about.html', $view->baseUrl('/about.html'));
    }

    /**
     * Helper Checkbox
     */
    public function testHelperCheckbox()
    {
        $view = $this->getView();

        $result = $view->checkbox('test', 1, true, ['class' => 'foo']);
        $this->assertEquals('<input class="foo" checked="checked" value="1" name="test" type="checkbox"/>', $result);

        $result = $view->checkbox('sex', 'male', 'male', ['class' => 'foo']);
        $this->assertEquals('<input class="foo" checked="checked" value="male" name="sex" type="checkbox"/>', $result);
    }

    /**
     * Helper Controller
     */
    public function testHelperController()
    {
        $view = $this->getView();

        $this->assertEquals(Router::DEFAULT_CONTROLLER, $view->controller());
        $this->assertTrue($view->controller(Router::DEFAULT_CONTROLLER));
    }

    /**
     * Helper Dispatch
     *  - this Controller is not exists -> call Exception helper
     */
    public function testHelperDispatch()
    {
        $view = $this->getView();

        $this->assertEmpty($view->dispatch('test', 'test'));
    }

    /**
     * Helper Exception
     *  - should be empty for disabled debug
     */
    public function testHelperException()
    {
        $view = $this->getView();

        $this->assertEmpty($view->exception(new \Exception()));
    }

    /**
     * Helper Script
     */
    public function testHelperHeadScript()
    {
        $view = $this->getView();

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
    public function testHelperHeadStyle()
    {
        $view = $this->getView();

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
     * Helper Module
     */
    public function testHelperModule()
    {
        $view = $this->getView();

        $this->assertEquals(Router::DEFAULT_MODULE, $view->module());
        $this->assertTrue($view->module(Router::DEFAULT_MODULE));
    }

    /**
     * Helper Partial
     */
    public function testHelperPartial()
    {
        $view = $this->getView();
        $view->setPath($this->getApp()->getPath() . '/modules/index/views');

        $result = $view->partial('partial/partial.phtml', ['foo' => 'bar']);
        $this->assertEquals('bar', $result);
    }

    /**
     * Helper Partial throws
     *
     * @expectedException \Bluz\View\ViewException
     */
    public function testHelperPartialNotFoundTrowsException()
    {
        $view = $this->getView();

        $view->partial('file-not-exists.phtml');
    }

    /**
     * Helper Partial Loop
     */
    public function testHelperPartialLoop()
    {
        $view = $this->getView();
        $view->setPath($this->getApp()->getPath() . '/modules/index/views');

        $result = $view->partialLoop('partial/partial-loop.phtml', [1, 2, 3], ['foo' => 'bar']);
        $this->assertEquals('bar:0:1:bar:1:2:bar:2:3:', $result);
    }

    /**
     * Helper Partial Loop throws
     *
     * @expectedException \InvalidArgumentException
     */
    public function testHelperPartialLoopInvalidArgumentsTrowsException()
    {
        $view = $this->getView();

        $view->partialLoop('file-not-exists.phtml', null);
    }

    /**
     * Helper Partial Loop throws
     *
     * @expectedException \Bluz\View\ViewException
     */
    public function testHelperPartialLoopNotFoundTrowsException()
    {
        $view = $this->getView();

        $view->partialLoop('file-not-exists.phtml', ['foo', 'bar']);
    }

    /**
     * Helper Radio
     */
    public function testHelperRadio()
    {
        $view = $this->getView();

        $result = $view->radio('test', 1, true, ['class' => 'foo']);

        $this->assertEquals('<input class="foo" checked="checked" value="1" name="test" type="radio"/>', $result);
    }

    /**
     * Helper Redactor
     */
    public function testHelperRedactor()
    {
        $view = $this->getView();

        $view->redactor('#editor');

        $this->assertNotEmpty($view->headScript());
        $this->assertNotEmpty($view->headStyle());
    }

    /**
     * Helper Script
     */
    public function testHelperScript()
    {
        $view = $this->getView();

        $result = $view->script('foo.js');

        $this->assertEquals('<script src="/foo.js"></script>', trim($result));
    }

    /**
     * Helper Script inline
     */
    public function testHelperScriptPlain()
    {
        $view = $this->getView();

        $result = $view->script('alert("foo=bar")');
        $result = str_replace(["\t", "\n", "\r"], '', $result);

        $this->assertEquals('<script type="text/javascript"><!--alert("foo=bar")//--></script>', $result);
    }

    /**
     * Helper Select
     */
    public function testHelperSelect()
    {
        $view = $this->getView();

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
            null,
            ["id"=>"car"]
        );

        $result = str_replace(["\t", "\n", "\r"], '', $result);

        $this->assertEquals(
            '<select id="car" name="car">'.
            '<option value="none">No Car</option>'.
            '<optgroup label="class-A">'.
            '<option value="citroen-c1">Citroen C1</option>'.
            '<option value="mercedes-benz-a200">Mercedes Benz A200</option>'.
            '</optgroup>'.
            '<optgroup label="class-B">'.
            '<option value="audi-a1">Audi A1</option>'.
            '<option value="citroen-c3">Citroen C3</option>'.
            '</optgroup>'.
            '</select>',
            $result
        );
    }

    /**
     * Helper Select
     */
    public function testHelperSelectWithSelectedElement()
    {
        $view = $this->getView();

        $result = $view->select(
            "car",
            [
                "none" => "No Car",
                'citroen-c1' => 'Citroen C1',
                'citroen-c3' => 'Citroen C3',
                'citroen-c4' => 'Citroen C4',
            ],
            'citroen-c4',
            ["id"=>"car"]
        );

        $result = str_replace(["\t", "\n", "\r"], '', $result);

        $this->assertEquals(
            '<select id="car" name="car">'.
            '<option value="none">No Car</option>'.
            '<option value="citroen-c1">Citroen C1</option>'.
            '<option value="citroen-c3">Citroen C3</option>'.
            '<option value="citroen-c4" selected="selected">Citroen C4</option>'.
            '</select>',
            $result
        );
    }

    /**
     * Helper Select
     */
    public function testHelperSelectMultiple()
    {
        $view = $this->getView();

        $result = $view->select(
            "car",
            [
                'citroen-c1' => 'Citroen C1',
                'mercedes-benz-a200' => 'Mercedes Benz A200',
                'audi-a1' => 'Audi A1',
                'citroen-c3' => 'Citroen C3',
            ],
            [
                'citroen-c1',
                'citroen-c3'
            ]
        );

        $result = str_replace(["\t", "\n", "\r"], '', $result);

        $this->assertEquals(
            '<select name="car" multiple="multiple">'.
            '<option value="citroen-c1" selected="selected">Citroen C1</option>'.
            '<option value="mercedes-benz-a200">Mercedes Benz A200</option>'.
            '<option value="audi-a1">Audi A1</option>'.
            '<option value="citroen-c3" selected="selected">Citroen C3</option>'.
            '</select>',
            $result
        );
    }

    /**
     * Helper Style
     */
    public function testHelperStyle()
    {
        $view = $this->getView();

        $result = $view->style('foo.css');

        $this->assertEquals('<link href="/foo.css" rel="stylesheet" media="all"/>', trim($result));
    }

    /**
     * Helper Style inline
     */
    public function testHelperStylePlain()
    {
        $view = $this->getView();

        $result = $view->style('#my{color:red}');
        $result = str_replace(["\t", "\n", "\r"], '', $result);

        $this->assertEquals('<style type="text/css" media="all">#my{color:red}</style>', $result);
    }

    /**
     * Helper Url
     */
    public function testHelperUrl()
    {
        $view = $this->getView();

        $this->assertEquals('/test/test/foo/bar', $view->url('test', 'test', ['foo' => 'bar']));
        $this->assertEquals('/test/test', $view->url('test', 'test', null));
        $this->assertEquals('/test', $view->url('test', null));
        $this->assertEquals('/index/test', $view->url(null, 'test'));
    }

    /**
     * Helper Url Exceptions
     *
     * @expectedException \Bluz\View\ViewException
     */
    public function testHelperUrlException()
    {
        $view = $this->getView();

        $this->assertEquals('/test/test', $view->url('test', 'test', [], true));
    }

    /**
     * Helper User
     */
    public function testHelperUser()
    {
        $view = $this->getView();

        $this->assertNull($view->user());
    }

    /**
     * Helper Widget
     *  - this Widget is not exists -> call Exception helper
     */
    public function testHelperWidget()
    {
        $view = $this->getView();

        $this->expectOutputString('');

        $view->widget('test', 'test');
    }
}
