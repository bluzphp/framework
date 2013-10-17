<?php
/**
 * Copyright (c) 2013 by Bluz PHP Team
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

use Bluz\Common\Package;
use Bluz\Config\ConfigException;

/**
 * Wrapper over PHPMailer
 *
 * @category Bluz
 * @package  Mailer
 *
 * @author   Pavel Machekhin
 * @created  27.12.12 16:25
 */
class Mailer
{
    use Package;

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
