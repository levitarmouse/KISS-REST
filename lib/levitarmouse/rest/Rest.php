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

use \levitarmouse\rest\Response;
use \levitarmouse\core\ConfigIni;

// para no eviar cookies al controller linkeado con pump/
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

        if ($this->config === null
            || is_a($this->config, 'ConfigIni')) {
            throw new \Exception('REST_CONFIG_IS_NOT_DEFINED');
        } else {
            $restConfig = $this->config;
        }
        
        try {
            $input = file_get_contents("php://input");
            $aReq = json_decode(file_get_contents("php://input"));

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

            $params = (new \levitarmouse\rest\RequestParams($aReq, $method))->getContent($method);

            if ($method == 'POST' && isset($params->HTTP_METHOD)) {
                if (in_array($params->HTTP_METHOD, array('GET', 'POST', 'PUT', 'DELETE', 'PUSH', 'OPTIONS'))) {
                    $method = $params->HTTP_METHOD;
                }
            }

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
                
                if ($hierarchySize == 4) {

                    $group = (isset($whatArray[2]) ) ? strtolower($whatArray[2]) : null;
                    
                    $with  = (isset($whatArray[3]) ) ? strtolower($whatArray[3]) : null;
                }
                
                if ($hierarchySize == 5) {

                    $group = (isset($whatArray[2]) ) ? strtolower($whatArray[2]) : null;

                    $subgroup = (isset($whatArray[3]) ) ? strtolower($whatArray[3]) : null;
                    
                    $with  = (isset($whatArray[4]) ) ? strtolower($whatArray[4]) : null;
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

                $methodStr = $restConfig->get('METHODS_ROUTING./' . $what."@".$method);

                $aMethodStr = explode('-->', $methodStr);

                $bRAW      = (isset($aMethodStr[1]) && strtoupper($aMethodStr[1]) == 'RAW');
                if ($bRAW) {
                    $methodStr = $aMethodStr[0];
                }

                if ($methodStr == null) {
                    // require basado en metodos POST, PUT, DELETE
                    $methodStr = $restConfig->get('METHODS_ROUTING.' . $method);
                }

                $methodStr = ($methodStr !== null) ? $methodStr : 'UndefiniedComponent';

/*
                if (in_array(strtoupper($method), array('POST', 'PUT', 'DELETE', 'GET'))) {
                    if (!in_array(strtolower($methodStr), array('hello'))
                    ) {

                        $headers = getallheaders();

                        $csrf = (isset($headers['AuthorizationCSRF'])) ? $headers['AuthorizationCSRF'] : '';

                        if (!$csrf) {
                            $csrf = (isset($headers['Authorizationcsrf'])) ? $headers['Authorizationcsrf'] : '';
                        }
                        if (!$csrf) {
                            $csrf = (isset($headers['authorizationcsrf'])) ? $headers['authorizationcsrf'] : '';
                        }

                        $bCreateOrLogin = (strtoupper($what) == 'ACCOUNT' && strtoupper($methodStr) == 'CREATE');

                        $params->token = $csrf;
                    }

//                    if (strtoupper($method) == 'GET') {
//                        $headers = getallheaders();
//
//                        $csrf = (isset($headers['AuthorizationCSRF'])) ? $headers['AuthorizationCSRF'] : '';
//
//                        if (!$csrf) {
//                            $csrf = (isset($headers['Authorizationcsrf'])) ? $headers['Authorizationcsrf'] : '';
//                        }
//                        if (!$csrf) {
//                            $csrf = (isset($headers['authorizationcsrf'])) ? $headers['authorizationcsrf'] : '';
//                        }
//                        $params->token = $csrf;
//                    }
                }
*/

                try {

                    ///////////////////////////////////////
                    //// CALL THE HANDLER  ////////////////
                    ///////////////////////////////////////
                    $result = $handler->$methodStr($params);
                    ///////////////////////////////////////

                    $rawResponse = $bRAW;

                    if (is_a($result, '\levitarmouse\rest\Response')) {
                        if ($result->errorId != 0) {

                            throw new \Exception($result->description);
                        }
                        $result->setError(\levitarmouse\rest\Response::NO_ERRORS);
                    } else {
                        if ($rawResponse) {
                            $this->rawResponse($result);
                        } else {
//                        if ($rawResponse !== true) {
                            $response = new Response();
                            $response->responseContent = $result;

                            $response->setError(\levitarmouse\rest\Response::NO_ERRORS);
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
                    
//                    header('HTTP/1.1 500');
                    
                    $result = new Response();
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
                        $result->setError(levitarmouse\rest\Response::INTERNAL_ERROR);
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

            $result = new \levitarmouse\rest\Response();
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

    public function rawResponse(RawResponseDTO $result) {

        if ($result->httpCode) {
            header('HTTP/1.1 '.$result->httpCode);
        }

        if ($result->contentType) {
            switch (strtoupper($result->contentType)) {
                case 'PLAIN':
                    header('Content-Type: text/plain');
                    echo $result->content;
                break;
                case 'JSON':
                    header('Content-type: application/json');
                    echo json_encode($result->content);
                break;
                default:
                header('Content-type: application/json');
                break;
            }
        }

        die;
    }

    public function responseJson($result = null) {

        if (isset($result->exception)) {
            $exception = $result->exception;
            $httpCode = $exception->httpCode;

            if ($httpCode)
                http_response_code($httpCode);

            if (!$exception->exceptionDescription) {
//                $toShow = $result->description;
            } else {
                $toShow = $exception->exceptionDescription;
            }
            echo $toShow;
        } else {
            header('Content-type: text/json');
            header('Content-type: application/json');
            echo json_encode($result);
        }
    }
}
