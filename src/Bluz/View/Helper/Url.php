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
namespace Bluz\View\Helper;

use Bluz\Application;
use Bluz\View\View;
use Bluz\View\ViewException;

return

/**
 * @param string $module
 * @param string $controller
 * @param array  $params
 * @param bool   $checkAccess
 * @return string|null
 */
function ($module, $controller, array $params = [], $checkAccess = false) {
    /** @var View $this */
    $app = $this->getApplication();

    try {
        if ($checkAccess && !$app->isAllowedController($module, $controller, $params)) {
            return null;
        }
    } catch (\Exception $e) {
        throw new \Bluz\View\ViewException('Url View Helper: '.$e->getMessage());
    }

    if (null === $module) {
        $module = $app->getRequest()->getModule();
    }
    if (null === $controller) {
        $controller = $app->getRequest()->getController();
    }
    if (null === $params) {
        $params = $app->getRequest()->getParams();
    }

    return $app ->getRouter()
                ->url($module, $controller, $params);
};