<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Controller;

use Bluz\Common\Exception\ComponentException;
use Bluz\Common\Options;
use Bluz\Proxy\Request;
use Bluz\Response\ContentType;
use Closure;
use ReflectionException;

/**
 * Meta information from reflection of the function
 *
 * @package  Bluz\Controller
 * @author   Anton Shevchuk
 */
class Meta
{
    use Options;

    /**
     * @var string full path to file
     */
    protected string $file;

    /**
     * @var int cache TTL
     */
    protected int $cache = 0;

    /**
     * @var array list of Accept
     */
    protected array $accept = [];

    /**
     * @var array list of Acl
     */
    protected array $acl = [];

    /**
     * @var array list of HTTP methods
     */
    protected array $method = [];

    /**
     * @var array described params
     */
    protected array $params = [];

    /**
     * @var ?string privilege
     */
    protected ?string $privilege = null;

    /**
     * @var array routers
     */
    protected array $route = [];

    /**
     * @var array default values of params
     */
    protected array $values = [];

    /**
     * Constructor of Reflection
     *
     * @param string $file
     */
    public function __construct(string $file)
    {
        $this->file = $file;
    }

    /**
     * Set state required for working with var_export (used inside PHP File cache)
     *
     * @param array $array
     *
     * @return Meta
     */
    public static function __set_state($array)
    {
        $instance = new Meta($array['file']);
        foreach ($array as $key => $value) {
            $instance->{$key} = $value;
        }
        return $instance;
    }

    /**
     * Process to get reflection from file
     *
     * @return void
     * @throws ComponentException
     * @throws ReflectionException
     */
    public function process(): void
    {
        /** @var Closure|object $closure */
        $closure = include $this->file;

        if (!is_callable($closure)) {
            throw new ComponentException("There is no callable structure in file `{$this->file}`");
        }

        $reflection = new \ReflectionFunction($closure);

        // check and normalize params by doc comment
        $docComment = $reflection->getDocComment();

        // get all options by one regular expression
        if (preg_match_all('/\s*\*\s*@([a-z0-9-_]+)\s+(.*).*\s+/i', $docComment, $matches)) {
            foreach ($matches[1] as $i => $key) {
                $this->setOption($key, trim($matches[2][$i]));
            }
        }

        // init routes
        $this->initRoute();

        // get params and convert it to simple array
        $reflectionParams = $reflection->getParameters();
        // setup params and optional params
        foreach ($reflectionParams as $param) {
            $name = $param->getName();
            // if some function params is missed in description
            if (!isset($this->params[$name])) {
                $this->params[$name] = null;
            }
            if ($param->isOptional()) {
                $this->values[$name] = $param->getDefaultValue();
            }
        }
    }

    /**
     * Process request params
     *
     *  - type conversion
     *  - set default value
     *
     * @param array $requestParams
     *
     * @return array
     */
    public function params(array $requestParams): array
    {
        // apply type and default value for request params
        $params = [];
        foreach ($this->params as $param => $type) {
            if (isset($requestParams[$param])) {
                $params[] = match ($type) {
                    'bool', 'boolean' => (bool)$requestParams[$param],
                    'int', 'integer' => (int)$requestParams[$param],
                    'float' => (float)$requestParams[$param],
                    'string' => (string)$requestParams[$param],
                    'array' => (array)$requestParams[$param],
                    default => $requestParams[$param],
                };
            } elseif (isset($this->values[$param])) {
                $params[] = $this->values[$param];
            } else {
                $params[] = null;
            }
        }
        return $params;
    }

    /**
     * Get path to file
     *
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }

    /**
     * Get Cache TTL
     *
     * @return integer
     */
    public function getCache(): int
    {
        return $this->cache;
    }

    /**
     * Set Cache TTL
     *
     * @param string $ttl
     *
     * @return void
     */
    public function setCache(string $ttl): void
    {
        $this->cache = $this->prepareCache($ttl);
    }

