<?php
/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Mailer;

use Bluz\Common\Exception\ComponentException;
use Bluz\Common\Exception\ConfigurationException;
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
    public function testWrongConfigurationThrowException()
    {
        $this->expectException(ConfigurationException::class);
        $mailer = new Mailer();
        $mailer->setOptions(null);
        $mailer->init();
    }

    public function testWithoutPHPMailerThrowException()
    {
        $this->expectException(ComponentException::class);
        $mailer = new Mailer();
        $mailer->create();
    }
}
