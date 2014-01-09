<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\View\Helper;

use Bluz\Application\Application;
use Bluz\View\View;
use Bluz\View\ViewException;

return
    /**
     * partial
     *
     * be careful, method rewrites the View variables with params
     *
     * @param string $__template
     * @param array $__params
     * @throws ViewException
     * @return string
     */
    function ($__template, $__params = array()) {
    /** @var View $this */
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
    if (!$__file) {
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
