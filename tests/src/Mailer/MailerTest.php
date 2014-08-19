<?php
/**
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Tests\Mailer;

use Bluz\Mailer\Mailer;
use Bluz\Tests\TestCase;
/**
 * MailerTest
 *
 * @package  Bluz\Tests\Mailer
 *
 * @author   Anton Shevcuk
 * @created  19.08.2014 13:03
 */
class MailerTest extends TestCase
{
    /**
     * @expectedException \Bluz\Config\ConfigException
     */
    public function testWrongConfigurationThrowException()
    {
        $mailer = new Mailer();
        $mailer->setOptions(array());
    }

    /**
     * @expectedException \Bluz\Mailer\MailerException
     */
    public function testWithoutPHPMailerThrowException()
    {
        $mailer = new Mailer();
        $mailer->create();
    }
}
 