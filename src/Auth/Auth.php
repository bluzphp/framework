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
namespace Bluz\Auth;

use Bluz\Common\Options;

/**
 * Auth class
 *
 * @package  Bluz\Auth
 *
 * @author   Anton Shevchuk
 * @created  11.07.11 19:10
 */
class Auth
{
    use Options;

    /**
     * Setup identity
     *
     * @param EntityInterface $identity
     * @return Auth
     */
    public function setIdentity(EntityInterface $identity)
    {
        // save identity to session
        app()->getSession()->identity = $identity;
        // save user agent to session
        app()->getSession()->agent = app()->getRequest()->getServer('HTTP_USER_AGENT');
        return $this;
    }

    /**
     * Return identity if user agent is correct
     *
     * @return EntityInterface|null
     */
    public function getIdentity()
    {
        // check user agent
        if (app()->getSession()->agent == app()->getRequest()->getServer('HTTP_USER_AGENT')) {
            return app()->getSession()->identity;
        } else {
            $this->clearIdentity();
            return null;
        }
    }

    /**
     * Clear identity and user agent information
     *
     * @return Auth
     */
    public function clearIdentity()
    {
        unset(
            app()->getSession()->identity,
            app()->getSession()->agent
        );
        return $this;
    }
}
