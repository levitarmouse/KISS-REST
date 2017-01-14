<?php

define('CORE', 'levitarmouse');

if (!defined('ROOT_PATH')) {
    define("ROOT_PATH", realpath(__DIR__."/../")."/");
}
$root_path = ROOT_PATH;

if (!defined('APP_PATH')) {
    define("APP_PATH", ROOT_PATH.'App/');
}
$app_path = APP_PATH;

if (!defined('LOGS_PATH')) {
    define("LOGS_PATH", ROOT_PATH."logs/messages.logs");
}

if (!defined('APP_NAME')) {
    define('APP_NAME', 'OLX');
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
    define("CONFIG_PATH", ROOT_PATH.'config/');
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

define("VENDOR_PATH", ROOT_PATH.'vendor/');
    
define ('DB_CONFIG', CONFIG_PATH.'database.ini');

define ('REST_CONFIG', CONFIG_PATH.'rest.ini');


$aWebServicesPSR0 = array();
$aWebServicesPSR0[] = LIB_PATH;
$aWebServicesPSR0[] = APP_PATH;
$aWebServicesPSR0[] = BUSSINES_LOGIC_PATH;
$aWebServicesPSR0[] = VENDOR_PATH;


$scriptName = filter_input(INPUT_SERVER, 'SCRIPT_NAME');
$aLinkName  = explode('/', $scriptName);
$garbage = array_pop($aLinkName);
$linkName   = implode('/', $aLinkName);

define('WWW_LINK_NAME', $linkName);


require_once 'Autoload.php';