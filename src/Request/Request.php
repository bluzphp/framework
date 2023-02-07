<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Request;

use Bluz\Http\MimeType;
use Bluz\Http\RequestMethod;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\UploadedFile;
use Psr\Http\Message\UriInterface;

/**
 * Request Factory
 *
 * @package  Bluz\Request
 * @author   Anton Shevchuk
 */
class Request
{
    /**
     * @var string Request path without baseUrl
     */
    private string $path;

    /**
     * @var array|null Accepted type
     */
    private ?array $accept = null;

    /**
     * @var array|null Accepted languages
     */
    private ?array $language = null;

    /**
     * @var ServerRequest
     */
    private ServerRequest $request;

    /**
     * @param string $baseUrl
     */
    public function __construct(protected string $baseUrl)
    {
        $this->request = RequestFactory::fromGlobals();
    }

    /**
     * @return ServerRequest
     */
    public function getServerRequest(): ServerRequest
    {
        return $this->request;
    }

    /**
     * @param ServerRequest $request
     * @return void
     */
    public function setServerRequest(ServerRequest $request): void
    {
        $this->request = $request;
    }

    /**
     * @return UriInterface
     */
    public function getUri(): UriInterface
    {
        return $this->request->getUri();
    }

    /**
     * Get the request URI without baseUrl
     *
     * @return string
     */
    public function getPath(): string
    {
        $path = $this->getUri()->getPath();
        if (str_starts_with($path, $this->baseUrl)) {
            $path = substr($path, strlen($this->baseUrl));
        }
        return $path;
    }

    /**
     * Retrieve a member of the $_GET super global
     *
     * If no $key is passed, returns the entire $_GET array.
     *
     * @param string|null $key
     * @param string|null $default Default value to use if key not found
     *
     * @return string|array|null Returns null if key does not exist
     */
    public function getQuery(?string $key = null, ?string $default = null): mixed
    {
        return $this->request->getQueryParams()[$key] ?? $default;
    }

    /**
     * Retrieve a member of the $_POST super global
     *
     * If no $key is passed, returns the entire $_POST array.
     *
     * @param string|null $key
     * @param string|null $default Default value to use if key not found
     *
     * @return string|array|null Returns null if key does not exist
     */
    public function getPost(?string $key = null, ?string $default = null): mixed
    {
        return $this->request->getParsedBody()[$key] ?? $default;
    }

    /**
     * Retrieve a member of the $_SERVER super global
     *
     * If no $key is passed, returns the entire $_SERVER array.
     *
     * @param string|null $key
     * @param string|null $default Default value to use if key not found
     *
     * @return string|null Returns null if key does not exist
     */
    public function getServer(?string $key = null, ?string $default = null): ?string
    {
        return $this->request->getServerParams()[$key] ?? $default;
    }

    /**
     * Retrieve a member of the $_COOKIE super global
     *
     * If no $key is passed, returns the entire $_COOKIE array.
     *
     * @param string|null $key
     * @param string|null $default Default value to use if key not found
     *
     * @return string|null Returns null if key does not exist
     */
    public function getCookie(?string $key = null, ?string $default = null): ?string
    {
        return $this->request->getCookieParams()[$key] ?? $default;
    }

    /**
     * Retrieve a member of the $_ENV super global
     *
     * If no $key is passed, returns the entire $_ENV array.
     *
     * @param string|null $key
     * @param string|null $default Default value to use if key not found
     *
     * @return string|null Returns null if key does not exist
     */
    public function getEnv(?string $key = null, ?string $default = null): ?string
    {
        return $_ENV[$key] ?? $default;
    }

    /**
     * Search for a header value
     *
     * @param string $header
     * @param mixed|null $default
     *
     * @return string|null
     */
    public function getHeader(string $header, mixed $default = null): ?string
    {
        $header  = strtolower($header);
        $headers = $this->request->getHeaders();
        $headers = array_change_key_case($headers, CASE_LOWER);
        if (array_key_exists($header, $headers)) {
            return is_array($headers[$header]) ? implode(', ', $headers[$header]) : $headers[$header];
        }
        return $default;
    }

    /**
     * Access values contained in the super-globals as public members
     * Order of precedence: 1. GET, 2. POST
     *
     * @param string $key
     * @param mixed|null $default
     *
     * @return string|array|null
     * @link http://msdn.microsoft.com/en-us/library/system.web.httprequest.item.aspx
     */
    public function getParam(string $key, mixed $default = null): mixed
    {
        return
            $this->getQuery($key) ??
            $this->getPost($key) ??
            $default;
    }

