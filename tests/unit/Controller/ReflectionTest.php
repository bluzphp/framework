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
     * Test all reflection options
     *  - methods
     *  - cache
     *  - cache to html
     *  - params with type cast
     *  - privilege
     *  - routes
     */
    public function testReflectionWithData()
    {
        $controllerFile = __DIR__ .'/Fixtures/ConcreteWithData.php';

        $reflection = new Reflection($controllerFile);
        $reflection->process();

        self::assertEqualsArray(['CLI', 'GET'], $reflection->getMethod());
        self::assertEquals(300, $reflection->getCache());
        self::assertEqualsArray(['a' => 'int', 'b' => 'float', 'c' => 'string'], $reflection->getParams());
        self::assertEquals('Test', $reflection->getPrivilege());
        self::assertEqualsArray(['Read', 'Write'], $reflection->getAcl());
        self::assertArrayHasSize($reflection->getRoute(), 2);
    }

    /**
     * Test all reflection options and export it
     */
    public function testExportReflectionWithData()
    {
        $controllerFile = __DIR__ .'/Fixtures/ConcreteWithData.php';

        $reflection = new Reflection($controllerFile);
        $reflection->process();

        $data = var_export($reflection, true);

        self::assertStringStartsWith('Bluz\Controller\Reflection::__set_state', $data);
    }

    /**
     * Test reflection:
     *  - get params without description
     */
    public function testReflectionWithoutData()
    {
        $controllerFile = __DIR__ .'/Fixtures/ConcreteWithoutData.php';

        $reflection = new Reflection($controllerFile);
        $reflection->process();

        self::assertEqualsArray(['a' => null, 'b' => null, 'c' => null], $reflection->getParams());
    }

    /**
     * Test reflection without return structure
     * @expectedException \Bluz\Common\Exception\ComponentException
     */
    public function testReflectionWithoutReturn()
    {
        $controllerFile = __DIR__ .'/Fixtures/ConcreteWithoutReturn.php';

        $reflection = new Reflection($controllerFile);
        $reflection->process();
    }
}