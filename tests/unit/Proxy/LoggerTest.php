<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */

namespace Bluz\Tests\Proxy;

use Bluz\Logger\Logger as Target;
use Bluz\Proxy\Logger as Proxy;
use Bluz\Tests\FrameworkTestCase;

/**
 * Proxy Test
 *
 * @package  Bluz\Tests\Proxy
 * @author   Anton Shevchuk
 */
class LoggerTest extends FrameworkTestCase
{
    public function testGetProxyInstance()
    {
        self::assertInstanceOf(Target::class, Proxy::getInstance());
    }

    public function testExeptionsLogger()
    {
        try {
            throw new \Exception('Message');
        } catch (\Exception $e) {
            Proxy::exception($e);
        }

        $errors = Proxy::get('error');
        $error = current($errors);

        self::assertArrayHasSize($errors, 1);
        self::assertEquals('Message ['. __FILE__ .':33]', $error);
    }
}
