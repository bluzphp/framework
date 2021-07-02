<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\View\Helper;

use Bluz\View\View;

/**
 * Generate HTML for <style> or <link> element
 *
 * @param string $href
 * @param string $media
 *
 * @return string
 */
return
    function (string $href, string $media = 'all') {
        /**
         * @var View $this
         */
        if (
            strpos($href, 'http://') !== 0
            && strpos($href, 'https://') !== 0
            && strpos($href, '//') !== 0
        ) {
            $href = $this->baseUrl($href);
        }
        return "<link href=\"$href\" rel=\"stylesheet\" media=\"$media\"/>\n";
    };
