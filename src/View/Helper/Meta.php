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
     * @param string|array|null $name
     * @param string|null $content
     * @return string|View
     */
    function ($name = null, $content = null) {
    /** @var View $this */
    if (app()->hasLayout()) {
        // it's stack for <head>
        $meta = app()->getRegistry()->__get('layout:meta') ? : [];

        if ($name && $content) {
            $meta[] = ['name' => $name, 'content' => $content];
            app()->getRegistry()->__set('layout:meta', $meta);
            return $this;
        } elseif (is_array($name)) {
            $meta[] = $name;
            app()->getRegistry()->__set('layout:meta', $meta);
            return $this;
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
                app()->getRegistry()->__set('layout:meta', []);
                return join("\n", $meta);
            }
        }
    }
    return '';
    };
