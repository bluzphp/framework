<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\View;

use Bluz\Proxy\Router;
use Bluz\Tests\FrameworkTestCase;
use Bluz\View\View;
use Bluz\View\ViewException;

/**
 * ViewTest
 *
 * @package  Bluz\Tests\View
 *
 * @author   Anton Shevchuk
 * @created  11.08.2014 10:15
 */
class ViewTest extends FrameworkTestCase
{
    /**
     * Setup `test` table before the first test
     */
    public static function setUpBeforeClass(): void
    {
        self::getApp();
    }

    /**
     * Drop `test` table after the last test
     */
    public static function tearDownAfterClass(): void
    {
        self::resetGlobals();
        self::resetApp();
    }

    /**
     * Working with View container over MagicAccess
     */
    public function testMagicMethods()
    {
        $view = new View();

        $view->foo = 'bar';
        $view->baz = 'qux';

        unset($view->baz);

        self::assertTrue(isset($view->foo));
        self::assertEquals('bar', $view->foo);
        self::assertNull($view->baz);
    }

    /**
     * Set Data Test
     */
    public function testData()
    {
        $view = new View();
        $view->setFromArray(['foo' => '---']);
        $view->setFromArray(['foo' => 'bar', 'baz' => 'qux']);

        self::assertEquals('bar', $view->foo);
        self::assertEquals('qux', $view->baz);
        self::assertEqualsArray(['foo' => 'bar', 'baz' => 'qux'], $view->toArray());
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

        self::assertEquals('bar', $view->foo);
        self::assertEquals('qux', $view->baz);
    }

    /**
     * Get new View instance
     *
     * @return View
     * @throws \Bluz\Application\Exception\ApplicationException
     * @throws \Bluz\Common\Exception\CommonException
     * @throws \ReflectionException
     */
    protected function getView(): View
    {
        $view = new View();

        // setup default partial path
        $view->addPartialPath(self::getApp()->getPath() . '/layouts/partial');

        return $view;
    }

    /**
     * Helper Ahref
     */
    public function testHelperAhref()
    {
        $view = $this->getView();

        self::assertEmpty($view->ahref('text', null));
        self::assertEquals('<a href="test/test" >text</a>', $view->ahref('text', 'test/test'));
        self::assertEquals(
            '<a href="/" class="active">text</a>',
            $view->ahref('text', [Router::getDefaultModule(), Router::getDefaultController()])
        );
        self::assertEquals(
            '<a href="/" class="foo active">text</a>',
            $view->ahref('text', [Router::getDefaultModule(), Router::getDefaultController()], ['class' => 'foo'])
        );
    }

    /**
     * Helper Attributes
     */
    public function testHelperAttributes()
    {
        $view = $this->getView();

        self::assertEmpty($view->attributes([]));

        $result = $view->attributes(['foo' => 'bar', 'baz' => null, 'qux']);

        self::assertEquals('foo="bar" qux', $result);
    }

    /**
     * Helper BaseUrl
     */
    public function testHelperBaseUrl()
    {
        $view = $this->getView();

        self::assertEquals('/', $view->baseUrl());
        self::assertEquals('/about.html', $view->baseUrl('/about.html'));
    }

    /**
     * Helper Checkbox
     */
    public function testHelperCheckbox()
    {
        $view = $this->getView();

        $result = $view->checkbox('test', 1, true, ['class' => 'foo']);
        self::assertEquals('<input class="foo" checked="checked" value="1" name="test" type="checkbox"/>', $result);

        $result = $view->checkbox('sex', 'male', 'male', ['class' => 'foo']);
        self::assertEquals('<input class="foo" checked="checked" value="male" name="sex" type="checkbox"/>', $result);
    }

    /**
     * Helper Controller
     */
    public function testHelperController()
    {
        $view = $this->getView();

        self::assertEquals(Router::getDefaultController(), $view->controller());
        self::assertTrue($view->controller(Router::getDefaultController()));
    }

    /**
     * Helper Dispatch
     *  - this Controller is not exists -> call Exception helper
     */
    public function testHelperDispatch()
    {
        $view = $this->getView();

        self::assertEmpty($view->dispatch('helper', 'dispatch'));
    }

    /**
     * Helper Exception
     *  - should be empty for disabled debug
     */
    public function testHelperException()
    {
        $view = $this->getView();

        self::assertEmpty($view->exception(new \Exception()));
    }

    /**
     * Helper Gravatar
     *  - should return URL to gravatar
     */
    public function testHelperGravatar()
    {
        $view = $this->getView();

        self::assertEquals(
            'https://www.gravatar.com/avatar/56887ad8e17c7c6f6f4f95caee8ad028?s=80&d=mm&r=g',
            $view->gravatar('admin@bluzphp.github.com')
        );
    }