    /**
     * Get all params from GET and POST or PUT
     *
     * @return array
     */
    public function getParams(): array
    {
        $body = (array)$this->request->getParsedBody();
        $query = $this->request->getQueryParams();
        return array_merge([], $body, $query);
    }

    /**
     * Get uploaded file
     *
     * @param string $name
     *
     * @return UploadedFile|null
     */
    public function getFile(string $name): ?UploadedFile
    {
        return $this->request->getUploadedFiles()[$name] ?? null;
    }

    /**
     * Get the client's IP address
     *
     * @param bool $checkProxy
     *
     * @return string|null
     */
    public function getClientIp(bool $checkProxy = true): ?string
    {
        $result = null;
        if ($checkProxy) {
            $result = $this->getServer('HTTP_CLIENT_IP') ?? $this->getServer('HTTP_X_FORWARDED_FOR') ?? null;
        }
        return $result ?? $this->getServer('REMOTE_ADDR');
    }

    /**
     * Get method
     *
     * @return RequestMethod
     */
    public function getMethod(): RequestMethod
    {
        // extract method from params
        $methodName = $this->getParam('_method', $this->request->getMethod());
        return RequestMethod::tryFrom(strtoupper($methodName)) ?? RequestMethod::GET;
    }

    /**
     * Get Accept MIME Type
     *
     * @return array
     */
    public function getAccept(): array
    {
        if (!$this->accept) {
            // get header from request
            $this->accept = $this->parseAcceptHeader($this->getHeader('Accept'));
        }
        return $this->accept;
    }

    /**
     * Get Accept MIME Type
     *
     * @return array
     */
    public function getAcceptLanguage(): array
    {
        if (!$this->language) {
            // get header from request
            $this->language = $this->parseAcceptHeader($this->getHeader('Accept-Language'));
        }
        return $this->language;
    }

    /**
     * parseAcceptHeader
     *
     * @param string|null $header
     *
     * @return array
     */
    private static function parseAcceptHeader(?string $header): array
    {
        // empty array
        $accept = [];

        // check empty
        if (!$header) {
            return $accept;
        }

        // make array from header
        $values = explode(',', $header);
        $values = array_map('trim', $values);

        foreach ($values as $a) {
            // the default quality is 1.
            $q = 1;
            // check if there is a different quality
            if (strpos($a, ';q=') || strpos($a, '; q=')) {
                // divide "mime/type;q=X" into two parts: "mime/type" i "X"
                [$a, $q] = preg_split('/;( ?)q=/', $a);
            }
            // remove other extension
            if (strpos($a, ';')) {
                $a = substr($a, 0, strpos($a, ';'));
            }

            // mime-type $a is accepted with the quality $q
            // WARNING: $q == 0 means, that isn’t supported!
            $accept[$a] = (float)$q;
        }
        arsort($accept);
        return $accept;
    }

    /**
     * Reset accept headers for tests
     *
     * @return void
     */
    public function resetAccept(): void
    {
        $this->accept = null;
    }

    /**
     * Check CLI
     *
     * @return bool
     */
    public function isCli(): bool
    {
        return (PHP_SAPI === 'cli');
    }

    /**
     * Check HTTP
     *
     * @return bool
     */
    public function isHttp(): bool
    {
        return (PHP_SAPI !== 'cli');
    }

    /**
     * Is this a GET method request?
     *
     * @return bool
     */
    public function isGet(): bool
    {
        return ($this->getMethod() === RequestMethod::GET);
    }

    /**
     * Is this a POST method request?
     *
     * @return bool
     */
    public function isPost(): bool
    {
        return ($this->getMethod() === RequestMethod::POST);
    }

    /**
     * Is this a PUT method request?
     *
     * @return bool
     */
    public function isPut(): bool
    {
        return ($this->getMethod() === RequestMethod::PUT);
    }

    /**
     * Is this a DELETE method request?
     *
     * @return bool
     */
    public function isDelete(): bool
    {
        return ($this->getMethod() === RequestMethod::DELETE);
    }

    /**
     * Is the request a Javascript XMLHttpRequest?
     *
     * @return bool
     */
    public function isXmlHttpRequest(): bool
    {
        return ($this->getHeader('X-Requested-With') === 'XMLHttpRequest');
    }
}
