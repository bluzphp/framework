<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * @namespace
 */
namespace Bluz\Proxy;

use Bluz\Mailer\Mailer as Instance;

/**
 * Proxy to Mailer
 *
 * Example of usage
 * <code>
 *     use Bluz\Proxy\Mailer;
 *
 *     $mail = Mailer::create();
 *     $mail->From = 'from@example.com';
 *     $mail->Subject = 'Here is the subject';
 *     // ...
 *     Mailer::send($mail);
 * </code>
 *
 * @package  Bluz\Proxy
 * @author   Anton Shevchuk
 *
 * @method   static Instance getInstance()
 *
 * @method   static \PHPMailer create()
 * @see      Bluz\Mailer\Mailer::create()
 *
 * @method   static bool send(\PHPMailer $mail)
 * @see      Bluz\Mailer\Mailer::send()
 */
class Mailer extends AbstractProxy
{
    /**
     * Init instance
     *
     * @return Instance
     */
    protected static function initInstance()
    {
        $instance = new Instance();
        $instance->setOptions(Config::getData('mailer'));
        return $instance;
    }
}
