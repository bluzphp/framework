<?php
/**
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
     * dispatch
     *
     * <code>
     * $this->dispatch($module, $controller, array $params);
     * </code>
     *
     * @param string $module
     * @param string $controller
     * @param array $params
     * @return View|null
     */
    function ($module, $controller, $params = array()) {
    /** @var View $this */
    $application = app();
    try {
        $view = $application->dispatch($module, $controller, $params);
    } catch (AclException $e) {
        // nothing for Acl exception
        return null;
    } catch (\Exception $e) {
        if (app()->isDebug()) {
            // exception message for developers
            return
                '<div class="alert alert-error">' .
                '<strong>Dispatch of "' . $module . '/' . $controller . '"</strong>: ' .
                $e->getMessage() .
                '</div>';
        } else {
            // nothing for production
            return null;
        }
    }

    // run closure
    if ($view instanceof \Closure) {
        return $view();
    }
    return $view;
    };
