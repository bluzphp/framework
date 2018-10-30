<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Mailer;

use Bluz\Common\Exception\ComponentException;
use Bluz\Common\Exception\ConfigurationException;
use Bluz\Common\Options;
use Bluz\Proxy\Translator;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Wrapper over PHPMailer
 *
 * @package  Bluz\Mailer
 * @author   Pavel Machekhin
 * @link     https://github.com/bluzphp/framework/wiki/Mailer
 */
class Mailer
{
    use Options;

    /**
     * Check Mailer configuration
     *
     * @throws ConfigurationException
     * @return void
     */
    public function init(): void
    {
        if (!$this->getOption('from', 'email')) {
            throw new ConfigurationException(
                "Missed `from.email` option in `mailer` configuration. <br/>\n" .
                "Read more: <a href='https://github.com/bluzphp/framework/wiki/Mailer'>" .
                "https://github.com/bluzphp/framework/wiki/Mailer</a>"
            );
        }
    }

    /**
     * Creates new instance of PHPMailer and set default options from config
     *
     * @return PHPMailer
     * @throws ComponentException
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function create(): PHPMailer
    {
        // can initial, can't use
        if (!class_exists(PHPMailer::class)) {
            throw new ComponentException(
                "PHPMailer library is required for `Bluz\\Mailer` package. <br/>\n" .
                "Read more: <a href='https://github.com/bluzphp/framework/wiki/Mailer'>" .
                "https://github.com/bluzphp/framework/wiki/Mailer</a>"
            );
        }

        $mail = new PHPMailer();
        $mail->WordWrap = 920; // RFC 2822 Compliant for Max 998 characters per line

        $fromEmail = $this->getOption('from', 'email');
        $fromName = $this->getOption('from', 'name') ?: '';

        // setup options from config
        $mail->setFrom($fromEmail, $fromName, false);

        // setup options
        if ($settings = $this->getOption('settings')) {
            foreach ($settings as $name => $value) {
                $mail->set($name, $value);
            }
        }

        // setup custom headers
        if ($headers = $this->getOption('headers')) {
            foreach ($headers as $header => $value) {
                $mail->addCustomHeader($header, $value);
            }
        }

        return $mail;
    }

    /**
     * Send email
     *
     * @todo Add mail to queue
     *
     * @param  PHPMailer $mail
     *
     * @return bool
     * @throws MailerException
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public function send(PHPMailer $mail)
    {
        if ($template = $this->getOption('subjectTemplate')) {
            /** @var string $template */
            $mail->Subject = Translator::translate($template, $mail->Subject);
        }

        if (!$mail->send()) {
            // Why you don't use "Exception mode" of PHPMailer
            // Because we need our Exception in any case
            throw new MailerException('Error mail send: ' . $mail->ErrorInfo);
        }

        return true;
    }
}
