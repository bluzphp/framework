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
namespace Bluz\Rcl;

/**
 * Rcl
 *
 * @category Bluz
 * @package  Rcl
 */
class Rcl
{
    use \Bluz\Package;

    /**
     * @var array
     */
    protected $assertions = array();

    /**
     * Add
     *
     * @param Assertion $assertion
     * @return self
     */
    public function addAssertion(Assertion $assertion)
    {
        array_unshift($this->assertions, $assertion);
        return $this;
    }

    /**
     * Is allowed
     *
     * @param string                    $privilege
     * @param string                    $resourceType
     * @param integer                   $resourceId
     * @internal param int $privilegeId
     * @throws RclException
     * @return boolean
     */
    public function isAllowed($privilege, $resourceType, $resourceId)
    {
        // check overloaded rcl
        foreach ($this->assertions as $assertion) {
            /* @var Assertion $assertion */
            $result = $assertion->isAllowed($privilege, $resourceType, $resourceId);
            if (null !== $result) {
                return $result;
            }
        }

        // check rcl by type + uid
        if ($resourceType) {
            $user = $this->getApplication()->getAuth()->getIdentity();
            if (!$user || !$user->getRole() || !$user->hasResource($resourceType, $resourceId)) {
                return false;
            }
        }

        return true;
    }
}
