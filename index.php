<?php

include_once './config/config.php';

$composerAutoloader = __DIR__.'/vendor/autoload.php';
if (file_exists($composerAutoloader)) {
    require $composerAutoloader;
    
}

$restHandler = new \levitarmouse\rest\Rest(REST_CONFIG);

$restHandler->handleRequest();