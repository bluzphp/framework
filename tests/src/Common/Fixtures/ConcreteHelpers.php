<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Common\Fixtures;

use Bluz\Common\Helper;

/**
 * Concrete class with Helpers trait
 *
 * @category Tests
 * @package  Bluz\Tests\Common
 *
 * @method integer HelperFunction($argument)
 * @method integer Helper2Function($argument)
 * @method bool helperClass()
 *
 * @author   Anton Shevchuk
 * @created  14.01.14 11:48
 */
class ConcreteHelpers
{
    use Helper;

    /**
     * Constructor of ConcreteHelpers
     *
     * @access  public
     */
    public function __construct()
    {
    }
}
