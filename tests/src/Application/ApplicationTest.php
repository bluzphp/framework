<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Application;

use Bluz\Tests\TestCase;
use Bluz\Application\Application;

/**
 * ApplicationTest
 *
 * @author   Anton Shevchuk
 * @created  21.05.13 10:24
 */
class ApplicationTest extends TestCase
{
    /**
     * @covers \Bluz\Application\Application::reflection
     * @return void
     */
    public function testReflection()
    {
        $controllerFile = dirname(__FILE__) .'/../Fixtures/ConcreteControllerWithData.php';
        $app = Application::getInstance();
        $reflectionData = $app->reflection($controllerFile);

        /** @var \closure $controllerClosure */
        $controllerClosure = require $controllerFile;

        $this->assertEquals($reflectionData, $controllerClosure('a', 'b', 'c'));
    }
}
