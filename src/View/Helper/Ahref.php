<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\View\Helper;

use Bluz\Proxy\Request;
use Bluz\Proxy\Translator;
use Bluz\View\View;

/**
 * Generate HTML for <a> element
 *
 * @param string $text
 * @param array|string|null $href
 * @param array $attributes HTML attributes
 *
 * @return string
 * @author ErgallM
 *
 */
return
    function (string $text, array|string|null $href, array $attributes = []) {
        // if href is settings for url helper
        if (is_array($href)) {
            $href = $this->url(...$href);
        }

        // href can be null, if access is denied
        if (null === $href) {
            return '';
        }

        $current = Request::getUri()->getPath();

        if (Request::getUri()->getQuery()) {
            $current .= sprintf('?%s', Request::getUri()->getQuery());
        }

        if ($href === $current) {
            if (isset($attributes['class'])) {
                $attributes['class'] .= ' active';
            } else {
                $attributes['class'] = 'active';
            }
        }

        $attributes = $this->attributes($attributes);

        return '<a href="' . $href . '" ' . $attributes . '>' . Translator::translate((string)$text) . '</a>';
    };
