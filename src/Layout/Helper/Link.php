<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\View\Helper;

use Bluz\Layout\Layout;
use Bluz\Proxy\Registry;

/**
 * Set or generate <link> code for <head>
 *
 * @param  array  $link
 * @return string|null
 */
return
    function (array $link = null) {
        /**
         * @var Layout $this
         */
        // it's stack for <head>
        $links = Registry::get('layout:link') ? : [];

        if (is_null($link)) {
            // clear system vars
            Registry::set('layout:link', []);
            // prepare to output
            $tags = array_map(
                function ($attr) {
                    return '<link '. $this->attributes($attr) .'/>';
                },
                $links
            );
            $tags = array_unique($tags);
            return implode("\n", $tags);
        }
        $links[] = $link;
        Registry::set('layout:link', $links);
        return null;
    };
