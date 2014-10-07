<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Controller;

use Bluz\Controller\Reflection;
use Bluz\Tests\TestCase;

/**
 * ApplicationTest
 *
 * @author   Anton Shevchuk
 * @created  21.05.13 10:24
 */
class ReflectionTest extends TestCase
{
    /**
     * @covers \Bluz\Application\Application::reflection
     */
    public function testReflection()
    {
        $controllerFile = dirname(__FILE__) .'/../Fixtures/Controllers/ConcreteWithData.php';

        $reflection = new Reflection($controllerFile);
        $reflection->process();

        $this->assertEqualsArray(['CLI', 'GET'], $reflection->getMethod());
        $this->assertEquals(60, $reflection->getCacheHtml());
        $this->assertEquals(300, $reflection->getCache());
        $this->assertEqualsArray(['a' => 'int', 'b' => 'float', 'c' => 'string'], $reflection->getParams());
        $this->assertEquals('Test', $reflection->getPrivilege());
        $this->assertArrayHasSize($reflection->getRoute(), 2);
    }
}
