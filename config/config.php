<?php

$domain = 'http://example.com';

$pathScriptsClasses = '/scripts/classes';

define('DB_HOST', 'db_host');
define('DB_USERNAME', 'db_username');
define('DB_PASSWORD', 'db_password');
define('DB_NAME', 'db_name');

define('PATH_FRAMEWORK', '/php-fw');
define('PATH_WWW', '/www');
define('PATH_PROJECT', PATH_WWW.'/project');
define('PATH_PROJECT_INC', PATH_PROJECT.'/inc');
define('PATH_PROJECT_LIB', PATH_PROJECT.'/lib');

define('URL_SITE', 'http://sitename.com');
define('URL_PROJECT', URL_SITE.'/project');
define('URL_IMAGES', URL_PROJECT.'/images');

require PATH_FRAMEWORK.'/Db.php';
require PATH_FRAMEWORK.'/Debug.php';

$db = new Db();
$debug = new Debug();
