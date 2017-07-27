<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Auth;

use Bluz\Db\Table;

/**
 * Abstract class for Auth\Table
 *
 * @package  Bluz\Auth
 * @author   Anton Shevchuk
 *
 * @method   static AbstractRow findRow($primaryKey)
 * @see      Table::findRow()
 *
 * @method   static AbstractRow findRowWhere($whereList)
 * @see      Table::findRowWhere()
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
    const PROVIDER_GOOGLE = 'google';
    const PROVIDER_LDAP = 'ldap';
    const PROVIDER_TOKEN = 'token';
    const PROVIDER_TWITTER = 'twitter';

    /**
     * @var string Table
     */
    protected $name = 'auth';

    /**
     * @var array Primary key(s)
     */
    protected $primary = ['provider', 'foreignKey'];

    /**
     * Get AuthRow
     *
     * @param  string $provider
     * @param  string $foreignKey
     *
     * @return AbstractRow
     */
    public static function getAuthRow($provider, $foreignKey)
    {
        return static::findRow(['provider' => $provider, 'foreignKey' => $foreignKey]);
    }
}
