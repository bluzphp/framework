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

    /**
     * clearIdentity
     *
     * @return \Bluz\Auth\AbstractEntity|null
     */
    public function clearIdentity()
    {
        return $this->setIdentity(null);
    }
}
