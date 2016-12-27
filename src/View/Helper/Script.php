<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\View\Helper;

use Bluz\View\View;

/**
 * Generate HTML for <script> element
 *
 * @param  string $script
 * @return string
 */
return
    function ($script) {
        /**
         * @var View $this
         */
        if ('.js' == substr($script, -3)) {
            if (strpos($script, 'http://') !== 0
                && strpos($script, 'https://') !== 0
                && strpos($script, '//') !== 0
            ) {
                $script = $this->baseUrl($script);
            }
            return "<script src=\"$script\"></script>\n";
        } else {
            return "<script type=\"text/javascript\">\n"
            . "<!--\n"
            . "$script\n"
            . "//-->\n"
            . "</script>";
        }
    };
