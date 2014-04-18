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

use Bluz\Common\Options;
use Bluz\Config\ConfigException;

/**
 * Wrapper over PHPMailer
 *
 * @package  Bluz\Mailer
 *
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
     * @throws ConfigException
     * @return boolean
     */
    protected function checkOptions()
    {
        if (!isset($this->options['from']['email'])) {
            throw new ConfigException(
                "Missed `from.email` option in `mailer` configuration. <br/>\n" .
                "Read more: <a href='https://github.com/bluzphp/framework/wiki/Mailer'>".
                "https://github.com/bluzphp/framework/wiki/Mailer</a>"
            );
        }
        return true;
    }

    /**
     * Creates new instance of PHPMailer and set default options from config
     *
     * @throws MailerException
     * @return \PHPMailer
     */
    public function create()
    {
        $mail = new \PHPMailer();
        $mail->WordWrap = 920; // RFC 2822 Compliant for Max 998 characters per line

        $fromEmail = $this->options['from']['email'];
        $fromName = isset($this->options['from']['name']) ? $this->options['from']['name'] : '';

        // setup options from config
        $mail->SetFrom($fromEmail, $fromName, false);

        // setup options
        if (isset($this->options['settings'])) {
            foreach ($this->options['settings'] as $name => $value) {
                $mail->set($name, $value);
            }
        }

        // setup custom headers
        if (isset($this->options['headers'])) {
            foreach ($this->options['headers'] as $header => $value) {
                $mail->AddCustomHeader($header, $value);
            }
        }

        return $mail;
    }

    /**
     * Send email
     *
     * @todo Add mail to queue
     *
     * @param \PHPMailer $mail
     * @return bool
     * @throws MailerException
     */
    public function send(\PHPMailer $mail)
    {
        if (isset($this->options['subjectTemplate'])) {
            $mail->Subject = sprintf($this->options['subjectTemplate'], $mail->Subject);
        }

        if (!$mail->Send()) {
            // Why you don't use "Exception mode" of PHPMailer
            // Because we need our Exception in any case
            throw new MailerException('Error mail send: '. $mail->ErrorInfo);
        }

        return true;
    }
}
