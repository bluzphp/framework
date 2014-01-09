<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Application\Helper;

use Bluz\Application\Exception\RedirectException;

return
    /**
     * redirect to url
     *
     * @param string $url
     * @throws RedirectException
     * @return void
     */
    function ($url) {
        throw new RedirectException($url);
    };
