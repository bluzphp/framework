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
namespace Bluz\Auth\Adapter;

use Bluz\Auth\AbstractAdapter;

/**
 * Ldap
 *
 * @category Bluz
 * @package  Auth
 *
 * @author   Anton Shevchuk
 * @created  27.09.11 13:39
 */
class Ldap extends AbstractAdapter
{
    /**
     * @var string
     */
    protected $host = '';

    /**
     * @var string
     */
    protected $domain = '';

    /**
     * @var string
     */
    protected $baseDn = '';

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
        /*$result = null;
        $ldap = $this->getAuth()
            ->getApplication()
            ->getLdap();*/
        $ldap = new \Bluz\Ldap\Ldap();

        $ldap->initConnector($this->host, $this->domain, $this->baseDn);

        return $ldap->checkAuth($login, $password);
    }

    /**
     * setHost
     *
     * @param string $name
     * @return Ldap
     */
    public function setHost($name)
    {
        $this->host = $name;
        return $this;
    }

    /**
     * setDomain
     *
     * @param string $name
     * @return Ldap
     */
    public function setDomain($name)
    {
        $this->domain = $name;
        return $this;
    }

    /**
     * setBaseDn
     *
     * @param string $name
     * @return Ldap
     */
    public function setBaseDn($name)
    {
        $this->baseDn = $name;
        return $this;
    }
}
