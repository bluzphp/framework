<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Auth;

use Bluz\Db\Row;

/**
 * Abstract class for Auth\Row
 *
 * @package  Bluz\Auth
 * @author   Anton Shevchuk
 *
 * @property integer $userId
 * @property string  $provider
 * @property string  $foreignKey
 * @property string  $token
 * @property string  $tokenSecret
 * @property string  $tokenType
 * @property string  $created
 * @property string  $updated
 * @property string  $expired
 */
class AbstractRow extends Row
{
}
