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

use Bluz\View\View;

return

    /**
     * baseUrl
     *
     * @param string $file
     * @return string
     */
    function ($file = null) {
    /** @var View $this */
    // setup baseUrl
    if (!$this->baseUrl) {
        $this->baseUrl = app()
            ->getRequest()
            ->getBaseUrl();
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
