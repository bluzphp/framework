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
namespace Bluz\View\Helper;

use Bluz\Proxy\Request;
use Bluz\View\View;

return

    /**
     * Get baseUrl
     *
     * @var View $this
     * @param string $file
     * @return string
     */
    function ($file = null) {
        // setup baseUrl
        if (!$this->baseUrl) {
            $this->baseUrl = Request::getBaseUrl();
            // clean script name
            if (isset($_SERVER['SCRIPT_NAME'])
                && ($pos = strripos($this->baseUrl, basename($_SERVER['SCRIPT_NAME']))) !== false
            ) {
                $this->baseUrl = substr($this->baseUrl, 0, $pos);
            }
        }

        // Remove trailing slashes
        if (null !== $file) {
            $file = ltrim($file, '/\\');
        }

        return rtrim($this->baseUrl, '/') . '/' . $file;
    };
