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
namespace Bluz\Ldap;

/**
 * Ldap
 *
 * <pre>
 * <code>
 * $ldap->setHost('x.x.x.x');
 * $ldap->setDomain('somedomain.com');
 * $ldap->setBaseDn('OU=Sites,DC=somedomain,DC=com');
 *
 * // check credentials
 * try{
 *      $ldap->checkAuth('username', 'password');
 * } catch (Exception $err) {
 *      print($err->getMessage());
 * }
 *
 * // check user login existance on LDAP
 * try{
 *      $searchLogin = 'another username';
 *      $filter = "samaccountname=" . $searchLogin . "*";
 *      $attribs = array("login" => "samaccountname");
 *      $ldap->processSearch('username', 'password', $filter, $attribs);
 * } catch (Exception $err) {
 *      print($err->getMessage());
 * }
 *
 * // get user Name and Email from LDAP
 * try{
 *      $searchLogin = 'another username';
 *      $filter = "samaccountname=" . $searchLogin . "*";
 *      $atr = array("name"  => "name", "login" => "samaccountname", "mail"  => "mail");
 *      $ldap->processSearch('username', 'password', $filter, $attribs);
 * } catch (Exception $err) {
 *      print($err->getMessage());
 * }
 * </code>
 * </pre>
 *
 * @category Bluz
 * @package  Ldap
 */
class Ldap
{
    /**
     * @var array
     */
    private $connectors = array();

    /**
     * init connector with options
     * @param $host
     * @param $domain
     * @param $baseDn
     */
    public function initConnector($host, $domain, $baseDn)
    {
        $connector = new \Bluz\Ldap\Connector();
        $connector->setHost($host)->setDomain($domain)->setBaseDn($baseDn)->connect();
        $this->connectors[] = $connector;
    }

    /**
     * check user can authenticate to Ldap
     *
     * @param string $loginName
     * @param string $pass
     * @return bool
     * @access public
     */
    public function checkAuth($loginName, $pass)
    {
        // bind
        foreach($this->connectors as $connector) {
            /* @var \Bluz\Ldap\Connector $connector */
            if ($connector->bind($loginName, $pass)) {
                return true;
            }
        }
        return false;
    }

    /**
     * search entry in one if domains
     *
     * @param string $login
     * @param string $pass
     * @param string $filter
     * @param array  $attribs
     * @return array|bool
     * @access public
     */
    public function processSearch($login, $pass, $filter, $attribs = array())
    {
        foreach ($this->connectors as $connector) {
            /* @var \Bluz\Ldap\Connector $connector */
            if ($connector->bind($login, $pass)) {
                $connector->doSearch($filter, array_values($attribs));
                $entries = $connector->getSearchEntries();
                if ($entries->count > 0) {
                    $struct = array();
                    for ($i = 0; $i < $entries->count; $i++) {
                        $entry = $entries[$i];
                        foreach ($attribs as $key => $value) {
                            $struct[$i][$key] = $this->getStringValue($entry->search($value, true));
                        }
                    }
                    return $struct;
                }
            }
        }
        return false;
    }

    /**
     * convert found entity to string
     *
     * @param array $arrayValue
     * @return string
     */
    private function getStringValue($arrayValue)
    {
        if (sizeof($arrayValue) > 0) {
            $arrayValue = array_pop($arrayValue);
            unset($arrayValue['count']);
        }

        return implode('', $arrayValue);
    }
}