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
     * Set or generate <script> code for <head>
     *
     * @var View $this
     * @param string $script
     * @return string|View
     */
    function ($script = null) {
    if (app()->hasLayout()) {
        // it's stack for <head>
        $headScripts = Registry::get('layout:headScripts') ? : [];

        if (null === $script) {
            $headScripts = array_unique($headScripts);
            // clear system vars
            Registry::set('layout:headScripts', []);
            $headScripts = array_map([$this, 'script'], $headScripts);
            return join("\n", $headScripts);
        } else {
            $headScripts[] = $script;
            Registry::set('layout:headScripts', $headScripts);
            return $this;
        }
    } else {
        // it's just alias to script() call
        return $this->script($script);
    }
    };
