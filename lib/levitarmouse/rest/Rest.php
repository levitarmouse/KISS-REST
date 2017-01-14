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
        
        $memc = new \Memcache();

        $host = '127.0.0.1';
        $port = 11211;
        $memc->addserver($host, $port);
        
        $restConfig = $memc->get('RestConfig');
        
        if ($restConfig) {
            $this->config = $restConfig;
        } else {
            if (is_string($restConfigPath)) {
                $this->config = new ConfigIni($restConfigPath);
            }
            if (is_a($this->config, '\levitarmouse\core\ConfigIni')) {
                $memc->set('RestConfig', $this->config);
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

    public function handleRequest($params = null) {
        
        if ($this->config === null
            || is_a($this->config, 'ConfigIni')) {
            throw new \Exception('REST_CONFIG_IS_NOT_DEFINED');
        } else {
            $restConfig = $this->config;
        }
        
        try {
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

            $what = null;
            $action = null;
            $with = null;

            $PATH_INFO = filter_input(INPUT_SERVER, 'REDIRECT_URL');
            
            $default = true;
            
            // Identifying an entity in the request
            if (isset($PATH_INFO)) {
                
//                $PATH_INFO = str_replace('/rest', '', $PATH_INFO);
                $PATH_INFO = str_replace(WWW_LINK_NAME, '', $PATH_INFO);
                
                $default = false;
                
                $whatArray = explode('/', $PATH_INFO);

                $hierarchySize = count($whatArray);

                if ($hierarchySize == 2) {

//                    $action = $this->getActionByHTTPMethod($method);
                }

                $what = (isset($whatArray[1]) ) ? strtolower($whatArray[1]) : null;
                
                if ($hierarchySize == 3) {

                    $with = (isset($whatArray[2]) ) ? strtolower($whatArray[2]) : null;

//                    $action = $this->getActionByHTTPMethod($method);
                }
            }

//            $oDB = null;
            $oLogger = null;
            $oRequest = null;
            $result = null;
//            $token = null;
            
            // si no hubo indicación de ruta, se llama al keep alive de la session
            
            // Frontal Controller idenfication
            if ($default) {
                
                $fwName  = CORE;

                $strController = (empty($what)) ? "DEFAULT.DEFAULT_CONTROLLER" : '';

                $classStr = $restConfig->get($strController);

//                if (!$classStr) {
//                    $strController = "CONTROLLERS." . strtoupper($what) . "_CONTROLLER";
//                    $classStr = $restConfig->get($strController);
//                }
                if ($classStr === 'RestController') {
                    $class = $fwName . '\rest\\' . $classStr;
                } else {
                    $class = $class = '\controllers\\' . $classStr;
                }
            } 
            else {
                $strController = 'CONTROLLERS.'.$what;
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

                $oParams = new \levitarmouse\rest\RestParams($aReq, $method);

                $params = $oParams->getParams($method);

                $params->id = $with;

                $defaultHandler = $this->config->get('DEFAULT.DEFAULT_HANDLER');
                
                $what = (!empty($what)) ? $what : $defaultHandler;

                $methodStr = $restConfig->get('METHODS_ROUTING.' . $method."@".strtoupper($what));

                if ($methodStr == null) {
                    // require basado en metodos POST, PUT, DELETE
                    $methodStr = $restConfig->get('METHODS_ROUTING.' . strtolower($method));
                }

                $methodStr = ($methodStr !== null) ? $methodStr : 'UndefiniedComponent';

//                error_log("method: " . $method . "\n", 3, '/tmp/csrf.log');

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

//                        error_log("csrf: " . $csrf . "\n", 3, '/tmp/csrf.log');


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

                
                
                
                try {

                    $result = $handler->$methodStr($params);

                    if ($result->errorId != 0) {

                        throw new \Exception($result->description);
                    }

                    $result->setError(\levitarmouse\rest\Response::NO_ERRORS);
                } catch (\Exception $ex) {
                    $result = new levitarmouse\rest\Response();
                    if ($ex->getMessage()) {
                        $result->setError($ex->getMessage());
                        if (is_a($ex, 'levitarmouse\util\security\XSSException')) {
                            $result->description = $ex->getWrongs();
                        }
                    } else {
                        $result->setError(levitarmouse\rest\Response::INTERNAL_ERROR);
                    }
                }
            }
        } catch (\Exception $ex) {

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
                $result->description = $invalidParamsDescription;
            }
        }
        
        IF ($apiType == 'REST') {
            $this->responseJson($result);
        }
    }

    protected function getActionByHTTPMethod($method = '') {

        $action = '';

        switch (strtoupper($method)) {
            case 'GET':
                $action = 'get';
                break;
            case 'POST':
                $action = 'post';
                break;
            case 'PUT':
                $action = 'put';
                break;
            case 'DELETE':
                $action = 'delete';
                break;
            case 'PATCH':
                $action = 'patch';
                break;
            case 'OPTIONS':
                $action = 'options';
                break;
        }
        return $action;
    }

    public function validateCsrf($token, $bCreateOrLogin) {

        $sessionId = session_id();

        $session = $_SESSION;

        $validationStr = $sessionId . $token;

        $csrf = (isset($session['CSRF'])) ? $session['CSRF'] : '';

        $bCSRF_OK = ($csrf == $validationStr) ? 1 : 0;

        return $bCSRF_OK;
    }

    public function responseJson($result = '') {

        header('Content-type: text/json');
        header('Content-type: application/json');
        echo json_encode($result);
    }

}
