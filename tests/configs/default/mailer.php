<?php
/**
 * Mailer configuration for PHPMailer
 *
 * @link https://github.com/bluzphp/framework/wiki/Mailer
 * @link https://github.com/Synchro/PHPMailer
 * @return array
 */
return [
    'subjectTemplate' => 'Bluz - %s',
    'from' => [
        'email' => 'no-reply@example.com',
        'name' => 'Bluz'
    ],
    'settings' => [
        'CharSet' => 'UTF-8'
    ],
];
