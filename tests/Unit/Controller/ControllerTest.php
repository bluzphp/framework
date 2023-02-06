<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/skeleton
 */

namespace Bluz\Tests\Unit\Controller;

use Bluz\Controller;
use Bluz\Http\Exception\ForbiddenException;
use Bluz\Proxy\Layout;
use Bluz\Proxy\Response;
use Bluz\Response\ContentType;
use Bluz\Tests\Unit\Unit;

/**
 * @package  Bluz\Tests
 * @author   Anton Shevchuk
 * @created  19.05.16 12:28
 */
class ControllerTest extends Unit
{
    /**
     * @var Controller\Controller
     */
    protected Controller\Controller $controller;

    /**
     * Create `index/index` controller
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->controller = self::getApp()->dispatch('index', 'index');
    }

    public function testHelperAttachment()
    {
        $this->controller->attachment('some.jpg');

        self::assertNull($this->controller->getTemplate());
        self::assertEquals(ContentType::FILE, Response::getContentType());
        self::assertFalse(self::getApp()->hasLayout());
    }

    /**
     * @todo Implement testHelperCheckHttpAccept().
     */
    public function testHelperCheckHttpAccept()
    {
        // 12 tests:
        //   -/- -> ANY,JSON,HTML
        //   */* -> ANY,JSON,HTML
        //   html/text -> ANY,JSON,HTML
        //   application/json -> ANY,JSON,HTML

        // Remove the following lines when you implement this test.
        self::markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @todo Implement testHelperCheckMethod().
     */
    public function testHelperCheckMethod()
    {
        // Remove the following lines when you implement this test.
        self::markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @todo Implement testHelperCheckPrivilege().
     */
    public function testHelperCheckPrivilege()
    {
        // Remove the following lines when you implement this test.
        self::markTestIncomplete('This test has not been implemented yet.');
    }

    public function testHelperDenied()
    {
        $this->expectException(ForbiddenException::class);
        $this->controller->denied();
    }

    public function testHelperDisableLayout()
    {
        self::assertTrue(self::getApp()->hasLayout());
        $this->controller->disableLayout();
        self::assertFalse(self::getApp()->hasLayout());
    }

    public function testHelperDisableView()
    {
        $this->controller->disableView();
        self::assertNull($this->controller->getTemplate());
    }

    public function testHelperDispatch()
    {
        self::assertInstanceOf(Controller\Controller::class, $this->controller->dispatch('test', 'index'));
    }

    public function testHelperIsAllowed()
    {
        self::assertFalse($this->controller->isAllowed('not-exists'));
    }

    public function testHelperUseJson()
    {
        $this->controller->useJson();
        self::assertFalse(self::getApp()->hasLayout());
    }

    public function testHelperUseLayout()
    {
        $this->controller->useLayout('small.phtml');
        self::assertTrue(self::getApp()->hasLayout());
        self::assertEquals(Layout::getTemplate(), 'small.phtml');
    }

    public function testHelperUser()
    {
        self::assertNull($this->controller->user());
    }
}
