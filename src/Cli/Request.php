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
     * @example $> php index.php --uri "/index/index/?foo=bar"
     */
    public function __construct()
    {
        $this->method = self::METHOD_CLI;

        $args = $_SERVER["argv"];

        // unset script name
        unset($args[0]);

        if (!in_array('--uri', $args)) {
            throw new RequestException('Attribute "--uri" is required');
        }

        $uriOrder = array_search('--uri', $args) + 1;

        if (isset($args[$uriOrder])) {
            $uri = $args[$uriOrder];
            $this->setRequestUri($uri);
            if ($query = parse_url($uri, PHP_URL_QUERY)) {
                parse_str($query, $params);
                $this->setParams($params);
            }
        } else {
            throw new RequestException('Attribute "--uri" is required');
        }
    }

    /**
     * Get the request URI.
     *
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
     * Get the request URI.
     *
     * @return string
     */
    public function getCleanUri()
    {
        return ltrim($this->getRequestUri(), '/');
    }

    /**
     * Get the base URL.
     *
     * @return string
     */
    public function getBaseUrl()
    {
        if (null === $this->baseUrl) {
            $this->setBaseUrl('');
        }
        return $this->baseUrl;
    }

    /**
     * Get the client's IP address
     *
     * @param  boolean $checkProxy
     * @return string
     */
    public function getClientIp($checkProxy = true)
    {
        return '127.0.0.1';
    }
}
