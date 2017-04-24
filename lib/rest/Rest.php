<?php
/**
 * PHP version 7
 *
 * @package   KISSREST
 * @author    Gabriel Prieto <levitarmouse@gmail.com>
 * @copyright 2017 Levitarmouse
 * @link      coming soon
 */

namespace rest;

use \rest\Response;
use \levitarmouse\core\ConfigIni;
use \levitarmouse\core\Logger;

// para no enviar cookies a los controladores listados
$m = ($_SERVER['REQUEST_METHOD'] == 'POST');
$y = $_SERVER['REQUEST_URI'];
$x = preg_match('(pump/[a-z0-9A-Z]*)', $y);
if ($x && $m)  {
    ini_set('session.use_cookies', 0);
    ini_set('session.use_only_cookies', 0);
}

//ini_set('max_input_time', 90);

/**
 * @property \levitarmouse\core\ConfigIni $config Rest Config Objet from /config/rest.ini
 */
class Rest {

    protected $config;

    public function __construct($restConfig = '') {
        $this->config = null;

        session_start();

        if ($restConfig) {
            $this->initConfig($restConfig);
        }
    }

    public function initConfig($restConfigPath) {

        $restConfig = null;
        if (USE_MEMCACHE) {
            $memc = new \Memcache();

            $host = '127.0.0.1';
            $port = 11211;
            $memc->addserver($host, $port);

            $restConfig = $memc->get('RestConfig');
        }

        if ($restConfig) {
            $this->config = $restConfig;

            if (is_a($this->config, '\levitarmouse\core\ConfigIni')) {
                $memc->set('RestConfig', $this->config);
            }
        } else {
            if (is_string($restConfigPath)) {
                $this->config = new ConfigIni($restConfigPath, true);
            }
        }
    }

    protected function makeToken()
    {
        $token = microtime(true);
        $token = hash('sha256', $token);
        return $token;
    }

    protected function initSecurity() {

    }

