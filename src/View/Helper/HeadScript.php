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
     * @param string $script
     * @return string|View
     */
    function ($script = null) {
    /** @var View $this */
    if (app()->hasLayout()) {
        // it's stack for <head>
        $headScripts = app()->getRegistry()->__get('layout:headScripts') ? : [];

        if (null === $script) {
            $headScripts = array_unique($headScripts);
            // clear system vars
            app()->getRegistry()->__set('layout:headScripts', []);
            $headScripts = array_map([$this, 'script'], $headScripts);
            return join("\n", $headScripts);
        } else {
            $headScripts[] = $script;
            app()->getRegistry()->__set('layout:headScripts', $headScripts);
            return $this;
        }
    } else {
        // it's just alias to script() call
        return $this->script($script);
    }
    };
