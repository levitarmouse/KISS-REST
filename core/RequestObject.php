<?php

namespace levitarmouse\kiss_rest\core;

class RequestObject extends \levitarmouse\core\StdObject {
    
    public $reqEndpoint;

    public function __construct(\levitarmouse\core\StdObject $params) {
        parent::__construct();

        $this->aData = $params->aData;
    }

    public function get($name = '') {
        $value = $this->$name;

        return $value;
    }

    public function headers() {

        return $this->requestHeaders->getAttribs();
    }
    
    public function header($name = '') {

        $headers = $this->requestHeaders;
        $value = $headers->$name;

        return $value;
    }
}
