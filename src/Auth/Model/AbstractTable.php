<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Auth\Model;

use Bluz\Db\RowInterface;
use Bluz\Db\Table;

/**
 * Abstract class for Auth\Table
 *
 * @package  Bluz\Auth
 * @author   Anton Shevchuk
 */
abstract class AbstractTable extends Table
{
    /**
     * Types
     */
    public const TYPE_ACCESS = 'access';
    public const TYPE_REQUEST = 'request';

    /**
     * Providers
     *  - equals - login + password
     *  - token  - token with ttl
     *  - cookie - cookie token with ttl
     */
    public const PROVIDER_COOKIE = 'cookie';
    public const PROVIDER_EQUALS = 'equals';
    public const PROVIDER_FACEBOOK = 'facebook';
    public const PROVIDER_GOOGLE = 'google';
    public const PROVIDER_LDAP = 'ldap';
    public const PROVIDER_TOKEN = 'token';
    public const PROVIDER_TWITTER = 'twitter';

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
     * @return RowInterface
     * @throws \InvalidArgumentException
     * @throws \Bluz\Db\Exception\DbException
     * @throws \Bluz\Db\Exception\InvalidPrimaryKeyException
     */
    public static function getAuthRow($provider, $foreignKey): ?RowInterface
    {
        return static::findRow(['provider' => $provider, 'foreignKey' => $foreignKey]);
    }
}
