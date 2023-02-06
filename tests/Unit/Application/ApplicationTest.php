<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Unit\Application;

use Bluz\Http\MimeType;
use Bluz\Http\RequestMethod;
use Bluz\Http\StatusCode;
use Bluz\Proxy;
use Bluz\Proxy\Response;
use Bluz\Proxy\Router;
use Bluz\Response\ContentType;
use Bluz\Tests\Unit\Unit;
use Laminas\Diactoros\ServerRequest;

/**
 * ApplicationTest
 *
 * @author   Anton Shevchuk
 * @created  21.05.13 10:24
 */
class ApplicationTest extends Unit
{
    public function testFullApplicationCircle()
    {
        self::getApp();
        self::setRequestParams('', [], [], RequestMethod::GET, ['Accept' => MimeType::HTML->value]);

        self::getApp()->run();

        self::assertEquals(StatusCode::OK, Response::getStatusCode());
        self::assertEquals(ContentType::HTML, Response::getContentType());
    }

    public function testGetApplicationPath()
    {
        self::assertEquals(PATH_APPLICATION, self::getApp()->getPath());
    }

    public function testPreProcessShouldDisableLayoutForAjaxRequests()
    {
        // setup Request
        self::setRequestParams('', [], [], RequestMethod::GET, ['X-Requested-With' => 'XMLHttpRequest']);

        // run Application
        self::getApp()->process();

        self::assertFalse(self::getApp()->hasLayout());
    }

    public function testPreProcessShouldSwitchToJsonResponseForAcceptJsonHeader()
    {
        // setup Request
        self::setRequestParams('', [], [], RequestMethod::GET, ['Accept' => MimeType::JSON->value]);

        // run Application
        self::getApp()->process();

        self::assertFalse(self::getApp()->hasLayout());
        self::assertEquals(ContentType::JSON, self::getApp()->getResponse()->getContentType());
    }

    /**
     * Test run Index Controller if Index Module
     */
    public function testIndexController()
    {
        // setup Request
        self::setRequestParams('', [], [], RequestMethod::GET, ['Accept' => MimeType::HTML->value]);

        // run Application
        self::getApp()->process();

        self::assertEquals(Router::getDefaultModule(), self::getApp()->getModule());
        self::assertEquals(Router::getDefaultController(), self::getApp()->getController());
    }

    /**
     * Test run Error Controller
     */
    public function testErrorController()
    {
        // setup Request
        self::setRequestParams(uniqid('module', false) . '/' . uniqid('controller', false));

        // run Application
        self::getApp()->process();

        self::assertEquals(Router::getErrorModule(), self::getApp()->getModule());
        self::assertEquals(Router::getErrorController(), self::getApp()->getController());
    }

    /**
     * Test call Error helper
     */
    public function testHelperError()
    {
        // setup Request
        self::setRequestParams('test/throw-exception');

        // run Application
        self::getApp()->process();

        self::assertEquals(Router::getErrorModule(), self::getApp()->getModule());
        self::assertEquals(Router::getErrorController(), self::getApp()->getController());
        self::assertEquals(Response::getStatusCode(), StatusCode::INTERNAL_SERVER_ERROR);
        self::assertEquals(Response::getBody()->getData()->get('code'), StatusCode::INTERNAL_SERVER_ERROR->value);
        self::assertEquals(Response::getBody()->getData()->get('message'), 'Message');
    }

    /**
     * Test call Forbidden helper
     */
    public function testHelperForbidden()
    {
        // setup Request
        self::setRequestParams('test/throw-forbidden');

        // run Application
        self::getApp()->process();

        self::assertEquals(Router::getErrorModule(), self::getApp()->getModule());
        self::assertEquals(Router::getErrorController(), self::getApp()->getController());
        self::assertEquals(Response::getStatusCode(), StatusCode::FORBIDDEN);
        self::assertEquals(Response::getBody()->getData()->get('code'), StatusCode::FORBIDDEN->value);
        self::assertEquals(Response::getBody()->getData()->get('message'), StatusCode::FORBIDDEN->message());
    }

    /**
     * Test call Redirect helper
     */
    public function testHelperRedirect()
    {
        // setup Request
        self::setRequestParams('test/throw-redirect');

        // run Application
        self::getApp()->process();

        self::assertEquals(Response::getStatusCode(), StatusCode::FOUND);
        self::assertEquals(Response::getHeader('Location'), Router::getFullUrl());
    }

    /**
     * Test call Redirect helper
     */
    public function testHelperRedirectAjaxCall()
    {
        // setup Request
        self::setRequestParams(
            'test/throw-redirect',
            [],
            [],
            RequestMethod::POST,
            ['X-Requested-With' => 'XMLHttpRequest']
        );

        // run Application
        self::getApp()->process();

        self::assertEquals(Response::getStatusCode(), StatusCode::NO_CONTENT);
        self::assertEquals(Response::getHeader('Bluz-Redirect'), Router::getFullUrl());
    }
}
