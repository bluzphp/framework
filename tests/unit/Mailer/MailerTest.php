<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Mailer;

use Bluz\Mailer\Mailer;
use Bluz\Tests\FrameworkTestCase;

/**
 * MailerTest
 *
 * @package  Bluz\Tests\Mailer
 *
 * @author   Anton Shevcuk
 * @created  19.08.2014 13:03
 */
class MailerTest extends FrameworkTestCase
{
    /**
     * @expectedException \Bluz\Common\Exception\ConfigurationException
     */
    public function testWrongConfigurationThrowException()
    {
        $mailer = new Mailer();
        $mailer->setOptions(null);
        $mailer->init();
    }

    /**
     * @expectedException \Bluz\Common\Exception\ComponentException
     */
    public function testWithoutPHPMailerThrowException()
    {
        $mailer = new Mailer();
        $mailer->create();
    }
}
