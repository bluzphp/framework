<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Cli;

use Bluz\Request\AbstractRequest;
use Bluz\Request\RequestException;

/**
 * CLI Request
 *
 * @package  Bluz\Cli
 *
 * @author   Anton Shevchuk
 * @created  06.07.11 16:59
 */
class Request extends AbstractRequest
{
    /**
     * Constructor
     *
     * @example $> php bin/cli.php --uri "/index/index/?foo=bar"
     */
    public function __construct()
    {
        $this->method = self::METHOD_CLI;

        $arguments = getopt("u:", ["uri:"]);

        if (!array_key_exists('u', $arguments) && !array_key_exists('uri', $arguments)) {
            throw new RequestException('Attribute `--uri` is required');
        }

        $uri = isset($arguments['u']) ? $arguments['u'] : $arguments['uri'];

        $path = parse_url($uri, PHP_URL_PATH);
        $query = parse_url($uri, PHP_URL_QUERY);

        // two syntax should be has equal result
        //     /module/controller?foo=bar
        //     /module/controller/?foo=bar
        $uri = rtrim($path, '/') .'/'. ($query ? '?'.$query : '');

        $this->setRequestUri($uri);

        if ($query) {
            parse_str($query, $params);
            if (is_array($params)) {
                $this->setParams($params);
            }
        }
    }

    /**
     * Get the request URI
     * @return string
     */
    public function getRequestUri()
    {
        if ($this->requestUri === null) {
            $this->setRequestUri('');
        }
        return $this->requestUri;
    }

    /**
     * Get the request URI
     * @return string
     */
    public function getCleanUri()
    {
        return ltrim($this->getRequestUri(), '/');
    }

    /**
     * Get the base URL
     * @return string
     */
    public function getBaseUrl()
    {
        if (null === $this->baseUrl) {
            $this->setBaseUrl('/');
        }
        return $this->baseUrl;
    }

    /**
     * Get the client's IP address
     * @return string
     */
    public function getClientIp()
    {
        return '127.0.0.1';
    }
}
