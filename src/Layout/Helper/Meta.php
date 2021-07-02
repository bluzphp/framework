<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\View\Helper;

use Bluz\Proxy\Registry;

/**
 * Set or generate <meta> code for <head>
 *
 * @param string|array|null $name
 * @param string|null $content
 *
 * @return string|null
 */
return
    function ($name = null, ?string $content = null) {
        // it's stack for <head>
        $meta = Registry::get('layout:meta') ?: [];

        if (null === $name && null === $content) {
            // clear system vars
            Registry::set('layout:meta', []);
            // prepare to output
            $tags = [];
            foreach ($meta as $aName => $aContent) {
                $tags[] = '<meta name="' . $aName . '" ' .
                    'content="' . htmlspecialchars((string)$aContent, ENT_QUOTES) . '"/>';
            }
            return implode("\n", $tags);
        }

        if (null === $name) {
            // if exists only $content, do nothing
            return null;
        }
        $meta[$name] = $content;
        Registry::set('layout:meta', $meta);
        return null;
    };
