<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\View\Helper;

/**
 * Generate HTML for <script> element with inline code
 *
 * @param string $code
 *
 * @return string
 */
return
    function (string $code) {
        return "<script type=\"text/javascript\">\n"
            . "<!--\n"
            . "$code\n"
            . "//-->\n"
            . "</script>";
    };
