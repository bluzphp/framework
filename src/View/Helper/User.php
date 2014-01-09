<?php
/**
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
     * get current user
     *
     * @return \Bluz\Auth\AbstractRowEntity|null
     */
    function () {
    /** @var View $this */
    return app()->getAuth() ?
        app()->getAuth()->getIdentity() :
        null;
    };
