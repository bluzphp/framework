<?php

/**
 * Bluz Framework Component
 *
 * @copyright Bluz PHP Team
 * @link      https://github.com/bluzphp/framework
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
require_once __DIR__ . '/Common/Container/Container.php';
require_once __DIR__ . '/Common/Container/JsonSerialize.php';
require_once __DIR__ . '/Common/Container/MagicAccess.php';
require_once __DIR__ . '/Common/Container/RegularAccess.php';
require_once __DIR__ . '/Common/Helper.php';
require_once __DIR__ . '/Common/Nil.php';
require_once __DIR__ . '/Common/Options.php';
require_once __DIR__ . '/Common/Singleton.php';

// application
require_once __DIR__ . '/Application/Application.php';

// proxy package
require_once __DIR__ . '/Proxy/ProxyTrait.php';
require_once __DIR__ . '/Proxy/Cache.php';
require_once __DIR__ . '/Proxy/Config.php';
require_once __DIR__ . '/Proxy/Logger.php';
require_once __DIR__ . '/Proxy/Messages.php';
require_once __DIR__ . '/Proxy/Response.php';
require_once __DIR__ . '/Proxy/Request.php';
require_once __DIR__ . '/Proxy/Router.php';
require_once __DIR__ . '/Proxy/Session.php';
require_once __DIR__ . '/Proxy/Translator.php';

// packages and support
require_once __DIR__ . '/Config/Config.php';
require_once __DIR__ . '/Controller/Controller.php';
require_once __DIR__ . '/Controller/Data.php';
require_once __DIR__ . '/Controller/Meta.php';
require_once __DIR__ . '/Http/RequestMethod.php';
require_once __DIR__ . '/Http/StatusCode.php';
require_once __DIR__ . '/Messages/Messages.php';
require_once __DIR__ . '/Response/Response.php';
require_once __DIR__ . '/Request/RequestFactory.php';
require_once __DIR__ . '/Router/Router.php';
require_once __DIR__ . '/Session/Session.php';
require_once __DIR__ . '/Translator/Translator.php';
require_once __DIR__ . '/View/ViewInterface.php';
require_once __DIR__ . '/View/View.php';

// @codeCoverageIgnoreEnd
