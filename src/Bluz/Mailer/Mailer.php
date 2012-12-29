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
class Mailer
{
    use \Bluz\Package;

    /**
     * @var array
     */
    protected $config;

    /**
     * getInstance
     *
     * @return static
     */
    static public function getInstance()
    {
        static $instance;

        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    /**
     * Creates new instance of PHPMailer and set default options from config
     *
     * @return \PHPMailer
     * @throws Exception
     */
    public function create()
    {
        $this->config = $this->getApplication()->getConfigData('mail');

        $mail = new \PHPMailer();

        if (!isset($this->config['from']['email'])) {
            throw new MailerException('Missed `from.email` option in `mail` config');
        }

        $fromEmail = $this->config['from']['email'];
        $fromName = isset($this->config['from']['name']) ? $this->config['from']['name'] : '';

        $mail->SetFrom($fromEmail, $fromName, false);

        $this->_useSmtp($mail);

        return $mail;
    }

    /**
     * Enable smtp with parameters from config if they were set
     *
     * @return bool
     */
    private function _useSmtp(\PHPMailer $mail)
    {
        if (!isset($this->config['smtp'])) {
            return false;
        }

        $mail->IsSMTP(); // telling the class to use SMTP
        $mail->SMTPSecure = "ssl";                 // sets the prefix to the server
        $mail->SMTPDebug = 2; // enables SMTP debug information (for testing)
        // 1 = errors and messages
        // 2 = messages only

        $mail->Host = $this->config['smtp']['host']; // sets the SMTP server
        $mail->Port = $this->config['smtp']['port']; // set the SMTP port for the GMAIL server

        if (!isset($this->config['smtp']['username'])
            || !isset($this->config['smtp']['password'])
        ) {
            return true;
        }

        $mail->SMTPAuth = true; // enable SMTP authentication
        $mail->Username = $this->config['smtp']['username']; // SMTP account username
        $mail->Password = $this->config['smtp']['password']; // SMTP account password

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
        if (isset($this->config['subjectPrefix'])) {
            $mail->Subject = $this->config['subjectPrefix'] . $mail->Subject;
        }

        // TODO: add to queue
        if (!$mail->Send()) {
            throw new MailerException('Error mail send');
        }

        return true;
    }
}
