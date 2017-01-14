<?php

include_once './config/config.php';

$restHandler = new \levitarmouse\rest\Rest(REST_CONFIG);

$restHandler->handleRequest();