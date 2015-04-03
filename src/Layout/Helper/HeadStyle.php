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
namespace Bluz\Layout\Helper;

use Bluz\Layout\Layout;
use Bluz\Proxy\Registry;

return
    /**
     * Set or generate <style> code for <head>
     *
     * @var Layout $this
     * @param string $style
     * @param string $media
     * @return string|null
     */
    function ($style = null, $media = 'all') {
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
            return null;
        }
    };
