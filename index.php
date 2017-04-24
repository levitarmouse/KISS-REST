<?php

include_once './config/kissrest/Bootstrap.php';

$composerAutoloader = __DIR__.'/vendor/autoload.php';
if (file_exists($composerAutoloader)) {
    require $composerAutoloader;
    
}

$restHandler = new rest\Rest(REST_CONFIG);

$restHandler->handleRequest();