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

use Bluz;
use Bluz\Request;

return
/**
 * @route  /error/{$code}
 * @param  int $code
 * @param  string $message
 */
function ($code, $message = '') use ($view) {
    /**
     * @var Bootstrap $this
     * @var \Bluz\View\View $view
     */
    $this->getLogger()->error($message);

    switch ($code) {
        case 400:
            $title = __("Bad Request");
            $description = __("The server didn't understand the syntax of the request");
            break;
        case 401:
            $title = __("Unauthorized");
            $description = __("You are not authorized to view this page, please sign in");
            break;
        case 403:
            $title = __("Forbidden");
            $description = __("You don't have permissions to access this page");
            break;
        case 404:
            $title = __("Not Found");
            $description = __("The page you requested was not found");
            break;
        case 405:
            $title = __("Method Not Allowed");
            $description = __("The server is not support method");
            $this->getResponse()->setHeader('Allow', $message);
            break;
        case 500:
            $title = __("Internal Server Error");
            $description = __("The server encountered an unexpected condition");
            break;
        case 501:
            $title = __("Not Implemented");
            $description = __("The server does not understand or does not support the HTTP method");
            break;
        case 503:
            $title = __("Service Unavailable");
            $description = __("The server is currently unable to handle the request due to a temporary overloading");
            $this->getResponse()->setHeader('Retry-After', '600');
            break;
        default:
            $title = __("Internal Server Error");
            $description = __("An unexpected error occurred with your request. Please try again later");
            break;
    }

    // check CLI or HTTP request
    if ($this->getRequest()->isHttp()) {

        // simple AJAX call
        if ($this->isJson()) {
            $this->getMessages()->addError($message);
            return $view;
        }

        // dialog AJAX call
        if (!$this->getRequest()->isXmlHttpRequest()) {
            $this->useLayout('small.phtml');
        }
    }

    $view->title = $title;
    $view->description = $description;
    $view->message = $message;
    $this->getLayout()->title($title);
    return $view;
};
