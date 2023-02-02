<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

namespace Bluz\Tests\Unit\Mailer;

use Bluz\Common\Exception\ComponentException;
use Bluz\Common\Exception\ConfigurationException;
use Bluz\Mailer\Mailer;
use Bluz\Tests\Unit\Unit;

/**
 * MailerTest
 *
 * @package  Bluz\Tests\Mailer
 *
 * @author   Anton Shevcuk
 * @created  19.08.2014 13:03
 */
class MailerTest extends Unit
{
    public function testWrongConfigurationThrowException()
    {
        $this->expectException(ConfigurationException::class);
        $mailer = new Mailer();
        $mailer->setOptions(null);
        $mailer->init();
    }
}
