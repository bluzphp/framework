<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\View\Helper;

use Bluz\Proxy\Router;
use Bluz\View\View;

/**
 * Get baseUrl
 *
 * @param string|null $file
 *
 * @return string
 */
return
    function (?string $file = null) {
        // setup baseUrl
        if (!$this->baseUrl) {
            $this->baseUrl = Router::getBaseUrl();
            // clean script name
            if (
                isset($_SERVER['SCRIPT_NAME'])
                && ($pos = strripos($this->baseUrl, basename($_SERVER['SCRIPT_NAME']))) !== false
            ) {
                $this->baseUrl = substr($this->baseUrl, 0, $pos);
            }
        }

        // Remove trailing slashes
        if (null !== $file) {
            $file = ltrim($file, '/\\');
        }

        return str_trim_end($this->baseUrl, '/') . $file;
    };
