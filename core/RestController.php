<?php
/**
 * PHP version 7
 *
 * @package   KISSREST
 * @author    Gabriel Prieto <gabriel@levitarmouse.com>
 * @copyright 2017 Levitarmouse
 * @link      coming soon
 */

namespace levitarmouse\kiss_rest\core;

use levitarmouse\kiss_orm\Mapper;
use levitarmouse\core\Codes;


use PRPOpCodes;
use sm\mgmt\SessionDTO;
use sm\mgmt\Session;

/**
 * Description of RestController
 *
 * @author gabriel
 */
class RestController
{
    private $_alreadyStarted = false;

    public $httpMethod;
    public $what;

    protected $oCfg;

    public function __construct($cfg = null) {
        $this->oCfg = $cfg;
    }

    public function __call($name, $request)
    {
        /**
         * @var \levitarmouse\core\StdObject $bodyParams
         */
        $bodyParams = $this->getRequestParams($request);

        if (method_exists($this, $name)) {
            return $this->$name($bodyParams);
        }

        throw new \Exception(Codes::INVALID_COMPONENT);
    }

    /*
     * @return levitarmouse\core\StdObject
     */
    protected function getRequestParams($request = null) {

        if (is_a($request, '\levitarmouse\core\StdObject')) {
            $inputParams = $request;
        } else {
            $inputParams = ($request !== null && is_array($request)) ? $request[0] : new \levitarmouse\core\StdObject();
        }

        return $inputParams;
    }

    public function setConfig(\levitarmouse\core\ConfigIni $configIni) {
        $this->oCfg = $configIni;
    }

//    public function formatDateTime($dateTime, $currFromat = 'd-m-Y', $returnFormat = 'Y-M-d') {
//
//        $result = \levitarmouse\common_tools\dateTime\Format::date($dateTime, $currFromat, $returnFormat)
//
//        $day = $month = $year = '';
//        if ($currFromat == 'd-m-Y') {
//            list($day, $month, $year) = explode('-', $dateTime);
//        }
//
//        $result = $dateTime;
//        switch ($returnFormat ) {
//            case 'Y-M-d':
//                $year = str_pad($year, 4, '20', STR_PAD_LEFT);
//                $month = str_pad($month, 2, '0', STR_PAD_LEFT);
//                $day = str_pad($day, 2, '0', STR_PAD_LEFT);
//
//                $result = $year.'-'.$month.'-'.$day;
//        }
//
//        return $result;
//    }

    public function get($params = null) {
        $params->method = strtoupper(__METHOD__);
        return $this->defaultHandler($params);
    }

    public function post($params = null) {
        $params->method = strtoupper(__FUNCTION__);
        return $this->defaultHandler($params);
    }

    public function put($params = null) {
        $params->method = strtoupper(__FUNCTION__);
        return $this->defaultHandler($params);
    }

    public function delete($params = null) {
        $params->method = strtoupper(__FUNCTION__);
        return $this->defaultHandler($params);
    }

    public function patch($params = null) {
        $params->method = strtoupper(__FUNCTION__);
        return $this->defaultHandler($params);
    }

    public function options($params = null) {

        $params->method = strtoupper(__FUNCTION__);
        return $this->defaultHandler($params);
    }

    private function defaultHandler($params = null)
    {
        $thisClass = get_class($this);

        if ($thisClass != '\levitarmouse\kiss_rest\core\RestController') {
            $method = $this->httpMethod;

            $exception = new \levitarmouse\core\Request_Exception(Codes::DEPLOYMENT_EXCEPTION);
            $exception->httpCode = 403;

            $exception->httpMethod = $method;
            $exception->exceptionDescription = "You dont implemented ".$method."  Response in your Controller yet. ";
            $exception->exceptionDescription .= "Do it inside him and configure routing in routes.ini as /endpoint@$method = name where name is the function name which handle the HTTP method";

            $exception->exceptionDescription  = "You did not implement the method that manage ".$method."s in your controller yet. ";
            $exception->exceptionDescription .= "Do it inside it and after configure the routing in routes.ini as /endpoint@".$method." = name";
            $exception->exceptionDescription .= "(where name is the the function in your controller that manage the $method HTTP method.)";

            throw $exception;
        } else {
            $response = new \levitarmouse\kiss_rest\core\HelloResponse();
        }

        $response->sessionId = session_id();
        $response->time      = date('d-m-Y H:i:s');

        return $response;
    }

    /**
     * @param levitarmouse\util\security\InjectionTestResult $params
     */
    protected function validateRequestParams($params, $omissions = array(), $specialChars = array()) {

        if (is_a($params, 'levitarmouse\core\StdObject')) {
            $params = $params->getAttribs();
        }

    //    $checker = new \levitarmouse\util\security\InjectionChecker('NUMBERS-ALPHA', true, null, null, $omissions);
        $dto = new \levitarmouse\util\security\InjectionCheckerRequest();
        $dto->omissions = $omissions;
        $dto->specialChars = $specialChars;

        $checker = new \levitarmouse\util\security\InjectionChecker($dto);

        $result = $checker->check($params, true);

        return $result;
    }

    protected function response($mixed) {

        $response = new Codes();
        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                $response->$key = $value;
            }
        } else {

            $type = '';
            if (is_object($mixed)) {
                $contentType = get_class($mixed);
                $aContentType = explode('\\', $contentType);
                $type = array_pop($aContentType);
            } else {
                $type = gettype($mixed);
            }

            $content = new \stdClass();
            $content->$type = $mixed;

            $response->content = $content;
        }

        return $response;
    }
}
