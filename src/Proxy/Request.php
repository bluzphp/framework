<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Proxy;

use Bluz\Common\Exception\ComponentException;
use Bluz\Http\RequestMethod;
use Bluz\Request\Request as Instance;
use Laminas\Diactoros\ServerRequest;
use Psr\Http\Message\UriInterface;

//use Psr\Http\Message\UriInterface;
//use Laminas\Diactoros\ServerRequest as Instance;
//use Laminas\Diactoros\UploadedFile;

/**
 * Proxy to Request
 *
 * Example of usage
 * <code>
 *     use Bluz\Proxy\Request;
 *
 *     Request::getParam('foo');
 * </code>
 *
 * @package Bluz\Proxy
 * @author  Anton Shevchuk
 *
 * @todo    Proxy class should be clean
 *
 * @method  static Instance getInstance()
 * @method  static UriInterface getUri()
 * @method  static RequestMethod getMethod()
 * @method  static ServerRequest getServerRequest()
 * @method  static void setServerRequest(ServerRequest $request)
 */
final class Request
{
    use ProxyTrait;

    /**
     * Init instance
     *
     * @throws ComponentException
     */
    private static function initInstance()
    {
        throw new ComponentException('Class `Proxy\\Request` required external initialization');
    }

    /**
     * Get module
     *
     * @return string
     */
    public static function getModule(): string
    {
        return self::getInstance()->getParam('_module', Router::getDefaultModule());
    }

    /**
     * Get controller
     *
     * @return string
     */
    public static function getController(): string
    {
        return self::getInstance()->getParam('_controller', Router::getDefaultController());
    }

    /**
     * Reset Request
     *
     * @param string $module
     * @param string $controller
     * @param array $params
     * @return void
     */
    public static function reset(string $module, string $controller, array $params = []): void
    {
        $request = self::getInstance()->getServerRequest();

        // priority:
        //  - default values
        //  - from GET query
        //  - from path
        $request = $request->withQueryParams(
            array_merge(
                [
                    '_module' => $module,
                    '_controller' => $controller
                ],
                $request->getQueryParams(),
                $params
            )
        );
        self::getInstance()->setServerRequest($request);
    }
}
