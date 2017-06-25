<?php
/**
 * PHP version 7
 *
 * @package   KISSREST
 * @author    Gabriel Prieto <levitarmouse@gmail.com>
 * @copyright 2017 Levitarmouse
 * @link      coming soon
 */

namespace levitarmouse\rest;
use levitarmouse\kiss_orm\Mapper;
use levitarmouse\rest\Response;

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
    protected $oDb;
    protected $oLogger;

    private $_alreadyStarted = false;
    
    public $httpMethod;    
    public $what;

    public function __call($name, $arguments)
    {
        throw new \Exception(Response::INVALID_COMPONENT);
    }

    public function __construct($dbCfg = null)
    {

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
        return $this->hello($params);
    }
    
    public function post($params = null) {
        $params->method = strtoupper(__FUNCTION__);
        return $this->hello($params);
    }
    
    public function put($params = null) {
        $params->method = strtoupper(__FUNCTION__);
        return $this->hello($params);
    }
    
    public function delete($params = null) {
        $params->method = strtoupper(__FUNCTION__);
        return $this->hello($params);
    }
    
    public function patch($params = null) {
        $params->method = strtoupper(__FUNCTION__);
        return $this->hello($params);
    }
    
    public function options($params = null) {
        $params->method = strtoupper(__FUNCTION__);
        return $this->hello($params);
    }
    
    public function hello($params = null) {
        
        $thisClass = get_class($this);
                
        if ($thisClass != 'levitarmouse\rest\RestController') {
            $method = $this->httpMethod;

            $exception = new \levitarmouse\core\Request_Exception(Response::DEPLOYMENT_EXCEPTION);
            $exception->httpCode = 40
                    ;
            $exception->httpMethod = $method;
            $exception->exceptionDescription = "You dont implemented ".$method."  Response in your Controller yet. ";
            $exception->exceptionDescription .= "Do it inside him and configure routing in rest.ini as /entity@$method = name where name is the function name which handle the HTTP method";
            
            $exception->exceptionDescription  = "You did not implement the method that manage ".$method."s in your controller yet. ";
            $exception->exceptionDescription .= "Do it inside it and after configure the routing in rest.ini as /entity@".$method." = name";
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

    protected function validateActivity($token, $user_id = null)
    {
        $result = new \sm\mgmt\ValidateActivityResultDTO();

        $sessionDto = new SessionDTO($token);
        $oSession   = new Session($sessionDto);
        unset($sessionDto);

        $bSessionExists   = $oSession->exists();
        $bSessionIsIdle   = $oSession->isIdle();
        $bSessionIsActive = $oSession->isActive();

        $bLogin = (strtoupper($this->what) == 'LOGIN');
        
        if ($bSessionExists) {

            // Validate the Session time
            $lastUpdate = $oSession->getSessionLastActivity($token);
            $bValid = true;

            $dates = $lastUpdate[0]['DATESDIFF'];

            list($hours, $minutes, $seconds) = explode(':', $lastUpdate[0]['TIMEDIFF']);

            $hours   += $dates*24;
            $minutes += $dates*1440;

            if (!$bLogin && $hours > 0) {
//            if (!$bLogin && $seconds > 10) {
                $result->statusCode  = Response::EXPIRED_LG_SESSION;
                $result->message     = 'Goodbye';
                $result->user_id     = $oSession->userid;
                $result->status      = $oSession->status;
                $result->valid       = false;
                $oSession->remove();
            } else if ($minutes > 45 ) {
                $result->statusCode  = Response::EXPIRED_SESSION;
                $result->message     = 'Goodbye';
                $result->user_id     = $oSession->userid;
                $result->status      = $oSession->status;
                $result->valid       = false;
                $oSession->remove();
            } else if ($bSessionIsIdle) {
                $oSession->last_update   = \levitarmouse\kiss_orm\Mapper::SQL_SYSDATE_STRING;
                $result->status = $oSession->status;
                $result->statusCode  = Response::UNAUTHORIZED_ACCESS;
                $oSession->modify();

            } else if ($bSessionIsActive) {
                if ($oSession->userid != $user_id) {
                        $result->statusCode  = Response::UNAUTHORIZED_ACCESS;
                        $result->message     = 'Goodbye';
                    $result->userid         = $oSession->userid;
                        $result->status = $oSession->status;
                        $result->valid       = false;
                        $oSession->remove();
                } else {
                    $oSession->last_update = Mapper::SQL_SYSDATE_STRING;
                    $oSession->modify();
                    $result->statusCode  = \sm\mgmt\PRPOpCodes::MULTI_LOGIN_SUCCESS;
                    $result->user_id     = $oSession->userid;
                    $result->status = $oSession->status;
                    $result->message     = 'Hello again';
                }

            } else if (!$bSessionIsActive) {
                $result->statusCode  = Response::INACTIVE_ERROR;
                $result->user_id     = null;
                $result->message     = 'Unknown';
                $result->status = $oSession->status;
                $result->valid       = false;
            } else {
                $result->statusCode  = Response::VALID_TOKEN_IS_REQUIRED;
                $result->message     = 'Goodbye';
                $result->status = $oSession->status;
                $result->valid = false;
            }
        } else {
                $result->statusCode  = Response::VALID_TOKEN_IS_REQUIRED;
                $result->message     = 'Goodbye';
                $result->valid = false;
        }
        return $result;
    }

    public function mamushash($str1, $str2 = '', $str3 = '', $version = 1) {
        if ($version == 1) {

            if ($str1 && $str2 && $str3) {
                return sha1($str1.sha1($str2.sha1($str3)));
            }

            if ($str1 && $str2) {

                return sha1($str1.sha1($str2));
            }

            if ($str1) {
                return sha1($str1.sha1($str1));
            }

            $hash = sha1($str);
        }
    }

    protected function response($mixed) {

        $response = new Response();
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
