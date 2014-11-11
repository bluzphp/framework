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
namespace Bluz\Mailer;

use Bluz\Common\Exception\ComponentException;
use Bluz\Common\Exception\ConfigurationException;
use Bluz\Common\Options;

/**
 * Wrapper over PHPMailer
 *
 * @package  Bluz\Mailer
 * @link     https://github.com/bluzphp/framework/wiki/Mailer
 *
 * @author   Pavel Machekhin
 * @created  27.12.12 16:25
 */
class Mailer
{
    use Options;

    /**
     * checkOptions
     *
     * @throws ConfigurationException
     * @return bool
     */
    protected function checkOptions()
    {
        if (!$this->getOption('from', 'email')) {
            throw new ConfigurationException(
                "Missed `from.email` option in `mailer` configuration. <br/>\n" .
                "Read more: <a href='https://github.com/bluzphp/framework/wiki/Mailer'>".
                "https://github.com/bluzphp/framework/wiki/Mailer</a>"
            );
        }
        return true;
    }

    /**
     * Creates new instance of PHPMailer and set default options from config
     * @api
     * @throws ComponentException
     * @throws \phpmailerException
     * @return \PHPMailer
     */
    public function create()
    {
        // can initial, can't use
        if (!class_exists('\PHPMailer')) {
            throw new ComponentException(
                "PHPMailer library is required for `Bluz\\Mailer` package. <br/>\n" .
                "Read more: <a href='https://github.com/bluzphp/framework/wiki/Mailer'>".
                "https://github.com/bluzphp/framework/wiki/Mailer</a>"
            );
        }

        $mail = new \PHPMailer();
        $mail->WordWrap = 920; // RFC 2822 Compliant for Max 998 characters per line

        $fromEmail = $this->getOption('from', 'email');
        $fromName = $this->getOption('from', 'name') ?: '';

        // setup options from config
        $mail->SetFrom($fromEmail, $fromName, false);

        // setup options
        if ($settings = $this->getOption('settings')) {
            foreach ($settings as $name => $value) {
                $mail->set($name, $value);
            }
        }

        // setup custom headers
        if ($headers = $this->getOption('headers')) {
            foreach ($headers as $header => $value) {
                $mail->AddCustomHeader($header, $value);
            }
        }

        return $mail;
    }

    /**
     * Send email
     * @api
     * @param \PHPMailer $mail
     * @return bool
     * @throws MailerException
     * @todo Add mail to queue
     */
    public function send(\PHPMailer $mail)
    {
        if ($template = $this->getOption('subjectTemplate')) {
            $mail->Subject = sprintf($template, $mail->Subject);
        }

        if (!$mail->Send()) {
            // Why you don't use "Exception mode" of PHPMailer
            // Because we need our Exception in any case
            throw new MailerException('Error mail send: '. $mail->ErrorInfo);
        }

        return true;
    }
}
