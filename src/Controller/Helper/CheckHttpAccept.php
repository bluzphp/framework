<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Controller\Helper;

use Bluz\Application\Exception\NotAcceptableException;
use Bluz\Controller\Controller;
use Bluz\Proxy\Request;

/**
 * Denied helper can be declared inside Bootstrap
 *
 * @return void
 * @throws NotAcceptableException
 */
return
    function () {
        /**
         * @var Controller $this
         */
        $allowAccept = $this->getMeta()->getAccept();

        // some controllers hasn't @accept tag
        if (!$allowAccept) {
            // but by default allow just HTML output
            $allowAccept = [Request::TYPE_HTML, Request::TYPE_ANY];
        }

        // get Accept with high priority
        $accept = Request::checkAccept($allowAccept);

        // some controllers allow any type (*/*)
        // and client doesn't send Accept header
        if (!$accept && in_array(Request::TYPE_ANY, $allowAccept, true)) {
            // all OK, controller should realize logic for response
            return;
        }

        // some controllers allow just selected types
        // choose MIME type by browser accept header
        // filtered by controller @accept
        // switch statement for this logic
        switch ($accept) {
            case Request::TYPE_ANY:
            case Request::TYPE_HTML:
                // HTML response with layout
                break;
            case Request::TYPE_JSON:
                // JSON response
                $this->disableView();
                break;
            default:
                throw new NotAcceptableException;
        }
    };