    /**
     * Helper Has Module
     *  - should return true, if module directory is exists
     *  - should return false, if directory not exists
     */
    public function testHelperHasModule()
    {
        $view = $this->getView();

        self::assertTrue($view->hasModule('index'));
        self::assertFalse($view->hasModule(uniqid('index', true)));
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

        self::assertEquals(
            '<script src="/foo.js" ></script>' .
            '<script src="/bar.js" ></script>',
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

        self::assertEquals(
            '<link href="/foo.css" rel="stylesheet" media="all"/>' .
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

        self::assertEquals(Router::getDefaultModule(), $view->module());
        self::assertTrue($view->module(Router::getDefaultModule()));
    }

    /**
     * Helper Partial
     */
    public function testHelperPartial()
    {
        $view = $this->getView();
        $view->setPath(self::getApp()->getPath() . '/modules/index/views');

        $result = $view->partial('partial/partial.phtml', ['foo' => 'bar']);
        self::assertEquals('bar', $result);
    }

    /**
     * Helper Partial throws
     */
    public function testHelperPartialNotFoundTrowsException()
    {
        $this->expectException(ViewException::class);
        $view = $this->getView();
        $view->partial('file-not-exists.phtml');
    }

    /**
     * Helper Partial Loop
     */
    public function testHelperPartialLoop()
    {
        $view = $this->getView();
        $view->setPath(self::getApp()->getPath() . '/modules/index/views');

        $result = $view->partialLoop('partial/partial-loop.phtml', [1, 2, 3], ['foo' => 'bar']);
        self::assertEquals('bar:0:1:bar:1:2:bar:2:3:', $result);
    }

    /**
     * Helper Partial Loop throws
     */
    public function testHelperPartialLoopInvalidArgumentsTrowsException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $view = $this->getView();

        $view->partialLoop('file-not-exists.phtml', null);
    }

    /**
     * Helper Partial Loop throws
     */
    public function testHelperPartialLoopNotFoundTrowsException()
    {
        $this->expectException(ViewException::class);
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

        self::assertEquals('<input class="foo" checked="checked" value="1" name="test" type="radio"/>', $result);
    }

    /**
     * Helper Redactor
     */
    public function testHelperRedactor()
    {
        $view = $this->getView();

        $result = $view->redactor('#editor');

        self::assertNotEmpty($result);
    }

    /**
     * Helper Script
     */
    public function testHelperScript()
    {
        $view = $this->getView();

        $result = $view->script('foo.js', ['async']);

        self::assertEquals('<script src="/foo.js" async></script>', trim($result));
    }

    /**
     * Helper Script inline
     */
    public function testHelperScriptBlock()
    {
        $view = $this->getView();

        $result = $view->scriptBlock('alert("foo=bar")');
        $result = str_replace(["\t", "\n", "\r"], '', $result);

        self::assertEquals('<script type="text/javascript"><!--alert("foo=bar")//--></script>', $result);
    }

    /**
     * Helper Select
     */
    public function testHelperSelect()
    {
        $view = $this->getView();

        $result = $view->select(
            'car',
            [
                'none' => "No Car",
                'class-A' => [
                    'citroen-c1' => 'Citroen C1',
                    'mercedes-benz-a200' => 'Mercedes Benz A200',
                ],
                'class-B' => [
                    'audi-a1' => 'Audi A1',
                    'citroen-c3' => 'Citroen C3',
                ],
            ],
            null,
            [
                'id' => 'car'
            ]
        );

        $result = str_replace(["\t", "\n", "\r"], '', $result);

        self::assertEquals(
            '<select id="car" name="car">' .
            '<option value="none">No Car</option>' .
            '<optgroup label="class-A">' .
            '<option value="citroen-c1">Citroen C1</option>' .
            '<option value="mercedes-benz-a200">Mercedes Benz A200</option>' .
            '</optgroup>' .
            '<optgroup label="class-B">' .
            '<option value="audi-a1">Audi A1</option>' .
            '<option value="citroen-c3">Citroen C3</option>' .
            '</optgroup>' .
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
            'car',
            [
                'none' => 'No Car',
                'citroen-c1' => 'Citroen C1',
                'citroen-c3' => 'Citroen C3',
                'citroen-c4' => 'Citroen C4',
            ],
            'citroen-c4',
            [
                'id' => 'car'
            ]
        );

        $result = str_replace(["\t", "\n", "\r"], '', $result);

        self::assertEquals(
            '<select id="car" name="car">' .
            '<option value="none">No Car</option>' .
            '<option value="citroen-c1">Citroen C1</option>' .
            '<option value="citroen-c3">Citroen C3</option>' .
            '<option value="citroen-c4" selected="selected">Citroen C4</option>' .
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
            'car',
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

        self::assertEquals(
            '<select name="car" multiple="multiple">' .
            '<option value="citroen-c1" selected="selected">Citroen C1</option>' .
            '<option value="mercedes-benz-a200">Mercedes Benz A200</option>' .
            '<option value="audi-a1">Audi A1</option>' .
            '<option value="citroen-c3" selected="selected">Citroen C3</option>' .
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

        self::assertEquals('<link href="/foo.css" rel="stylesheet" media="all"/>', trim($result));
    }

    /**
     * Helper Style inline
     */
    public function testHelperStyleBlock()
    {
        $view = $this->getView();

        $result = $view->styleBlock('#my{color:red}');
        $result = str_replace(["\t", "\n", "\r"], '', $result);

        self::assertEquals('<style type="text/css" media="all">#my{color:red}</style>', $result);
    }

    /**
     * Helper Url
     */
    public function testHelperUrl()
    {
        $view = $this->getView();

        self::assertEquals('/test/test/foo/bar', $view->url('test', 'test', ['foo' => 'bar']));
        self::assertEquals('/test/test', $view->url('test', 'test', null));
        self::assertEquals('/test', $view->url('test', null));
        self::assertEquals('/index/test', $view->url(null, 'test'));
    }

    /**
     * Helper Url Exceptions
     */
    public function testHelperUrlException()
    {
        $this->expectException(ViewException::class);
        $view = $this->getView();

        self::assertEquals('/test/test', $view->url('test', 'test', [], true));
    }

    /**
     * Helper User
     */
    public function testHelperUser()
    {
        $view = $this->getView();

        self::assertNull($view->user());
    }
}
