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
     * Set or generate <style> code for <head>
     *
     * @var View $this
     * @param string $script
     * @return string|View
     */
    function ($style = null, $media = 'all') {
    if (app()->hasLayout()) {
        // it's stack for <head>
        $headStyle = Registry::get('layout:headStyle') ? : [];

        if (null === $style) {
            // clear system vars
            Registry::set('layout:headStyle', []);
            array_walk(
                $headStyle,
                function (&$item, $key) {
                    $item = $this->style($key, $item);
                }
            );
            return join("\n", $headStyle);
        } else {
            $headStyle[$style] = $media;
            Registry::set('layout:headStyle', $headStyle);
            return $this;
        }
    } else {
        // it's just alias to style() call
        return $this->style($style);
    }
    };
