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
use Bluz\Proxy\Request;
use Bluz\Proxy\Session;

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
     * @param EntityInterface $identity
     * @return void
     */
    public function setIdentity(EntityInterface $identity)
    {
        // save identity to session
        Session::set('auth:identity', $identity);
        // save user agent to session
        Session::set('auth:agent', Request::getServer('HTTP_USER_AGENT'));
    }

    /**
     * Return identity if user agent is correct
     * @return EntityInterface|null
     */
    public function getIdentity()
    {
        // check user agent
        if (Session::get('auth:agent') == Request::getServer('HTTP_USER_AGENT')) {
            return Session::get('auth:identity');
        } else {
            $this->clearIdentity();
            return null;
        }
    }

    /**
     * Clear identity and user agent information
     * @return void
     */
    public function clearIdentity()
    {
        Session::delete('auth:identity');
        Session::delete('auth:agent');
    }
}
