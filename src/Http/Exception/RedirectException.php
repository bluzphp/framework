<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Http\Exception;

use Bluz\Http\StatusCode;

/**
 * Redirect Exception
 *
 * @package  Bluz\Http\Exception
 * @author   Anton Shevchuk
 */
class RedirectException extends HttpException
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
    public function setUrl($url): void
    {
        $this->url = $url;
    }

    /**
     * getUrl
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }
}
