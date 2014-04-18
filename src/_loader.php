<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
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
require_once 'Common/Exception.php';

// traits
require_once 'Common/Helper.php';
require_once 'Common/Singleton.php';
require_once 'Common/Options.php';

// application
require_once 'Application/Application.php';
require_once 'Application/Exception/ApplicationException.php';

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
