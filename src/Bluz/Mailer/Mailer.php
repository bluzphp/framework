<?php
/**
 * Copyright (c) 2012 by Bluz PHP Team
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * @namespace
 */
namespace Bluz\Mailer;

/**
 * Wrapper over PHPMailer
 *
 * @category Bluz
 * @package  Mailer
 *
 * @author   Pavel Machekhin
 * @created  27.12.12 16:25
 */
use Bluz\Config\ConfigException;

class Mailer
{
    use \Bluz\Package;

    /**
     * checkOptions
     *
     * @throws \Bluz\Config\ConfigException
     * @return boolean
     */
    protected function checkOptions()
    {
        if (!isset($this->options['from']['email'])) {
            throw new ConfigException(
                "Missed `from.email` option in `mailer` configuration. <br/>\n".
                "Read more: <a href='https://github.com/bluzphp/framework/wiki/Mailer'>https://github.com/bluzphp/framework/wiki/Mailer</a>"
            );
        }
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

        $fromEmail = $this->options['from']['email'];
        $fromName = isset($this->options['from']['name']) ? $this->options['from']['name'] : '';

        $mail->SetFrom($fromEmail, $fromName, false);

        $this->setupSmtp($mail);

        return $mail;
    }

    /**
     * Enable smtp with parameters from config if they were set
     *
     * @param \PHPMailer $mail
     * @return bool
     */
    private function setupSmtp(\PHPMailer $mail)
    {
        if (!isset($this->options['smtp'])) {
            return false;
        }

        $mail->IsSMTP(); // telling the class to use SMTP
        $mail->SMTPSecure = "ssl";                 // sets the prefix to the server
        $mail->SMTPDebug = 2; // enables SMTP debug information (for testing)
        // 1 = errors and messages
        // 2 = messages only

        $mail->Host = $this->options['smtp']['host']; // set the SMTP server
        $mail->Port = $this->options['smtp']['port']; // set the SMTP port

        if (!isset($this->options['smtp']['username'])
            || !isset($this->options['smtp']['password'])
        ) {
            return true;
        }

        $mail->SMTPAuth = true; // enable SMTP authentication
        $mail->Username = $this->options['smtp']['username']; // SMTP account username
        $mail->Password = $this->options['smtp']['password']; // SMTP account password

        return true;
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

        // TODO: add to queue
        if (!$mail->Send()) {
            throw new MailerException('Error mail send');
        }

        return true;
    }
}
