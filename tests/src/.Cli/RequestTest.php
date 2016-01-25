<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Cli;

use Bluz\Cli\Request;
use Bluz\Tests\TestCase;

/**
 * RequestTest
 *
 * @package  Bluz\Tests\Cli
 *
 * @author   Anton Shevchuk
 * @created  22.08.2014 12:55
 */
class RequestTest extends TestCase
{
    /**
     * @expectedException \Bluz\Request\RequestException
     */
    public function testInitialRequestWithoutUriArgumentThrowException()
    {
        new Request();
    }
}
