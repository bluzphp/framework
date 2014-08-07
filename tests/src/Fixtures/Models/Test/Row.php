<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/skeleton
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Fixtures\Models\Test;

use Bluz\Validator\Traits\Validator;
use Bluz\Validator\Validator as v;

/**
 * Test Row
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $status enum('active','disable','delete')
 *
 * @package  Bluz\Tests\Fixtures
 */
class Row extends \Bluz\Db\Row
{
    use Validator;

    /**
     * Return table instance for manipulation
     *
     * @return Table
     */
    public function getTable()
    {
        return Table::getInstance();
    }

    /**
     * beforeInsert
     *
     * @return void
     */
    public function beforeSave()
    {
        $this->addValidator(
            'name',
            v::required()->notEmpty()->latin()
        );

        $this->addValidator(
            'email',
            v::required()->notEmpty()->email()
        );
    }
}
