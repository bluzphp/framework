<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */

namespace Bluz\Tests\Crud;

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
     * @expectedException \Bluz\Application\Exception\NotImplementedException
     */
    public function testNotImplementedReadOneMethodThrowException()
    {
        EmptyCrud::getInstance()->readOne('any');
    }

    /**
     * @covers \Bluz\Crud\AbstractCrud::readSet()
     * @expectedException \Bluz\Application\Exception\NotImplementedException
     */
    public function testNotImplementedReadSetMethodThrowException()
    {
        EmptyCrud::getInstance()->readSet();
    }

    /**
     * @covers \Bluz\Crud\AbstractCrud::createOne()
     * @expectedException \Bluz\Application\Exception\NotImplementedException
     */
    public function testNotImplementedCreateOneMethodThrowException()
    {
        EmptyCrud::getInstance()->createOne([]);
    }

    /**
     * @covers \Bluz\Crud\AbstractCrud::createSet()
     * @expectedException \Bluz\Application\Exception\NotImplementedException
     */
    public function testNotImplementedCreateSetMethodThrowException()
    {
        EmptyCrud::getInstance()->createSet([]);
    }

    /**
     * @covers \Bluz\Crud\AbstractCrud::updateOne()
     * @expectedException \Bluz\Application\Exception\NotImplementedException
     */
    public function testNotImplementedUpdateOneMethodThrowException()
    {
        EmptyCrud::getInstance()->updateOne('any', []);
    }

    /**
     * @covers \Bluz\Crud\AbstractCrud::updateSet()
     * @expectedException \Bluz\Application\Exception\NotImplementedException
     */
    public function testNotImplementedUpdateSetMethodThrowException()
    {
        EmptyCrud::getInstance()->updateSet([]);
    }

    /**
     * @covers \Bluz\Crud\AbstractCrud::deleteOne()
     * @expectedException \Bluz\Application\Exception\NotImplementedException
     */
    public function testNotImplementedDeleteOneMethodThrowException()
    {
        EmptyCrud::getInstance()->deleteOne('any');
    }

    /**
     * @covers \Bluz\Crud\AbstractCrud::deleteSet()
     * @expectedException \Bluz\Application\Exception\NotImplementedException
     */
    public function testNotImplementedDeleteSetMethodThrowException()
    {
        EmptyCrud::getInstance()->deleteSet([]);
    }
}
