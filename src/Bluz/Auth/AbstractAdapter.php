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
 * Adapter
 *
 * @category Bluz
 * @package  Auth
 *
 * @author   Anton Shevchuk
 * @created  12.07.11 15:33
 */
abstract class AbstractAdapter
{
    use \Bluz\Package;

    /**
     * @var Auth
     */
    protected $auth;

    /**
     * authenticate
     *
     * @param string $login
     * @param string $password
     * @param \Bluz\Auth\AbstractEntity $entity
     * @return bool
     */
    abstract function authenticate($login, $password, \Bluz\Auth\AbstractEntity $entity = null);

    /**
     * setAuth
     *
     * @param $auth
     * @return Table
     */
    public function setAuth($auth)
    {
        $this->auth = $auth;
        return $this;
    }

    /**
     * getAuth
     *
     * @throws AuthException
     * @return Auth
     */
    public function getAuth()
    {
        if (!$this->auth) {
            throw new AuthException('Auth instance not found in Auth Adapter');
        }
        return $this->auth;
    }


}
