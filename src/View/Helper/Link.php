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
use Bluz\Proxy\Registry;

return
    /**
     * Set or generate <link> code for <head>
     *
     * @var View $this
     * @param array $link
     * @return string|View
     */
    function (array $link = null) {
    if (app()->hasLayout()) {
        // it's stack for <head>
        $links = Registry::get('layout:link') ? : [];

        if (null === $link) {
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
            return $this;
        }
    }
    return '';
    };
