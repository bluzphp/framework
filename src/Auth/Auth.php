<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Auth;

use Bluz\Common\Options;
use Bluz\Proxy\Request;
use Bluz\Proxy\Session;

/**
 * Auth class
 *
 * @package  Bluz\Auth
 * @author   Anton Shevchuk
 * @link     https://github.com/bluzphp/framework/wiki/Auth
 */
class Auth
{
    use Options;

    /**
     * @var IdentityInterface Instance of EntityInterface
     */
    protected $identity;

    /**
     * Setup identity
     *
     * @param  IdentityInterface $identity
     *
     * @return void
     */
    public function setIdentity(IdentityInterface $identity) : void
    {
        // save identity to Auth
        $this->identity = $identity;
        // regenerate session
        if (PHP_SAPI !== 'cli') {
            Session::regenerateId();
        }
        // save identity to session
        Session::set('auth:identity', $identity);
        // save user agent to session
        Session::set('auth:agent', Request::getServer('HTTP_USER_AGENT'));
    }

    /**
     * Return identity if user agent is correct
     *
     * @return IdentityInterface|null
     */
    public function getIdentity() : ?IdentityInterface
    {
        if (!$this->identity) {
            // check user agent
            if (Session::get('auth:agent') === Request::getServer('HTTP_USER_AGENT')) {
                $this->identity = Session::get('auth:identity');
            } else {
                $this->clearIdentity();
            }
        }
        return $this->identity;
    }

    /**
     * Clear identity and user agent information
     *
     * @return void
     */
    public function clearIdentity() : void
    {
        $this->identity = null;
        Session::delete('auth:identity');
        Session::delete('auth:agent');
    }
}
