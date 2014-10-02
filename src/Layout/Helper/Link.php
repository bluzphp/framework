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

use Bluz\Layout\Layout;
use Bluz\Proxy\Registry;

return
    /**
     * Set or generate <link> code for <head>
     *
     * @var Layout $this
     * @param array $link
     * @return string|null
     */
    function (array $link = null) {
        // it's stack for <head>
        $links = Registry::get('layout:link') ? : [];

        if (is_null($link)) {
            // prepare to output
            $links = array_map(
                function ($attr) {
                    return '<link '. $this->attributes($attr) .'/>';
                },
                $links
            );
            // clear system vars
            Registry::set('layout:link', []);
            $links = array_unique($links);
            return join("\n", $links);
        } else {
            $links[] = $link;
            Registry::set('layout:link', $links);
            return null;
        }
    };
