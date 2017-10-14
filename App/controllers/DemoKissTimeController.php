<?php

namespace controllers;

class DemoKissTimeController extends \rest\RestController {

    public function dateTime() {
        $dateTime = date('d-m-Y H:i:s');

        $response = new \rest\Response();

        $response->dateTime = $dateTime;

        return $response;
    }
}
