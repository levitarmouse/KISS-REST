<?php

namespace levitarmouse\kiss_rest\core;

class RequestObject extends \levitarmouse\core\Object {

//$params->urlParam1 = (isset($params->urlParam1)) ? $params->urlParam1 : $urlParam1;
//$params->urlParam2 = (isset($params->urlParam2)) ? $params->urlParam2 : $urlParam2;
//$params->urlParam3 = (isset($params->urlParam3)) ? $params->urlParam3 : $urlParam3;
//$params->urlParam4 = (isset($params->urlParam4)) ? $params->urlParam4 : $urlParam4;



    public function __construct(\levitarmouse\core\Object $params) {
        parent::__construct();

        $this->aData = $params->aData;
    }

    public function get($name = '') {
        $value = $this->$name;

        return $value;
    }

    public function header($name = '') {

        $headers = $this->requestHeaders;
        $value = $headers->$name;

        return $value;
    }
}
