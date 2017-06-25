<?php
echo "KISS REST POST INSTALL SCRIPT";

$fwPath      = __DIR__;
//$ormPath     = $fwPath. '/vendor/levitarmouse/kiss_rest';
$fwCfgPath   = $fwPath. '/config';
$restCfgPath = $fwPath. '/config/kissrest';

echo PHP_EOL;

echo "creando: ".$restCfgPath.PHP_EOL;

if (!is_dir($fwCfgPath)) {
    mkdir($fwCfgPath);
}

mkdir($restCfgPath);

if (!is_dir($fwPath.'/App')) {
    mkdir($fwPath.'/App');
}
mkdir($fwPath.'/App/controllers');

copy('./vendor/levitarmouse/kiss_rest/App/controllers/DemoMessageController.php', './App/controllers/DemoMessageController.php');
copy('./vendor/levitarmouse/kiss_rest/App/controllers/DemoTimeController.php', './App/controllers/DemoTimeController.php');
copy('./vendor/levitarmouse/kiss_rest/index.php.dist', './index.php');
copy('./vendor/levitarmouse/kiss_rest/dist.htaccess', './.htaccess');


copy('./vendor/levitarmouse/kiss_rest/config/rest.ini', './config/kissrest/rest.ini');
copy('./vendor/levitarmouse/kiss_rest/config/Bootstrap.php', './config/kissrest/Bootstrap.php');
copy('./vendor/levitarmouse/kiss_rest/config/Autoload.php', './config/kissrest/Autoload.php');
