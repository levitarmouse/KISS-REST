<?php

namespace controllers;

class DemoKissTimeController extends KissBaseController {

    public function dateTime() {
        $dateTime = date('d-m-Y H:i:s');

        return $dateTime;
    }
}
