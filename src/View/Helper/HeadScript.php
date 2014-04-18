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
     * @return string|void
     */
    function ($script = null) {
    /** @var View $this */
    if (app()->hasLayout()) {
        // it's stack for <head>
        $view = app()->getLayout();

        $headScripts = $view->system('headScripts') ? : [];

        if (null === $script) {
            $headScripts = array_unique($headScripts);
            // clear system vars
            $view->system('headScripts', []);

            $headScripts = array_map([$this, 'script'], $headScripts);
            return join("\n", $headScripts);
        } else {
            $headScripts[] = $script;
            $view->system('headScripts', $headScripts);
        }
    } else {
        // it's just alias to script() call
        return $this->script($script);
    }
    };
