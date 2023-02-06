<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Request;

use Bluz\Http\RequestMethod;
use Bluz\Proxy\Request;
use Bluz\Response\ContentType;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\ServerRequestFilter\FilterServerRequestInterface;

/**
 * Request Factory
 *
 * @package  Bluz\Request
 * @author   Anton Shevchuk
 */
class RequestFactory extends ServerRequestFactory
{
    /**
     * {@inheritdoc}
     */
    public static function fromGlobals(
        array $server = null,
        array $query = null,
        array $body = null,
        array $cookies = null,
        array $files = null,
        ?FilterServerRequestInterface $requestFilter = null
    ): ServerRequest {
        $request = parent::fromGlobals($server, $query, $body, $cookies, $files);

        $contentType = current($request->getHeader('Content-Type'));

        // support header like "application/json" and "application/json; charset=utf-8"
        if (false !== $contentType && false !== stripos($contentType, ContentType::JSON->value)) {
            $input = file_get_contents('php://input');
            $data = (array)json_decode($input, false);
        } elseif ($request->getMethod() === RequestMethod::POST->value) {
            $data = $_POST;
        } else {
            $input = file_get_contents('php://input');
            parse_str($input, $data);
        }

        return $request->withParsedBody($body ?: $data);
    }
}
