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
 * @link     https://github.com/bluzphp/framework/wiki/Auth
 *
 * @author   Anton Shevchuk
 * @created  11.07.11 19:10
 */
class Auth
{
    use Options;

    /**
     * @var EntityInterface Instance of EntityInterface
     */
    protected $identity;

    /**
     * Setup identity
     * @api
     * @param EntityInterface $identity
     * @return void
     */
    public function setIdentity(EntityInterface $identity)
    {
        // save identity to Auth
        $this->identity = $identity;
        // save identity to session
        Session::set('auth:identity', $identity);
        // save user agent to session
        Session::set('auth:agent', Request::getServer('HTTP_USER_AGENT'));
    }

    /**
     * Return identity if user agent is correct
     * @api
     * @return EntityInterface|null
     */
    public function getIdentity()
    {
        if (!$this->identity) {
            // check user agent
            if (Session::get('auth:agent') == Request::getServer('HTTP_USER_AGENT')) {
                $this->identity = Session::get('auth:identity');
            } else {
                $this->clearIdentity();
            }
        }
        return $this->identity;
    }

    /**
     * Clear identity and user agent information
     * @api
     * @return void
     */
    public function clearIdentity()
    {
        $this->identity = null;
        Session::delete('auth:identity');
        Session::delete('auth:agent');
    }
}
