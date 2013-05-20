<?php
/**
 * Copyright (c) 2013 by Bluz PHP Team
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

use Bluz\Application;
use Bluz\Db\Row;

/**
 *
 */
abstract class AbstractRowEntity extends Row implements EntityInterface
{
    /**
     * Get roles
     *
     * @return array
     */
    abstract public function getPrivileges();

    /**
     * Can entity login
     *
     * @throws AuthException
     * @return boolean
     */
    abstract public function tryLogin();

    /**
     * Has role a privilege
     *
     * @param string $module
     * @param string $privilege
     * @return boolean
     */
    public function hasPrivilege($module, $privilege)
    {
        $privileges = $this->getPrivileges();

        foreach ($privileges as $rule) {
            if ($rule->module == $module
                && $rule->privilege == $privilege) {
                return true;
            }
        }

        return false;
    }

    /**
     * Login
     * @throw AuthException
     */
    public function login()
    {
        $this->tryLogin();
        Application::getInstance()->getAuth()->setIdentity($this);
    }
}