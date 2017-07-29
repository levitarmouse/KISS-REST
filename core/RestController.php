<?php
/**
 * PHP version 7
 *
 * @package   KISSREST
 * @author    Gabriel Prieto <levitarmouse@gmail.com>
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

    public function __call($name, $request)
    {
        $arguments = $request[0];

        $TokenValidation = (1*$this->oCfg->get('TOKEN_OMISSIONS.'.$name) == 0);

        $token = $arguments->token;

//        $reqTokenSize = strlen($reqToken);
//        if ($reqTokenSize > 70) {
//            $token = substr($reqToken, 0, 65);
//            $user_id = substr($reqToken, 65, $reqTokenSize);
//        }
//        $token = '';
//        $user_id = $arguments->id;

        if ($TokenValidation) {
            if (!$token) {
                $headers = getallheaders();
//                                         AuthorizationCSRF
                $csrf = (isset($headers['AuthorizationCSRF'])) ? $headers['AuthorizationCSRF'] : '';

                if (!$csrf) {
                    $csrf = (isset($headers['Authorizationcsrf'])) ? $headers['Authorizationcsrf'] : '';
                }
                if (!$csrf) {
                    $csrf = (isset($headers['authorizationcsrf'])) ? $headers['authorizationcsrf'] : '';
                }
                if (!$csrf) {
                    $csrf = (isset($headers['token'])) ? $headers['token'] : '';
                }

                $arguments->token = $csrf;
            }

            $validation = $this->validateActivity($arguments);

            if ($validation->statusCode == Codes::CHECKING_IN) {

            } else {
                if (!$validation->valid || $validation->status == 'IDLE') {
                    throw new \Exception($validation->statusCode);
                }
            }

            $arguments->user_id = $validation->user_id;
            $arguments->token   = $validation->token;
        }


        if (method_exists($this, $name)) {
            return $this->$name($arguments);
        }

        throw new \Exception(Codes::INVALID_COMPONENT);
    }

    public function setConfig(\levitarmouse\core\ConfigIni $configIni) {
        $this->oCfg = $configIni;
    }

    public function formatDateTime($dateTime, $currFromat = 'd-m-Y', $returnFormat = 'Y-M-d') {

        $day = $month = $year = '';
        if ($currFromat == 'd-m-Y') {
            list($day, $month, $year) = explode('-', $dateTime);
        }

        $result = $dateTime;
        switch ($returnFormat ) {
            case 'Y-M-d':
                $year = str_pad($year, 4, '20', STR_PAD_LEFT);
                $month = str_pad($month, 2, '0', STR_PAD_LEFT);
                $day = str_pad($day, 2, '0', STR_PAD_LEFT);

                $result = $year.'-'.$month.'-'.$day;
        }

        return $result;
    }

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

        if ($thisClass != 'levitarmouse\core\RestController') {
            $method = $this->httpMethod;

            $exception = new \levitarmouse\core\Request_Exception(Codes::DEPLOYMENT_EXCEPTION);
            $exception->httpCode = 403;

            $exception->httpMethod = $method;
            $exception->exceptionDescription = "You dont implemented ".$method."  Response in your Controller yet. ";
            $exception->exceptionDescription .= "Do it inside him and configure routing in rest.ini as /endpoint@$method = name where name is the function name which handle the HTTP method";

            $exception->exceptionDescription  = "You did not implement the method that manage ".$method."s in your controller yet. ";
            $exception->exceptionDescription .= "Do it inside it and after configure the routing in rest.ini as /endpoint@".$method." = name";
            $exception->exceptionDescription .= "(where name is the the function in your controller that manage the $method HTTP method.)";

            throw $exception;
        } else {
            $response = new \levitarmouse\rest\HelloResponse();
        }

        $response->sessionId = session_id();
        $response->time      = date('d-m-Y H:i:s');
//        $response->token  = session_id();

        return $response;
    }

    protected function validateActivity($request) {

        $authController = new \controllers\AuthController();
        $validation = $authController->validateActivity($request);
        return $validation;
    }

    /**
     *
     * @param levitarmouse\util\security\InjectionTestResult $params
     */
    protected function validateRequestParams($params, $omissions = array(), $specialChars = array()) {

//        $omissions = array('token');

        if (is_a($params, 'levitarmouse\core\Object')) {
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


    protected function getExpenseFreqByCode($code = '') {

        $freqName = '';
        if (is_string($code)) {
            switch (strtoupper($code)) {
                case 'D':
                    $freqName = 'DAILY';
                    break;
                case 'W':
                    $freqName = 'WEEKLY';
                    break;
                case 'M':
                    $freqName = 'MONTHLY';
                    break;
                case 'BM':
                    $freqName = 'BIMONTHLY';
                    break;
                case 'BA':
                    $freqName = 'BIANNUAL';
                    break;
                case 'Y':
                    $freqName = 'YEARLY';
                    break;
                default:
                case '':
                    $freqName = 'NONE';
                    break;
            }
        }
        return $freqName;
    }

}
