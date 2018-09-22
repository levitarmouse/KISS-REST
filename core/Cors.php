<?php
namespace levitarmouse\kiss_rest\core;

class Cors {
//        Origin:http://localhost:8080
//        Access-Control-Request-Method:POST
//        Access-Control-Request-Headers:origin, content-type, accept
//        Referer:http://localhost:8080/home

    protected $preflightDetected;

    protected $response;

    protected $origin;
    protected $method;
    protected $headers;

    private $_originIndexes;
    private $_methodIndexes;
    private $_headersIndexes;

    public function __construct(array $reqHeaders) {

        $this->requestHeaders = $reqHeaders;

        $this->_originIndexes  = array();
        $this->_methodIndexes  = array();
        $this->_headersIndexes = array();

        $this->response = array();

        $this->_init();

        $bOrigin  = false;
        $bMethod  = false;
        $bHeaders = false;
        foreach ($reqHeaders as $index => $value) {

            $testIndex = trim(strtoupper($index));

            if (!$bOrigin) {
                $bOrigin = array_key_exists($testIndex, $this->_originIndexes);
            }
            if (!$bMethod) {
                $bMethod = array_key_exists($testIndex, $this->_methodIndexes);
            }
            if (!$bHeaders) {
                $bHeaders = array_key_exists($testIndex, $this->_headersIndexes);
            }

            if (($bOrigin || $bMethod || $bHeaders)) {

                $headerParam = str_replace('Request', 'Allow', $index);
                $this->response[$headerParam] = $value;
            }
        }

        $this->preflightDetected = ($bOrigin || $bMethod || $bHeaders);

    }

    private function _init() {
        $this->_originIndexes['ACCESS-CONTROL-ALLOW-ORIGIN'] = true;
        $this->_originIndexes['HTTP-ORIGIN'] = true;
        $this->_originIndexes['HTTP_ORIGIN'] = true;
        $this->_originIndexes['ORIGIN'] = true;
        $this->_originIndexes['ACCESS-CONTROL-ALLOW-ORIGIN'] = true;

        $this->_methodIndexes['ACCESS-CONTROL-ALLOW-METHOD'] = true;
        $this->_methodIndexes['HTTP-ACCESS-CONTROL-REQUEST-METHOD'] = true;
        $this->_methodIndexes['HTTP_ACCESS_CONTROL_REQUEST_METHOD'] = true;
        $this->_methodIndexes['ACCESS-CONTROL-REQUEST-METHOD'] = true;
        $this->_methodIndexes['ACCESS_CONTROL_REQUEST_METHOD'] = true;

        $this->_headersIndexes['ACCESS-CONTROL-ALLOW-HEADERS'] = true;
        $this->_headersIndexes['HTTP-ACCESS-CONTROL-REQUEST-HEADERS'] = true;
        $this->_headersIndexes['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'] = true;
        $this->_headersIndexes['ACCESS-CONTROL-REQUEST-HEADERS'] = true;
        $this->_headersIndexes['ACCESS_CONTROL_REQUEST_HEADERS'] = true;

    }

    public function isCorsPreflightRequest() {
        return $this->preflightDetected;

    }

    public function getResponseHeaders() {

        return $this->response;

    }

}




