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

use Bluz\Http\StatusCode;

/**
 * Redirect Exception
 *
 * @package  Bluz\Application\Exception
 * @author   Anton Shevchuk
 */
class RedirectException extends ApplicationException
{
    /**
     * Redirect HTTP code
     *
     * - 301 Moved Permanently
     * - 302 Moved Temporarily / Found
     * - 307 Temporary Redirect
     *
     * @var integer
     */
    protected $code = StatusCode::FOUND;

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
