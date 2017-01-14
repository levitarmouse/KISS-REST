<?php

namespace controllers;

class TimeController extends \levitarmouse\rest\Response {

    public function dateTime() {
        $dateTime = date('d-m-Y H:i:s');
        
        $response = new \levitarmouse\rest\Response();
        
        $response->dateTime = $dateTime;
        
        return $response;        
    }
}
