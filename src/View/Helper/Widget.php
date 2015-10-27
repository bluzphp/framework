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

use Bluz\Application\Application;
use Bluz\Application\Exception\ForbiddenException;
use Bluz\View\View;

return
    /**
     * Widget call
     *
     * Example of usage:
     * <code>
     *     $this->widget($module, $controller, array $params);
     * </code>
     *
     * @param  string $module
     * @param  string $widget
     * @param  array $params
     * @return void
     */
    function ($module, $widget, $params = array()) {
        /**
         * @var View $this
         */
        try {
            $widgetClosure = Application::getInstance()->widget($module, $widget);
            $widgetClosure(...$params);
        } catch (ForbiddenException $e) {
            // nothing for Acl exception
        } catch (\Exception $e) {
            echo $this->exception($e);
        }
    };
