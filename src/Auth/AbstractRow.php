<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Auth;

use Bluz\Db\Row;

/**
 * Abstract class for Auth\Row
 *
 * @package  Bluz\Auth
 *
 * @property integer $userId
 * @property string $provider
 * @property string $foreignKey
 * @property string $token
 * @property string $tokenSecret
 * @property string $tokenType
 * @property string $created
 * @property string $updated
 * @property string $expired
 *
 * @author   Anton Shevchuk
 * @created  24.10.12 11:57
 */
class AbstractRow extends Row
{
}
