<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */
namespace Bluz\Auth;

use Bluz\Db\Table;

/**
 * Abstract class for Auth\Table
 *
 * @package  Bluz\Auth
 *
 * @method   static AbstractRow findRow($primaryKey)
 * @see      Bluz\Db\Table::findRow()
 *
 * @method   static AbstractRow findRowWhere($whereList)
 * @see      Bluz\Db\Table::findRowWhere()
 *
 * @author   Anton Shevchuk
 * @created  12.07.11 15:28
 */
abstract class AbstractTable extends Table
{
    /**
     * Types
     */
    const TYPE_ACCESS = 'access';
    const TYPE_REQUEST = 'request';

    /**
     * Providers
     *  - equals - login+password
     *  - token - token with ttl
     *  - cookie - cookie token with ttl
     */
    const PROVIDER_COOKIE = 'cookie';
    const PROVIDER_EQUALS = 'equals';
    const PROVIDER_FACEBOOK = 'facebook';
    const PROVIDER_LDAP = 'ldap';
    const PROVIDER_TOKEN = 'token';
    const PROVIDER_TWITTER = 'twitter';

    /**
     * Table
     *
     * @var string
     */
    protected $table = 'auth';

    /**
     * Primary key(s)
     * @var array
     */
    protected $primary = array('provider', 'foreignKey');

    /**
     * getAuthRow
     *
     * @param string $provider
     * @param string $foreignKey
     * @return AbstractRow
     */
    public function getAuthRow($provider, $foreignKey)
    {
        return static::findRow(['provider' => $provider, 'foreignKey' => $foreignKey]);
    }

    /**
     * Generate Secret token
     *
     * @param int $id
     * @return string
     */
    protected function generateSecret($id)
    {
        // generate secret
        $alpha = range('a', 'z');
        shuffle($alpha);
        $secret = array_slice($alpha, 0, rand(5, 15));
        return md5($id . join('', $secret));
    }
}
