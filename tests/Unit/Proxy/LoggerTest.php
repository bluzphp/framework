<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/skeleton
 */

namespace Bluz\Tests\Unit\Proxy;

use Bluz\Logger\Logger as Target;
use Bluz\Proxy\Logger as Proxy;
use Bluz\Tests\Unit\Unit;

/**
 * Proxy Test
 *
 * @package  Bluz\Tests\Proxy
 * @author   Anton Shevchuk
 */
class LoggerTest extends Unit
{
    public function testGetProxyInstance()
    {
        self::assertInstanceOf(Target::class, Proxy::getInstance());
    }

    public function testExeptionsLogger()
    {
        $line = __LINE__ + 2;
        try {
            throw new \Exception('Message');
        } catch (\Exception $e) {
            Proxy::exception($e);
        }

        $errors = Proxy::get('error');
        $error = current($errors);

        self::assertCount(1, $errors);
        self::assertEquals('Message [' . __FILE__ . ':' . $line . ']', $error);
    }
}
