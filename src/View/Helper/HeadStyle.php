<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\View\Helper;

use Bluz\Application\Application;
use Bluz\Proxy\Layout;
use Bluz\View\View;

/**
 * Set or generate <style> code for <head>
 *
 * @param  string $href
 * @param  string $media
 * @return string|null
 */
return
    function ($href = null, $media = 'all') {
        /**
         * @var View $this
         */
        if (Application::getInstance()->useLayout()) {
            return Layout::headStyle($href, $media);
        }
        // it's just alias to style() call
        return $this->style($href, $media);
    };
