<?php
/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link https://github.com/bluzphp/framework
 */

declare(strict_types=1);

/**
 * Files in this list is core of framework
 * use require_once it's really faster than use Loader for it
 *
 * Use absolute path `/...` for avoid usage `include_path` for search files
 *
 * @author   Anton Shevchuk
 */

// @codeCoverageIgnoreStart

// traits
require_once '/Common/Container/Container.php';
require_once '/Common/Container/JsonSerialize.php';
require_once '/Common/Container/MagicAccess.php';
require_once '/Common/Container/RegularAccess.php';
require_once '/Common/Helper.php';
require_once '/Common/Nil.php';
require_once '/Common/Options.php';
require_once '/Common/Singleton.php';

// application
require_once '/Application/Application.php';

// proxy package
require_once '/Proxy/ProxyTrait.php';
require_once '/Proxy/Cache.php';
require_once '/Proxy/Config.php';
require_once '/Proxy/Logger.php';
require_once '/Proxy/Messages.php';
require_once '/Proxy/Response.php';
require_once '/Proxy/Request.php';
require_once '/Proxy/Router.php';
require_once '/Proxy/Session.php';
require_once '/Proxy/Translator.php';

// packages and support
require_once '/Config/Config.php';
require_once '/Controller/Controller.php';
require_once '/Controller/Data.php';
require_once '/Controller/Meta.php';
require_once '/Http/RequestMethod.php';
require_once '/Http/StatusCode.php';
require_once '/Messages/Messages.php';
require_once '/Response/Response.php';
require_once '/Request/RequestFactory.php';
require_once '/Router/Router.php';
require_once '/Session/Session.php';
require_once '/Translator/Translator.php';
require_once '/View/ViewInterface.php';
require_once '/View/View.php';

// @codeCoverageIgnoreEnd
