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
use Bluz\Http\MimeType;
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
 * @method static Instance getInstance()
 * @method static RequestMethod getMethod()
 * @method static ServerRequest getServerRequest()
 * @method static void setServerRequest(ServerRequest $request)
 * @method static UriInterface getUri()
 * @method static string getPath()
 * @method static mixed getQuery(?string $key = null, ?string $default = null)
 * @method static mixed getPost(?string $key = null, ?string $default = null)
 * @method static string|null getServer(?string $key = null, ?string $default = null)
 * @method static string|null getCookie(?string $key = null, ?string $default = null)
 * @method static string|null getEnv(?string $key = null, ?string $default = null)
 * @method static string|null getHeader(string $header, mixed $default = null)
 * @method static mixed getParam(string $key, mixed $default = null)
 * @method static array getParams()
 * @method static bool isCli()
 * @method static bool isHttp()
 * @method static bool isGet()
 * @method static bool isPost()
 * @method static bool isPut()
 * @method static bool isDelete()
 * @method static bool isXmlHttpRequest()
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
     * Check Accept header
     *
     * @param MimeType[] $allowTypes
     *
     * @return bool
     */
    public static function checkAccept(...$allowTypes): bool
    {
        $accept = self::getInstance()->getAccept();

        foreach ($allowTypes as $mimeType) {
            if (array_key_exists($mimeType->value, $accept)) {
                return true;
            }
        }
        // no mime-type found
        return false;
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