    public function handleRequest() {

        $warnings = \levitarmouse\core\WarningsResponse::getInstance();

        if ($this->config === null
            || is_a($this->config, 'ConfigIni')) {
            throw new \Exception('REST_CONFIG_IS_NOT_DEFINED');
        } else {
            $restConfig = $this->config;
        }
        
        $bXSSTest = XSS_VALIDATION;
        $bCSRFTest = CSRF_VALIDATION;

        try {
            $input = file_get_contents("php://input");
            $aReq = json_decode(file_get_contents("php://input"));

//            Logger::log('RawRequest');
//            Logger::log($aReq);
            
            $invalidParams = null;

            if (!$aReq) {

                $aReq = $_REQUEST;

                if (!$aReq && isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
                    $aReq = $GLOBALS['HTTP_RAW_POST_DATA'];
                    if (is_string($aReq)) {
                        $aReq = json_decode($aReq);
                    }
                }
            }

            $apiType = 'REST';

            $method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');

            $params = (new \rest\RequestParams($aReq, $method))->getContent($method);

            if ($method == 'POST' && isset($params->HTTP_METHOD)) {
                if (in_array($params->HTTP_METHOD, array('GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'))) {
                    $method = $params->HTTP_METHOD;
                }
            }
            
            $params->http_method = $method;

            $what = null;
            $action = null;
            $with = null;

            // fix donweb
            $PATH_INFO = filter_input(INPUT_SERVER, 'REQUEST_URI');

            $PATH_INFO = str_replace(APP_NAME.'/', '', $PATH_INFO);

            if (strlen($PATH_INFO) == 1) {
                $PATH_INFO = null;
            }

            if (count($aPathInfo = explode('?', $PATH_INFO)) > 1) {
                $PATH_INFO = ($aPathInfo[0] != '/') ? $aPathInfo[0] : null;
            }
            // fix donweb

            $default = true;

            // Identifying an entity in the request
            if (isset($PATH_INFO)) {

                $PATH_INFO = str_replace(WWW_LINK_NAME, '', $PATH_INFO);

                $default = false;

                $whatArray = explode('/', $PATH_INFO);

                $hierarchySize = count($whatArray);

               if ($hierarchySize == 2) {
////                    $action = $this->getActionByHTTPMethod($method);
               }

                $what = (isset($whatArray[1]) ) ? $whatArray[1] : null;

                if ($hierarchySize == 3) {

                    $with = (isset($whatArray[2]) ) ? strtolower($whatArray[2]) : null;

                }

            }

            $oLogger = null;
            $oRequest = null;
            $result = null;

            // Frontal Controller idenfication
            if ($default) {
                $fwName  = CORE;

                $strController = (empty($what)) ? "DEFAULT.DEFAULT_CONTROLLER" : '';

                $classStr = $restConfig->get($strController);

                if ($classStr === 'RestController') {
                    $class = $fwName . '\rest\\' . $classStr;
                } else {
                    $class = $class = '\controllers\\' . $classStr;
                }
            }
            else {
                $strController = 'CONTROLLERS_ROUTING./'.$what;
                $classStr = $restConfig->get($strController);

                $class = '\controllers\\' . $classStr;
            }

            $handler = null;
            if (class_exists($class)) {
                $handler = new $class();
            }

            if (!$handler) {
                throw new \Exception(Response::INVALID_COMPONENT);
            } else {

                $params->id = $with;

                $handleHttpMethod = $method;

                $what = (!empty($what)) ? $what : $handleHttpMethod;
                
                $handler->what = $what;
                $handler->httpMethod = $method;

                $methodStr = $restConfig->get('METHODS_ROUTING./' . $what."@".$method);

                $aMethodStr = explode('-->', $methodStr);

                $bRAW      = (isset($aMethodStr[1]) && strtoupper($aMethodStr[1]) == 'RAW');
                if ($bRAW) {
                    $methodStr = $aMethodStr[0];
                }

                $bVIEW      = (isset($aMethodStr[1]) && strtoupper($aMethodStr[1]) == 'VIEW');
                if ($bVIEW) {
                    $methodStr = $aMethodStr[0];
                }
                $params->returnView = $bVIEW;

                if ($methodStr == null) {
                    // require basado en metodos POST, PUT, DELETE
                    $methodStr = $restConfig->get('METHODS_ROUTING.' . $method);
                }

                $methodStr = ($methodStr !== null) ? $methodStr : 'UndefiniedComponent';

                try {
                    
                    if (in_array($method, array('POST', 'PUT') ) )  {
                        if ($bCSRFTest) {
                            $dto = new \levitarmouse\tools\security\InjectionCheckerRequest();

                            $byUserCRUD = array('pass1', 'pass2', 'npass1', 'npass2', 'password');
                            $byPumpsInput = array('fechadato', 'niveldetanque', 'caudalacumulado', 'alarma1');

                            $omitions = array_merge($byUserCRUD, $byPumpsInput);
                            
                            $dto->omissions = $omitions;


                            $xssTest = new \levitarmouse\tools\security\InjectionChecker($dto);

                            $paramsToAnalize = $params->getAttribs();
                            $xssTestResult = $xssTest->check($paramsToAnalize);
                            
                            if ($xssTestResult->getStatus() == 'INVALID') {
                                $invalidList = $xssTestResult->getInvalid();
                                
                                foreach ($invalidList as $key => $value) {
                                    $warnings->appendWarning($key, '');
                                }                                
                                throw new \Exception(Response::INVALID_PARAMS);
                            }
                        }
                    }

                    $authController = new \controllers\AuthController();
                    
                    $sessionProfile = $authController->getSessionProfile();
                    $params->sessionProfile = $sessionProfile;
                    
                    ///////////////////////////////////////
                    //// CALL THE HANDLER  ////////////////
                    ///////////////////////////////////////
                    $result = $handler->$methodStr($params);
                    ///////////////////////////////////////

//		    Logger::log('RawResponse');
//		    Logger::log($result);

                    $rawResponse = $bRAW;

                    if (is_a($result, '\rest\Response')) {
                        if ($result->errorId != 0) {

                            throw new \Exception($result->description);
                        }
                        $result->setError(\rest\Response::NO_ERRORS);
                    } else {
                        if ($rawResponse) {
                            $this->rawResponse($result);
                        } else {
                            $response = new Response();
                            $response->responseContent = $result;

                            $response->setError(\rest\Response::NO_ERRORS);
                            $result = $response;
                        }
                    }

                }

                catch (\levitarmouse\core\HTTP_Exception $ex) {
                    
                    header('HTTP/1.1 500');
                    
                    $result = new Response();

                    $message = $ex->getMessage();
                    if ($message) {

                        $result->setError($message);
                    }
                    $result->exception = $ex;
                }
                catch (\Exception $ex) {
                    
                    $result = new Response();
                    
                    $bWarn = $warnings->has;
                    if ($bWarn) {
                        $result->warnings = $warnings;
                    }
                    
                    if ($ex->getMessage()) {
                        $message = $ex->getMessage();
                        if ($obj = json_decode($message)) {
                            $errorCode = $obj->errorCode;
                            $result->setError($errorCode);
                        } else {
                            if (is_a($ex, 'levitarmouse\util\security\XSSException')) {
                                $result->exception = $ex->getWrongs();
                            }
                            $result->setError($message);
                        }
                    } else {
                        $result->exception = $ex;
                        $result->setError(rest\Response::INTERNAL_ERROR);
                    }
                }
            }
        } catch (\Exception $ex) {

            header('HTTP/1.1 500');
            
            global $invalidParams;

            $mesg = $ex->getMessage();
            if (!$mesg) {
                $mesg = Response::INTERNAL_ERROR;
            }

            $result = new \rest\Response();
            $result->setError($mesg);

            if ($invalidParams) {
                $invalidParamsDescription = $invalidParams;
                //        $invalidParamsDescription = json_encode($invalidParams);
                $invalidParams = null;
                $result->errorDescription = $invalidParamsDescription;
            }
        }

        IF ($apiType == 'REST') {
            $this->responseJson($result);
            }
        }

