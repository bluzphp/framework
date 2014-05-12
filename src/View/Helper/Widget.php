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

use Bluz\Acl\AclException;
use Bluz\View\View;

return
    /**
     * Widget call View Helper
     *
     * Example of usage:
     *     $this->widget($module, $controller, array $params);
     *
     * @param string $module
     * @param string $widget
     * @param array $params
     * @return void
     */
    function ($module, $widget, $params = array()) {
    /** @var View $this */
    $application = app();
    try {
        $widgetClosure = $application->widget($module, $widget);
        call_user_func_array($widgetClosure, $params);
    } catch (AclException $e) {
        // nothing for Acl exception
    } catch (\Exception $e) {
        if (app()->isDebug()) {
            // exception message for developers
            echo
                '<div class="alert alert-error">' .
                '<strong>Widget "' . $module . '/' . $widget . '"</strong>: ' .
                $e->getMessage() .
                '</div>';
        }
    }
    };
