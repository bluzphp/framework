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
 * @param  array  $attributes HTML attributes
 * @return string
 */
return
    function ($script, array $attributes = []) {
        /**
         * @var View $this
         */
        $attributes = $this->attributes($attributes);
        if ('.js' == substr($script, -3)) {
            if (strpos($script, 'http://') !== 0
                && strpos($script, 'https://') !== 0
                && strpos($script, '//') !== 0
            ) {
                $script = $this->baseUrl($script);
            }
            return "<script src=\"$script\" $attributes></script>\n";
        } else {
            return "<script type=\"text/javascript\" $attributes>\n"
            . "<!--\n"
            . "$script\n"
            . "//-->\n"
            . "</script>";
        }
    };
