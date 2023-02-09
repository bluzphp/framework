<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
 */

declare(strict_types=1);

namespace Bluz\Controller\Helper;

use Bluz\Controller\Controller;
use Bluz\Proxy\Response;
use Bluz\Response\ResponseType;

/**
 * Switch layout
 *
 * @param $file
 */
return
    function ($file) {
        /**
         * @var Controller $this
         */
        Response::setContentType(ResponseType::FILE);
        $this->assign('FILE', $file);
        $this->disableLayout();
        $this->disableView();
    };
