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
use Bluz\Router\Router as Instance;

/**
 * Proxy to Router
 *
 * Example of usage
 * <code>
 *     use Bluz\Proxy\Router;
 *
 *     Router::getUrl('pages', 'index', ['alias' => 'about']); // for skeleton application is `/about.html`
 * </code>
 *
 * @package  Bluz\Proxy
 * @author   Anton Shevchuk
 *
 * @method   static Instance getInstance()
 *
 * @method   static string getBaseUrl()
 * @see      Instance::getBaseUrl()
 *
 * @method   static void   setBaseUrl($baseUrl)
 * @see      Instance::setBaseUrl()
 *
 * @method   static string getCleanUri()
 * @see      Instance::getCleanUri()
 *
 * @method   static mixed getParam(mixed $key, mixed $default = null)
 * @method   static array getParams()
 *
 * @method   static void process()
 * @see      Instance::process()
 *
 * @method   static string getDefaultModule()
 * @see      Instance::getDefaultModule()
 *
 * @method   static string getDefaultController()
 * @see      Instance::getDefaultController()
 *
 * @method   static string getErrorModule()
 * @see      Instance::getErrorModule()
 *
 * @method   static string getErrorController()
 * @see      Instance::getErrorController()
 */
final class Router
{
    use ProxyTrait;

    /**
     * Init instance
     *
     * @throws ComponentException
     */
    private static function initInstance()
    {
        throw new ComponentException("Class `Proxy\\Router` required external initialization");
    }

    /**
     * Build URL to controller
     *
     * @param string|null $module
     * @param string|null $controller
     * @param array $params
     *
     * @return string
     */
    public static function getUrl(
        ?string $module = Instance::DEFAULT_MODULE,
        ?string $controller = Instance::DEFAULT_CONTROLLER,
        array $params = []
    ): string {
        $module = $module ?? Request::getModule();
        $controller = $controller ?? Request::getController();

        return self::getInstance()->getUrl($module, $controller, $params);
    }

    /**
     * Build full URL to controller
     *
     * @param string|null $module
     * @param string|null $controller
     * @param array $params
     *
     * @return string
     */
    public static function getFullUrl(
        ?string $module = Instance::DEFAULT_MODULE,
        ?string $controller = Instance::DEFAULT_CONTROLLER,
        array $params = []
    ): string {
        $scheme = Request::getUri()->getScheme() . '://';
        $host = Request::getUri()->getHost();
        $port = Request::getUri()->getPort();
        if ($port && !in_array($port, [80, 443], true)) {
            $host .= ':' . $port;
        }
        $url = self::getUrl($module, $controller, $params);
        return $scheme . $host . $url;
    }
}
