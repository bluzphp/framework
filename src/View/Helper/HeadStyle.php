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
 * @param  string $style
 * @param  string $media
 * @return string|null
 */
return
    function ($style = null, $media = 'all') {
        /**
         * @var View $this
         */
        if (Application::getInstance()->useLayout()) {
            return Layout::headStyle($style, $media);
        } else {
            // it's just alias to style() call
            return $this->style($style);
        }
    };
