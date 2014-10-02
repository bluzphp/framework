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
     * Set or generate <meta> code for <head>
     *
     * @var Layout $this
     * @param string|array|null $name
     * @param string|null $content
     * @return string|null
     */
    function ($name = null, $content = null) {
        // it's stack for <head>
        $meta = Registry::get('layout:meta') ? : [];

        if ($name && $content) {
            $meta[] = ['name' => $name, 'content' => $content];
            Registry::set('layout:meta', $meta);
            return null;
        } elseif (is_array($name)) {
            $meta[] = $name;
            Registry::set('layout:meta', $meta);
            return null;
        } elseif (!$name && !$content) {
            if (sizeof($meta)) {
                // prepare to output
                $meta = array_map(
                    function ($attr) {
                        return '<meta '. $this->attributes($attr) .'/>';
                    },
                    $meta
                );
                // clear system vars
                Registry::set('layout:meta', []);
                return join("\n", $meta);
            } else {
                return '';
            }
        }
    };
