<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/skeleton
 */

namespace Bluz\Tests\Crud;

use Bluz\Http\Exception\NotImplementedException;
use Bluz\Tests\Fixtures\Crud\EmptyCrud;
use Bluz\Tests\FrameworkTestCase;

/**
 * CrudTest
 *
 * @package  Bluz\Tests\Crud
 *
 * @author   Anton Shevchuk
 * @created  22.08.2014 16:13
 */
class CrudTest extends FrameworkTestCase
{
    /**
     * @covers \Bluz\Crud\AbstractCrud::readOne()
     */
    public function testNotImplementedReadOneMethodThrowException()
    {
        $this->expectException(NotImplementedException::class);
        EmptyCrud::getInstance()->readOne('any');
    }

    /**
     * @covers \Bluz\Crud\AbstractCrud::readSet()
     */
    public function testNotImplementedReadSetMethodThrowException()
    {
        $this->expectException(NotImplementedException::class);
        EmptyCrud::getInstance()->readSet();
    }

    /**
     * @covers \Bluz\Crud\AbstractCrud::createOne()
     */
    public function testNotImplementedCreateOneMethodThrowException()
    {
        $this->expectException(NotImplementedException::class);
        EmptyCrud::getInstance()->createOne([]);
    }

    /**
     * @covers \Bluz\Crud\AbstractCrud::createSet()
     */
    public function testNotImplementedCreateSetMethodThrowException()
    {
        $this->expectException(NotImplementedException::class);
        EmptyCrud::getInstance()->createSet([]);
    }

    /**
     * @covers \Bluz\Crud\AbstractCrud::updateOne()
     */
    public function testNotImplementedUpdateOneMethodThrowException()
    {
        $this->expectException(NotImplementedException::class);
        EmptyCrud::getInstance()->updateOne('any', []);
    }

    /**
     * @covers \Bluz\Crud\AbstractCrud::updateSet()
     */
    public function testNotImplementedUpdateSetMethodThrowException()
    {
        $this->expectException(NotImplementedException::class);
        EmptyCrud::getInstance()->updateSet([]);
    }

    /**
     * @covers \Bluz\Crud\AbstractCrud::deleteOne()
     */
    public function testNotImplementedDeleteOneMethodThrowException()
    {
        $this->expectException(NotImplementedException::class);
        EmptyCrud::getInstance()->deleteOne('any');
    }

    /**
     * @covers \Bluz\Crud\AbstractCrud::deleteSet()
     */
    public function testNotImplementedDeleteSetMethodThrowException()
    {
        $this->expectException(NotImplementedException::class);
        EmptyCrud::getInstance()->deleteSet([]);
    }
}
