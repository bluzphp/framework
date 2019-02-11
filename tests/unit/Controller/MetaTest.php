<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Controller;

use Bluz\Controller\Meta;
use Bluz\Tests\FrameworkTestCase;

/**
 * ApplicationTest
 *
 * @author   Anton Shevchuk
 * @created  21.05.13 10:24
 */
class MetaTest extends FrameworkTestCase
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
        $controllerFile = __DIR__ . '/Fixtures/ConcreteWithData.php';

        $meta = new Meta($controllerFile);
        $meta->process();

        self::assertEqualsArray(['CLI', 'GET'], $meta->getMethod());
        self::assertEquals(300, $meta->getCache());
        self::assertEqualsArray(['a' => 'int', 'b' => 'float', 'c' => 'string'], $meta->getParams());
        self::assertEquals('Test', $meta->getPrivilege());
        self::assertEqualsArray(['Read', 'Write'], $meta->getAcl());
        self::assertArrayHasSize($meta->getRoute(), 2);
    }

    /**
     * Test all reflection options and export it
     */
    public function testExportReflectionWithData()
    {
        $controllerFile = __DIR__ . '/Fixtures/ConcreteWithData.php';

        $meta = new Meta($controllerFile);
        $meta->process();

        $data = var_export($meta, true);

        self::assertStringStartsWith('Bluz\Controller\Meta::__set_state', $data);
    }

    /**
     * Test reflection:
     *  - get params without description
     */
    public function testReflectionWithoutData()
    {
        $controllerFile = __DIR__ . '/Fixtures/ConcreteWithoutData.php';

        $meta = new Meta($controllerFile);
        $meta->process();

        self::assertEqualsArray(['a' => null, 'b' => null, 'c' => null], $meta->getParams());
    }

    /**
     * Test reflection without return structure
     *
     * @expectedException \Bluz\Common\Exception\ComponentException
     */
    public function testReflectionWithoutReturn()
    {
        $controllerFile = __DIR__ . '/Fixtures/ConcreteWithoutReturn.php';

        $meta = new Meta($controllerFile);
        $meta->process();
    }
}
