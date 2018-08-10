<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

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
 * @see      Instance::create()
 *
 * @method   static bool send(\PHPMailer $mail)
 * @see      Instance::send()
 */
final class Mailer
{
    use ProxyTrait;

    /**
     * Init instance
     *
     * @return Instance
     * @throws \Bluz\Common\Exception\ConfigurationException
     * @throws \Bluz\Config\ConfigException
     */
    private static function initInstance() : Instance
    {
        $instance = new Instance();
        $instance->setOptions(Config::getData('mailer'));
        $instance->init();
        return $instance;
    }
}
