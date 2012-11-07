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

use Bluz\Exception;

/**
 * LDAP Connection class. Use to connect to LDAP and make search.
 *
 * @category Bluz
 * @package Ldap
 */
class Connector implements LdapIterator
{
    use \Bluz\Package;

    /**
     * Connection resource
     *
     * @var resource
     */
    protected $connection = null;

    /**
     * Hostname of LDAP server
     *
     * @var string
     */
    protected $host = null;

    /**
     * Domain for LDAP server
     *
     * @var string
     */
    protected $domain = null;

    /**
     * search baseDn for LDAP server
     *
     * @var string
     */
    protected $baseDn = null;

    /**
     * Is connected
     *
     * @var boolean
     */
    protected $binded = false;

    /**
     * Search result resource
     *
     * @var resource
     */
    protected $resource = null;

    /**
     * set Host param
     *
     * @param string $host
     * @throws LdapException
     * @return \Bluz\Ldap\Connector
     */
    public function setHost($host)
    {
        if (empty($host)) {
            throw new LdapException('LDAP connection can\'t be initialized: required host value');
        }
        $this->host = $host;
        return $this;
    }

    /**
     * set Host param
     *
     * @param $domain
     * @return \Bluz\Ldap\Connector
     *
     * @internal param string $host
     */
    public function setDomain($domain)
    {
        if (!empty($domain)) {
            $this->domain = $domain;
        }
        return $this;
    }

    /**
     * set baseDn param for search attr
     *
     * @param string $baseDn
     * @throws LdapException
     * @return \Bluz\Ldap\Connector
     */
    public function setBaseDn($baseDn)
    {
        if (empty($baseDn)) {
            throw new LdapException('LDAP Search can\'t be initialized without "baseDn" search params');
        }
        $this->baseDn = $baseDn;
        return $this;
    }

    /**
     * Unlink in destructor
     *
     */
    public function __destruct()
    {
        if ($this->binded) {
            ldap_unbind($this->connection);
        }
    }

    /**
     * Connect to LDAP server
     *
     * @throws LdapException
     */
    public function connect()
    {
        if (!$this->host) {
            throw new LdapException('LDAP connection can\'t be initialized: required host');
        }
        if (!($this->connection = ldap_connect($this->host))) {
            throw new LdapException("Can't connect to " . $this->host);
        }
    }

    /**
     * Bind username and pass on server
     *
     * @param string $username
     * @param string $password
     * @throws LdapException
     * @return bool
     */
    public function bind($username, $password)
    {
        $this->binded = false;
        if ($username && $password) {
            $username = $username . ( ($this->domain) ? ("@" . $this->domain) : ("") );
            $this->binded = @ldap_bind($this->connection, $username, $password);
            $errorMsg = "Can't connect with " . $username . "/"
                    . str_repeat("*", strlen($password)) . " on " . $this->host;
        } else {
            $this->binded = @ldap_bind($this->connection);
            $errorMsg = "Can't connect anonymously on " . $this->host;
        }
        $errors = ldap_errno($this->connection);
        if ($errors != 0x00) {
            throw new LdapException($errorMsg);
        }
        return $this->binded;
    }

    /**
     * Do search. N.B!: Results are not returning.
     * Use getSearch** methods to get results.
     *
     * @param string $filter
     * @param string $attributes
     * @throws LdapException
     * @return \Bluz\Ldap\Entries\Entries|void
     */
    public function doSearch($filter, $attributes = null)
    {
        if (!$this->binded) {
            throw new LdapException("Can't do search. LDAP not initialized.");
        }
        // TODO: WTF? check condition
        if (!is_null($attributes)) {
            $this->resource = @ldap_search(
                $this->connection, $this->baseDn, $filter, $attributes
            );
        } else {
            $this->resource = @ldap_search(
                $this->connection, $this->baseDn, $filter
            );
        }

        if (!$this->resource) {
            throw new LdapException("Search error: '" . ldap_error($this->connection) . "'");
        }
    }

    /**
     * Return search entries
     *
     * @return Entries\Entries
     */
    public function getSearchEntries()
    {
        if (!$this->resource) {
            return false;
        }
        return new Entries\Entries(ldap_get_entries($this->connection, $this->resource));
    }
}