    /**
     * Prepare Cache
     *
     * @param string $cache
     *
     * @return integer
     */
    protected function prepareCache(string $cache): int
    {
        $num = (int)$cache;
        $time = 'min';

        if ($pos = strpos($cache, ' ')) {
            $time = substr($cache, $pos);
        }

        return match ($time) {
            'day', 'days' => $num * 86400,
            'hour', 'hours' => $num * 3600,
            default => $num * 60,
        };
    }

    /**
     * Get accepted type
     *
     * @return array|null
     */
    public function getAccept(): ?array
    {
        return count($this->accept) ? $this->accept : null;
    }

    /**
     * Set accepted types
     *
     * @param string $accept
     *
     * @return void
     */
    public function setAccept(string $accept): void
    {
        // accept map
        $acceptMap = [
            'ANY' => ContentType::ANY,
            'HTML' => ContentType::HTML,
            'JSON' => ContentType::JSON
        ];

        $accept = strtoupper($accept);

        if (isset($acceptMap[$accept])) {
            $this->accept[] = $acceptMap[$accept];
        }
    }

    /**
     * Get Acl privileges
     *
     * @return array|null
     */
    public function getAcl(): ?array
    {
        return count($this->acl) ? $this->acl : null;
    }

    /**
     * Set Acl privileges
     *
     * @param string $acl
     *
     * @return void
     */
    public function setAcl(string $acl): void
    {
        $this->acl[] = $acl;
    }

    /**
     * Get HTTP Method
     *
     * @return array|null
     */
    public function getMethod(): ?array
    {
        return count($this->method) ? $this->method : null;
    }

    /**
     * Set HTTP Method
     *
     * @param string $method
     *
     * @return void
     */
    public function setMethod(string $method): void
    {
        $this->method[] = strtoupper($method);
    }

    /**
     * Get all params
     *
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Set param types
     *
     * @param string $param
     *
     * @return void
     */
    public function setParam(string $param): void
    {
        // prepare params data
        // setup param types
        if (!str_contains($param, '$')) {
            return;
        }

        [$type, $key] = preg_split('/[ $]+/', $param);

        $this->params[$key] = trim($type);
    }

    /**
     * Get Privilege fo ACL
     *
     * @return string|null
     */
    public function getPrivilege(): ?string
    {
        return $this->privilege;
    }

    /**
     * Set Privilege fo ACL allow only one privilege
     *
     * @param string $privilege
     *
     * @return void
     */
    public function setPrivilege(string $privilege): void
    {
        $this->privilege = $privilege;
    }

    /**
     * Get Route
     *
     * @return array|null
     */
    public function getRoute(): ?array
    {
        return count($this->route) ? $this->route : null;
    }

    /**
     * Set Route
     *
     * @param string $route
     *
     * @return void
     */
    public function setRoute(string $route): void
    {
        $this->route[$route] = null;
    }

    /**
     * Init Route
     *
     * @return void
     */
    protected function initRoute(): void
    {
        foreach ($this->route as $route => &$pattern) {
            $pattern = $this->prepareRoutePattern($route);
        }
    }

    /**
     * Prepare Route pattern
     *
     * @param string $route
     *
     * @return string
     */
    protected function prepareRoutePattern(string $route): string
    {
        $pattern = str_replace('/', '\/', $route);

        foreach ($this->getParams() as $param => $type) {
            switch ($type) {
                case 'int':
                case 'integer':
                    $pattern = str_replace("{\$$param}", "(?P<$param>[0-9]+)", $pattern);
                    break;
                case 'float':
                    $pattern = str_replace("{\$$param}", "(?P<$param>[0-9.,]+)", $pattern);
                    break;
                case 'string':
                case 'module':
                case 'controller':
                    $pattern = str_replace(
                        "{\$$param}",
                        "(?P<$param>[a-zA-Z0-9-_.]+)",
                        $pattern
                    );
                    break;
            }
        }
        return '/^' . $pattern . '/i';
    }
}
