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
 * files in this list is core of framework
 * use require_once it's really faster than use Loader for it
 *
 * @author   Anton Shevchuk
 * @created  15.07.11 13:21
 */

// add current directory to include path
set_include_path(dirname(__FILE__) . PATH_SEPARATOR . get_include_path());

// exceptions
require_once 'Exception.php';

// traits
require_once 'Helper.php';
require_once 'Singleton.php';
require_once 'Package.php';

// application
require_once 'Application.php';
require_once 'Application/ApplicationException.php';

// packages and support
require_once 'Config/Config.php';
require_once 'EventManager/Event.php';
require_once 'EventManager/EventManager.php';
require_once 'Messages/Messages.php';
require_once 'Request/AbstractRequest.php';
require_once 'Router/Router.php';
require_once 'Session/Session.php';
require_once 'Session/Store/AbstractStore.php';
require_once 'Translator/Translator.php';
require_once 'View/ViewInterface.php';
require_once 'View/View.php';
require_once 'View/Layout.php';
