<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\View\Helper;

use Bluz\Application\Application;
use Bluz\Proxy\Layout;
use Bluz\View\View;

/**
 * Get either a Gravatar URL or complete image tag for a specified email address.
 *
 * @param string $email The email address
 * @param int $size Size in pixels, defaults to 80px [ 1 - 2048 ]
 * @param string $default Default set of images to use [ 404 | mm | identicon | monsterid | wavatar ]
 * @param string $rate Maximum rating (inclusive) [ g | pg | r | x ]
 *
 * @return String containing either just a URL or a complete image tag
 * @source https://gravatar.com/site/implement/images/php/
 */
return
    function (string $email, int $size = 80, string $default = 'mm', string $rate = 'g') {
        $email = md5(strtolower(trim($email ?? '')));
        return "https://www.gravatar.com/avatar/$email?s=$size&d=$default&r=$rate";
    };
