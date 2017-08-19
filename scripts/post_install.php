<?php
echo "KISS REST POST INSTALL SCRIPT";

$fwPath      = __DIR__;
$fwCfgPath   = $fwPath. '/config';
$restCfgPath = $fwPath. '/config/kissrest';

echo PHP_EOL;

if (!is_dir($fwCfgPath)) {
    mkdir($fwCfgPath);
}

echo "creando: ".$restCfgPath.PHP_EOL;

if (!is_dir($restCfgPath)) {
    mkdir($restCfgPath);
} else {
    echo $restCfgPath.PHP_EOL;
}

if (!is_dir($fwPath.'/App')) {
    mkdir($fwPath.'/App');
} else {
    echo $fwPath.'/App'.PHP_EOL;
}

if (!is_dir($fwPath.'/App/controllers')) {
    mkdir($fwPath.'/App/controllers');
} else {
    echo $fwPath.'/App/controllers'.PHP_EOL;
}

$htaccess    = './.htaccess';
$restRouting = './config/kissrest/rest.ini';
$bootstrap   = './config/kissrest/Bootstrap.php';
$autloader   = './config/kissrest/Autoload.php';

copy('./vendor/levitarmouse/kiss_rest/App/controllers/DemoMessageController.php', './App/controllers/DemoMessageController.php');
copy('./vendor/levitarmouse/kiss_rest/App/controllers/DemoTimeController.php', './App/controllers/DemoTimeController.php');
copy('./vendor/levitarmouse/kiss_rest/index.php.dist', './index.php');

if (!file_exists($htaccess)) {
    copy('./vendor/levitarmouse/kiss_rest/dist.htaccess', $htaccess);
} else {
    echo 'INFO -> ALREADY EXIST ->'.$htaccess.PHP_EOL;
}

if (!file_exists($restRouting)) {
    copy('./vendor/levitarmouse/kiss_rest/config/rest.ini', $restRouting);
} else {
    echo 'INFO -> ALREADY EXIST ->'.$restRouting.PHP_EOL;
}

if (!file_exists($bootstrap)) {
    copy('./vendor/levitarmouse/kiss_rest/config/Bootstrap.php', $bootstrap);
} else {
    echo 'INFO -> ALREADY EXIST ->'.$bootstrap.PHP_EOL;
}

if (!file_exists($autloader)) {
    copy('./vendor/levitarmouse/kiss_rest/config/Autoload.php', $autloader);
} else {
    echo 'INFO -> ALREADY EXIST ->'.$autloader.PHP_EOL;
}