    public function validateCsrf($token, $bCreateOrLogin) {

        $sessionId = session_id();

        $session = $_SESSION;

        $validationStr = $sessionId . $token;

        $csrf = (isset($session['CSRF'])) ? $session['CSRF'] : '';

        $bCSRF_OK = ($csrf == $validationStr) ? 1 : 0;

        return $bCSRF_OK;
    }

    public function httpResponse($result = '') {

    }

    public function rawResponse($response = null) {

        if (is_a($response, 'rest\RawResponseDTO')) {
            if ($response->httpCode) {
                header('HTTP/1.1 '.$response->httpCode);
            }

            if ($response->contentType) {
                switch (strtoupper($response->contentType)) {
                    case 'PLAIN':
                        header('Content-Type: text/plain');
                        echo $response->content;
                    break;
                    case 'JSON':
                        header('Content-type: application/json');
                        echo json_encode($response->content);
                    break;
                    default:
                        header('Content-type: application/json');
                    break;
                }
            }            
        } else {
            echo $response;
        }

        die;
    }

    public function responseJson($result = null) {

        if (isset($result->exception)) {
            $exception = $result->exception;
            $httpCode = $exception->httpCode;

            if ($httpCode)
                http_response_code($httpCode);

            $toShow = $result->description;
            if ($exception->exceptionDescription) {
                $toShow = $exception->exceptionDescription;
            }
            $result = $toShow;
        } 

	header('Content-type: text/json');
	header('Content-type: application/json');

        echo json_encode($result);
    }
}
