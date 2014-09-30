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
 * @package  Bluz\Proxy
 *
 * @method   static \PHPMailer create()
 * @method   static bool send(\PHPMailer $mail)
 *
 * @author   Anton Shevchuk
 * @created  29.09.2014 12:08
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
