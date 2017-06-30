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
 * Set or generate <style> code for <head>
 *
 * @param  string $href
 * @param  string $media
 *
 * @return string|null
 */
return
    function ($href = null, $media = 'all') {
        /**
         * @var Layout $this
         */
        // it's stack for <head>
        $headStyle = Registry::get('layout:headStyle') ?: [];

        if (is_null($href)) {
            // clear system vars
            Registry::set('layout:headStyle', []);
            $tags = [];
            foreach ($headStyle as $aHref => $aMedia) {
                $tags[] = $this->style($aHref, $aMedia);
            }
            return implode("\n", $tags);
        }
        $headStyle[$href] = $media;
        Registry::set('layout:headStyle', $headStyle);
        return null;
    };
