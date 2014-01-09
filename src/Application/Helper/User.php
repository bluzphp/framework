<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Application\Helper;

use Bluz\Application\Application;

return
    /**
     * get current user
     *
     * @return \Bluz\Auth\AbstractEntity|null
     */
    function () {
        /** @var Application $this */
        return $this->getAuth() ?
            $this->getAuth()->getIdentity() :
            null;
    };
