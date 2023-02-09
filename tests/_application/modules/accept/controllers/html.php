<?php

/**
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

/**
 * Example of empty test controller
 *
 * @author   Anton Shevchuk
 */

namespace Application;

use Bluz\Controller\Attribute\Accept;
use Bluz\Http\MimeType;

/**
 * @return bool
 */
return
    #[Accept(MimeType::HTML)]
    function () {
        return false;
    };
