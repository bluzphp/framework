<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Crud;

use Bluz\Tests\Crud\Fixtures\EmptyCrud;
use Bluz\Tests\TestCase;

/**
 * CrudTest
 *
 * @package  Bluz\Tests\Crud
 *
 * @author   Anton Shevchuk
 * @created  22.08.2014 16:13
 */
class CrudTest extends TestCase
{
    /**
     * @covers \Bluz\Crud\AbstractCrud::readOne()
     * @expectedException \Bluz\Application\Exception\NotImplementedException
     */
    public function testNotImplementedReadOneMethodThrowException()
    {
        $crud = new EmptyCrud();
        $crud->readOne('any');
    }

    /**
     * @covers \Bluz\Crud\AbstractCrud::readSet()
     * @expectedException \Bluz\Application\Exception\NotImplementedException
     */
    public function testNotImplementedReadSetMethodThrowException()
    {
        $crud = new EmptyCrud();
        $crud->readSet();
    }

    /**
     * @covers \Bluz\Crud\AbstractCrud::createOne()
     * @expectedException \Bluz\Application\Exception\NotImplementedException
     */
    public function testNotImplementedCreateOneMethodThrowException()
    {
        $crud = new EmptyCrud();
        $crud->createOne([]);
    }

    /**
     * @covers \Bluz\Crud\AbstractCrud::createSet()
     * @expectedException \Bluz\Application\Exception\NotImplementedException
     */
    public function testNotImplementedCreateSetMethodThrowException()
    {
        $crud = new EmptyCrud();
        $crud->createSet([]);
    }

    /**
     * @covers \Bluz\Crud\AbstractCrud::updateOne()
     * @expectedException \Bluz\Application\Exception\NotImplementedException
     */
    public function testNotImplementedUpdateOneMethodThrowException()
    {
        $crud = new EmptyCrud();
        $crud->updateOne('any', []);
    }

    /**
     * @covers \Bluz\Crud\AbstractCrud::updateSet()
     * @expectedException \Bluz\Application\Exception\NotImplementedException
     */
    public function testNotImplementedUpdateSetMethodThrowException()
    {
        $crud = new EmptyCrud();
        $crud->updateSet([]);
    }

    /**
     * @covers \Bluz\Crud\AbstractCrud::deleteOne()
     * @expectedException \Bluz\Application\Exception\NotImplementedException
     */
    public function testNotImplementedDeleteOneMethodThrowException()
    {
        $crud = new EmptyCrud();
        $crud->deleteOne('any');
    }

    /**
     * @covers \Bluz\Crud\AbstractCrud::deleteSet()
     * @expectedException \Bluz\Application\Exception\NotImplementedException
     */
    public function testNotImplementedDeleteSetMethodThrowException()
    {
        $crud = new EmptyCrud();
        $crud->deleteSet([]);
    }
}
