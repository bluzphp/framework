<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Controller\Helper;

use Bluz\Common\Exception\ComponentException;
use Bluz\Controller\ControllerException;
use Bluz\Http\Exception\NotAcceptableException;
use Bluz\Controller\Controller;
use Bluz\Proxy\Request;
use Bluz\Response\ContentType;
use ReflectionException;

/**
 * Denied helper can be declared inside Bootstrap
 *
 * @return void
 * @throws NotAcceptableException
 * @throws ComponentException
 * @throws ControllerException
 * @throws ReflectionException
 */
return
    function () {
        /**
         * @var Controller $this
         */
        $allowAccept = $this->getMeta()->getAccept();

        // some controllers haven't @accept tag
        if (!$allowAccept) {
            // but by default allow just HTML output
            $allowAccept = [ContentType::HTML, ContentType::ANY];
        }

        // get Accept with high priority
        $accept = Request::checkAccept($allowAccept);

        // some controllers allow any type (*/*)
        // and client doesn't send Accept header
        if (!$accept && in_array(ContentType::ANY, $allowAccept, true)) {
            // all OK, controller should realize logic for response
            return;
        }

        // some controllers allow just selected types
        // choose MIME type by browser accept header
        // filtered by controller @accept
        // switch statement for this logic
        switch ($accept) {
            case ContentType::ANY:
            case ContentType::HTML:
                // HTML response with layout
                break;
            case ContentType::JSON:
                // JSON response
                $this->disableView();
                break;
            default:
                throw new NotAcceptableException();
        }
    };
