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
namespace Bluz\Application\Exception;

/**
 * Redirect Exception
 *
 * @package  Bluz\Application\Exception
 * @author   Anton Shevchuk
 */
class RedirectException extends ApplicationException
{
    /**
     * @var string exception message consist Location data
     */
    protected $message = "Application Redirect";

    /**
     * Redirect HTTP code
     *
     * - 301 Moved Permanently
     * - 302 Moved Temporarily
     * - 307 Temporary Redirect
     *
     * @var integer
     */
    protected $code = 302;

    /**
     * @var string
     */
    protected $url;

    /**
     * Set Url to Redirect
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * getUrl
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}
