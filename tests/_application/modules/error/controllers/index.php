<?php

/**
 * Error controller
 * Send error headers and show simple page
 *
 * @author   Anton Shevchuk
 * @created  11.07.11 15:32
 */

/**
 * @namespace
 */

namespace Application;

use Bluz\Controller\Attribute\Route;
use Bluz\Controller\Controller;
use Bluz\Http\MimeType;
use Bluz\Http\StatusCode;
use Bluz\Proxy\Layout;
use Bluz\Proxy\Logger;
use Bluz\Proxy\Messages;
use Bluz\Proxy\Response;
use Bluz\Proxy\Request;
use Exception;

/**
 * @param int $code
 * @param Exception|null $exception
 *
 * @return array|null
 */
return
    #[Route('/error/{$code}')]
    function (int $code, Exception $exception = null) {
        /**
         * @var Controller $this
         */
        // cast to valid HTTP error code
        //  or use Internal Server Error (code 500)
        $statusCode = StatusCode::tryFrom($code) ?: StatusCode::INTERNAL_SERVER_ERROR;

        Response::setStatusCode($statusCode);

        $exceptionMessage = '';

        if ($exception) {
            Logger::exception($exception);
            $exceptionMessage = $exception->getMessage();
        }

        switch ($statusCode) {
            case StatusCode::BAD_REQUEST:
                $description = $exceptionMessage ?: __('The server didn\'t understand the syntax of the request');
                break;
            case StatusCode::UNAUTHORIZED:
                $description = __('You are not authorized to view this page, please sign in');
                break;
            case StatusCode::FORBIDDEN:
                $description = __('You don\'t have permissions to access this page');
                break;
            case StatusCode::NOT_FOUND:
                $description = __('The page you requested was not found');
                break;
            case StatusCode::METHOD_NOT_ALLOWED:
                Response::setHeader('Allow', $exceptionMessage);
                $description = __('The server is not support method `%s`', Request::getMethod()->value);
                break;
            case StatusCode::NOT_ACCEPTABLE:
                $description = __('The server is not acceptable generating content type described at `Accept` header');
                break;
            case StatusCode::INTERNAL_SERVER_ERROR:
                $description = __('The server encountered an unexpected condition');
                break;
            case StatusCode::NOT_IMPLEMENTED:
                $description = __('The server does not understand or does not support the HTTP method');
                break;
            case StatusCode::SERVICE_UNAVAILABLE:
                Response::setHeader('Retry-After', '600');
                $description = __(
                    'The server is currently unable to handle the request due to a temporary overloading'
                );
                break;
            default:
                $description = __('An unexpected error occurred with your request. Please try again later');
                break;
        }

        // check CLI or HTTP request
        // simple AJAX call, accept JSON
        if (Request::isHttp() && Request::checkAccept(MimeType::JSON)) {
            $this->useJson();
            Messages::addError($description);
            return [
                'code' => $statusCode->value,
                'error' => $description
            ];
        }

        Layout::title($statusCode->message());

        return [
            'code' => $statusCode->value,
            'message' => $exceptionMessage ?: $statusCode->message(),
            'description' => $description,
            'exception' => $exception
        ];
    };
