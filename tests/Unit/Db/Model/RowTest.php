<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Unit\Db\Model;

use Bluz;
use Bluz\Common\Exception\CommonException;
use Bluz\Db\Table;
use Bluz\Tests\Fixtures\Db;
use Bluz\Tests\Unit\Unit;
use Symfony\Component\Validator\Validation;

/**
 * Test class for Row.
 * Generated by PHPUnit on 2011-07-27 at 13:52:01.
 */
class RowTest extends Unit
{
    /**
     * @var Db\Model\Row
     */
    protected Db\Model\Row $row;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     * @throws CommonException
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->row = new Db\Model\Row();
    }

    /**
     * @covers \Bluz\Db\Row::__get
     */
    public function testGetNotPresentProperty()
    {
        self::assertNull($this->row->someValue);
    }

    /**
     * @covers \Bluz\Db\Row::__set
     */
    public function testSet()
    {
        $this->row->name = 'foo';
        $this->row->email = 'foo';

        codecept_debug($this->row);

        $builder = Validation::createValidatorBuilder();
        $builder->enableAnnotationMapping();
        $validator = $builder->getValidator();
        $errors = $validator->validate($this->row);
        codecept_debug($errors->count());

        //self::assertEquals('foo', $this->row->name);
    }
}