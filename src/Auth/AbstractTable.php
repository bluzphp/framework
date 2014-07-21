<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */
namespace Bluz\Auth;

use Bluz\Db\Row;
use Bluz\Db\Table;

/**
 * Abstract class for Auth\Table
 *
 * @package  Bluz\Auth
 *
 * @method   static Row findRow($primaryKey)
 * @method   static Row findRowWhere($whereList)
 *
 * @author   Anton Shevchuk
 * @created  12.07.11 15:28
 */
abstract class AbstractTable extends Table
{
    /**
     * Types
     */
    const TYPE_REQUEST = 'request';
    const TYPE_ACCESS = 'access';

    /**
     * Providers
     *  - equals - login+password
     *  - token - login+token
     */
    const PROVIDER_EQUALS = 'equals';
    const PROVIDER_TOKEN = 'token';
    const PROVIDER_LDAP = 'ldap';
    const PROVIDER_TWITTER = 'twitter';
    const PROVIDER_FACEBOOK = 'facebook';

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
     * @return Row
     */
    public function getAuthRow($provider, $foreignKey)
    {
        return static::findRow(['provider' => $provider, 'foreignKey' => $foreignKey]);
    }
}
