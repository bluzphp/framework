<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Validator\Rule;

use Bluz\Tests;
use Bluz\Validator\Rule\Slug;

/**
 * Class SlugTest
 * @package Bluz\Tests\Validator\Rule
 */
class SlugTest extends Tests\TestCase
{
    /**
     * @var \Bluz\Validator\Rule\Slug
     */
    protected $validator;

    /**
     * Setup validator instance
     */
    protected function setUp()
    {
        parent::setUp();
        $this->validator = new Slug();
    }

    /**
     * @dataProvider providerForPass
     */
    public function testValidSlug($input)
    {
        $this->assertTrue($this->validator->validate($input));
        $this->assertTrue($this->validator->assert($input));
    }

    /**
     * @dataProvider providerForFail
     * @expectedException \Bluz\Validator\Exception\ValidatorException
     */
    public function testInvalidSlug($input)
    {
        $this->assertFalse($this->validator->validate($input));
        $this->assertFalse($this->validator->assert($input));
    }

    public function providerForPass()
    {
        return array(
            array('o-rato-roeu-o-rei-de-roma'),
            array('o-alganet-e-um-feio'),
            array('a-e-i-o-u'),
            array('anticonstitucionalissimamente')
        );
    }

    public function providerForFail()
    {
        return array(
            array(''),
            array('o-alganet-é-um-feio'),
            array('á-é-í-ó-ú'),
            array('-assim-nao-pode'),
            array('assim-tambem-nao-'),
            array('nem--assim'),
            array('--nem-assim'),
            array('Nem mesmo Assim'),
            array('Ou-ate-assim'),
            array('-Se juntar-tudo-Então-')
        );
    }
}
