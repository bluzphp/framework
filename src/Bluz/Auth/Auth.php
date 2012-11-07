<?php
/**
 * Copyright (c) 2012 by Bluz PHP Team
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * @namespace
 */
namespace Bluz\Auth;

/**
 * Auth support
 *  - DB adapter
 *  - LDAP adapter
 *
 * @category Bluz
 * @package  Auth
 *
 * @author   Anton Shevchuk
 * @created  11.07.11 19:10
 */
class Auth
{
    use \Bluz\Package;

    /**
     * @var AbstractAdapter
     */
    protected $adapter;

    /**
     * setAdapter
     *
     * @param array $options
     * @throws AuthException
     * @return Auth
     */
    public function setAdapter($options)
    {
        if (!isset($options['name'])) {
            throw new AuthException('Auth: adapter name is not present in configuration');
        }

        if (!isset($options['options']) or !is_array($options['options'])) {
            throw new AuthException('Auth: adapter settings is not present in configuration');
        }

        $className = '\\Bluz\\Auth\\Adapter\\'.ucfirst(strtolower($options['name']));
        $this->adapter = new $className($options['options']);
        $this->adapter->setAuth($this);
        return $this;
    }

    /**
     * authenticate
     *
     * @param string $login
     * @param string $password
     * @param \Bluz\Auth\AbstractEntity $entity
     * @return bool
     */
    public function authenticate($login, $password, \Bluz\Auth\AbstractEntity $entity = null)
    {
        $result = $this->adapter->authenticate($login, $password, $entity);
        if ($result) {
            $this->setIdentity($entity);
        }
        return $result;
    }

    /**
     * setIdentity
     *
     * @param \Bluz\Auth\AbstractEntity $identity
     * @return Auth
     */
    public function setIdentity($identity)
    {
        $this->getApplication()->getSession()->identity = $identity;
        return $this;
    }

    /**
     * getIdentity
     *
     * @return \Bluz\Auth\AbstractEntity|null
     */
    public function getIdentity()
    {
        return $this->getApplication()->getSession()->identity;
    }
}
