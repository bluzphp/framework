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
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\ServerRequestFactory;

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
        array $files = null
    ) {
        $server = static::normalizeServer($server ?: $_SERVER);
        $files = static::normalizeFiles($files ?: $_FILES);
        $headers = static::marshalHeaders($server);
        $request = new ServerRequest(
            $server,
            $files,
            static::marshalUriFromServer($server, $headers),
            static::get('REQUEST_METHOD', $server, RequestMethod::GET),
            'php://input',
            $headers
        );

        $contentType = current($request->getHeader('Content-Type'));

        $input = file_get_contents('php://input');

        // support header like "application/json" and "application/json; charset=utf-8"
        if ($contentType !== false && stristr($contentType, Request::TYPE_JSON)) {
            $data = (array)json_decode($input);
        } else {
            switch ($request->getMethod()) {
                case RequestMethod::POST:
                    $data = $_POST;
                    break;
                default:
                    parse_str($input, $data);
                    break;
            }
        }

        return $request
            ->withCookieParams($cookies ?: $_COOKIE)
            ->withQueryParams($query ?: $_GET)
            ->withParsedBody($body ?: $data);
    }
}
