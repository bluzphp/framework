<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

/**
 * Files in this list is core of framework
 * use require_once it's really faster than use Loader for it
 *
 * @author   Anton Shevchuk
 * @created  15.07.11 13:21
 */

// @codeCoverageIgnoreStart

// add current directory to include path
set_include_path(dirname(__FILE__) . PATH_SEPARATOR . get_include_path());

// traits
require_once 'Common/Container/Container.php';
require_once 'Common/Container/JsonSerialize.php';
require_once 'Common/Container/MagicAccess.php';
require_once 'Common/Container/RegularAccess.php';
require_once 'Common/Helper.php';
require_once 'Common/Nil.php';
require_once 'Common/Options.php';
require_once 'Common/Singleton.php';

// application
require_once 'Application/Application.php';

// proxy package
require_once 'Proxy/AbstractProxy.php';
require_once 'Proxy/Cache.php';
require_once 'Proxy/Config.php';
require_once 'Proxy/Logger.php';
require_once 'Proxy/Messages.php';
require_once 'Proxy/Response.php';
require_once 'Proxy/Request.php';
require_once 'Proxy/Router.php';
require_once 'Proxy/Session.php';
require_once 'Proxy/Translator.php';

// packages and support
require_once 'Cache/CacheInterface.php';
require_once 'Cache/TagableInterface.php';
require_once 'Cache/Cache.php';
require_once 'Config/Config.php';
require_once 'Controller/Reflection.php';
require_once 'Messages/Messages.php';
require_once 'Response/AbstractResponse.php';
require_once 'Request/AbstractRequest.php';
require_once 'Router/Router.php';
require_once 'Session/Session.php';
require_once 'Translator/Translator.php';
require_once 'View/ViewInterface.php';
require_once 'View/View.php';
require_once 'Layout/Layout.php';

// @codeCoverageIgnoreEnd
