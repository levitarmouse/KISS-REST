<?php

define('CORE', 'levitarmouse');

//$useMemCache = class_exists('Memcache');
$useMemCache = false;

if (!defined('USE_MEMCACHE')) {
    define("USE_MEMCACHE", $useMemCache);
}

if (!defined('ROOT_PATH')) {
    define("ROOT_PATH", realpath(__DIR__."/../../")."/");
}
$root_path = ROOT_PATH;

if (!defined('APP_PATH')) {
    define("APP_PATH", ROOT_PATH.'App/');
}
$app_path = APP_PATH;

if (!defined('LOGS_PATH')) {
    define("LOGS_PATH", "/tmp/kissrest.logs");
}

if (!defined('EXTERNALS_PATH')) {
    define("EXTERNALS_PATH", ROOT_PATH.'externals/');
}
$externals_path = EXTERNALS_PATH;

if (!defined('LIB_PATH')) {
    define("LIB_PATH", ROOT_PATH.'lib/');
}
$lib_path = LIB_PATH;

if (!defined('CONFIG_PATH')) {
    define("CONFIG_PATH", ROOT_PATH.'config/kissrest/');
}
$config_path = CONFIG_PATH;

if (!defined('SERVICE_PATH')) {
    define("SERVICE_PATH", ROOT_PATH.'services/');
}
$service_path = SERVICE_PATH;

if (!defined('BUSSINES_LOGIC_PATH')) {
    define("BUSSINES_LOGIC_PATH", ROOT_PATH.'src/');
}
$app_path = APP_PATH;

if (!defined('VIEWS_PATH')) {
    define("VIEWS_PATH", BUSSINES_LOGIC_PATH.'views/');
}
$views_path = APP_PATH;

define("VENDOR_PATH", ROOT_PATH.'vendor/');

define("KISS_REST_PATH", VENDOR_PATH.'levitarmouse/kiss_rest/lib/');
$kiss_vendor_path = KISS_REST_PATH;

define ('REST_CONFIG', CONFIG_PATH.'rest.ini');

define ('UPLOADS_LOCATION', ROOT_PATH.'uploads/');
define ('PUBLISH_LOCATION', '');
define ('IMAGES_SOURCE', '');

define ('XSS_VALIDATION', false);

define ('CSRF_VALIDATION', false);

$aWebServicesPSR0 = array();
$aWebServicesPSR0[] = KISS_REST_PATH;
$aWebServicesPSR0[] = LIB_PATH;
$aWebServicesPSR0[] = APP_PATH;
$aWebServicesPSR0[] = BUSSINES_LOGIC_PATH;
$aWebServicesPSR0[] = VENDOR_PATH;


//$scriptName = filter_input(INPUT_SERVER, 'SCRIPT_NAME');
$scriptName = $_SERVER['SCRIPT_NAME'];
$aLinkName  = explode('/', $scriptName);
$garbage = array_pop($aLinkName);
$linkName   = implode('/', $aLinkName);

define('WWW_LINK_NAME', $linkName);

require_once 'Autoload.php';
