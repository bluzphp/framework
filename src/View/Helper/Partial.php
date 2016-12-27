<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\View\Helper;

use Bluz\View\View;
use Bluz\View\ViewException;

/**
 * Render partial file
 *
 * be careful, method rewrites the View variables with params
 *
 * @param  string $__template
 * @param  array  $__params
 * @return string
 * @throws ViewException
 */
return
    function ($__template, $__params = []) {
        /**
         * @var View $this
         */
        $__file = null;
        if (file_exists($this->path . '/' . $__template)) {
            $__file = $this->path . '/' . $__template;
        } else {
            foreach ($this->partialPath as $__path) {
                if (file_exists($__path . '/' . $__template)) {
                    $__file = $__path . '/' . $__template;
                    break;
                }
            }
        }

        if (is_null($__file)) {
            throw new ViewException("Template '{$__template}' not found");
        }

        if (sizeof($__params)) {
            extract($__params);
        }
        unset($__params);

        ob_start();
        try {
            require $__file;
        } catch (\Exception $e) {
            ob_end_clean();
            throw new ViewException("Template '{$__template}' throw exception: ".$e->getMessage());
        }
        return ob_get_clean();
    };
