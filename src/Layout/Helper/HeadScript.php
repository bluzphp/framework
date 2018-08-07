<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Layout\Helper;

use Bluz\Layout\Layout;
use Bluz\Proxy\Registry;

/**
 * Set or generate <script> code for <head>
 *
 * @param  string $src
 * @param  array  $attributes
 *
 * @return null|string
 */
return
    function ($src = null, array $attributes = []) {
        /**
         * @var Layout $this
         */
        // it's stack for <head>
        $headScripts = Registry::get('layout:headScripts') ?: [];

        if (null === $src) {
            // clear system vars
            Registry::set('layout:headScripts', []);
            $tags = [];
            foreach ($headScripts as $aSrc => $aData) {
                $tags[] = $this->script($aSrc, $aData);
            }
            return implode("\n", $tags);
        }
        $headScripts[$src] = $attributes;
        Registry::set('layout:headScripts', $headScripts);
        return null;
    };
