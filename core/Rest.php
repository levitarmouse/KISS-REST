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

use \levitarmouse\core\ConfigIni;
use \levitarmouse\tools\logs\Logger;
use levitarmouse\kiss_rest\core\Response;
use levitarmouse\kiss_rest\core\RequestParams;
use levitarmouse\kiss_rest\core\CorsHandler;

// para no enviar cookies a los controladores listados
$m = ($_SERVER['REQUEST_METHOD'] == 'POST');
$y = $_SERVER['REQUEST_URI'];
$x = preg_match('(pump/[a-z0-9A-Z]*)', $y);
if ($x && $m)  {
    ini_set('session.use_cookies', 0);
    ini_set('session.use_only_cookies', 0);
}

ini_set('max_input_time', 90);

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
//            $input = file_get_contents("php://input");
            $aReq = json_decode(file_get_contents("php://input"));

//            var_dump("AA000001111");
//            var_dump($input);

//            var_dump("BB000001111");
//            var_dump($aReq);


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

//            $method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
            $method = $_SERVER['REQUEST_METHOD'];

//            var_dump("CC000001111");
//            var_dump($_SERVER);
//
//            var_dump("DDD00001111");
//            var_dump($method);

            $params = (new RequestParams($aReq, $method))->getContent($method);


//            var_dump("EEE00001111");
//            var_dump($params);


            if ($method == 'POST' && isset($params->HTTP_METHOD)) {
                if (in_array($params->HTTP_METHOD, array('GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'))) {
                    $method = $params->HTTP_METHOD;
                }
            }

            $params->http_method = $method;

            $what = null;
            $action = null;
            $urlParam1 = $urlParam2 = $urlParam3 = $urlParam4 = null;

//            $baseEndpoint = filter_input(INPUT_SERVER, 'SCRIPT_NAME');
            $baseEndpoint = $_SERVER['SCRIPT_NAME'];

//            var_dump("EEE00001111");
//            var_dump($params);

            $baseEndpoint = str_replace('/index.php', '', $baseEndpoint);

//            var_dump("FFFF00001111");
//            var_dump($baseEndpoint);

            // fix don_web
//            $PATH_INFO = filter_input(INPUT_SERVER, 'REQUEST_URI');
            $PATH_INFO = $_SERVER['REQUEST_URI'];

//            var_dump("GGGG00001111");
//            var_dump($PATH_INFO);

            $PATH_INFO = str_replace($baseEndpoint.'/', '', $PATH_INFO);

//            var_dump("HHHH00001111");
//            var_dump($PATH_INFO);

            if (strlen($PATH_INFO) == 1) {
                $PATH_INFO = null;
            }

//            var_dump("IIIII00001111");
//            var_dump($PATH_INFO);

            if (count($aPathInfo = explode('?', $PATH_INFO)) > 1) {
                $PATH_INFO = ($aPathInfo[0] != '/') ? $aPathInfo[0] : null;
            }
            // fix donweb

            $default = true;

            // Identifying an entity in the request
            if (isset($PATH_INFO) && !empty($PATH_INFO)) {

                $PATH_INFO = str_replace(WWW_LINK_NAME, '', $PATH_INFO);

                $default = false;

                $whatArray = explode('/', $PATH_INFO);

                $hierarchySize = count($whatArray);

                if ($hierarchySize == 1) {
                    $what = (isset($whatArray[0]) ) ? $whatArray[0] : null;
                }
                if ($hierarchySize == 2) {
                    $what = (isset($whatArray[0] ) ) ? $whatArray[0] : null;
                    $urlParam1 = (isset($whatArray[1] ) ) ? strtolower($whatArray[1]) : null;
                }
                if ($hierarchySize == 3) {
                    $what = (isset($whatArray[0]) ) ? $whatArray[0] : null;
                    $urlParam1 = (isset($whatArray[1] ) ) ? strtolower($whatArray[1]) : null;
                    $urlParam2 = (isset($whatArray[2] ) ) ? strtolower($whatArray[2]) : null;
                }
                if ($hierarchySize == 4) {
                    $what = (isset($whatArray[0]) ) ? $whatArray[0] : null;
                    $urlParam1 = (isset($whatArray[1] ) ) ? strtolower($whatArray[1]) : null;
                    $urlParam2 = (isset($whatArray[2] ) ) ? strtolower($whatArray[2]) : null;
                    $urlParam3 = (isset($whatArray[3] ) ) ? strtolower($whatArray[3]) : null;
                }
                if ($hierarchySize == 5) {
                    $what = (isset($whatArray[0]) ) ? $whatArray[0] : null;
                    $urlParam1 = (isset($whatArray[1] ) ) ? strtolower($whatArray[1]) : null;
                    $urlParam2 = (isset($whatArray[2] ) ) ? strtolower($whatArray[2]) : null;
                    $urlParam3 = (isset($whatArray[3] ) ) ? strtolower($whatArray[3]) : null;
                    $urlParam4 = (isset($whatArray[4] ) ) ? strtolower($whatArray[4]) : null;
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
                    $class = $fwName . '\kiss_rest\\core\\' . $classStr;
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
                $handler = new $class($this->config);
            }

            if (!$handler) {
                throw new \Exception(Response::INVALID_COMPONENT);
            } else {

                $params->urlParam1 = (isset($params->urlParam1)) ? $params->urlParam1 : $urlParam1;
                $params->urlParam2 = (isset($params->urlParam2)) ? $params->urlParam2 : $urlParam2;
                $params->urlParam3 = (isset($params->urlParam3)) ? $params->urlParam3 : $urlParam3;
                $params->urlParam4 = (isset($params->urlParam4)) ? $params->urlParam4 : $urlParam4;

                $handleHttpMethod = $method;

                $what = (!empty($what)) ? $what : $handleHttpMethod;

                $handler->what = $what;
                $handler->httpMethod = $method;


                $endpointRoute = $what."@".$method;
                if (in_array($endpointRoute, array('GET@GET', 'POST@POST', 'PUT@PUT',
                                                   'PATCH@PATCH', 'DELETE@DELETE',
                                                   'OPTIONS@OPTIONS') ) ) {
                    $endpointRoute = 'null';
                } else {
                    $endpointRoute = './'.$endpointRoute;
                }

                $methodStr = $restConfig->get('METHODS_ROUTING' . $endpointRoute);

                if ($methodStr === null) {
                    $methodStr = $restConfig->get('METHODS_ROUTING.' . $method);
                }

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

                $allHeaders = getallheaders();
                $headers = new \levitarmouse\core\BasicObject($allHeaders);

                $params->requestHeaders = $headers;

                try {

                    /*
                    if (in_array($method, array('POST', 'PUT', 'PATCH') ) )  {
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
                     */

                    $responsePreFlight = null;
                    if ($method == 'OPTIONS') {
                        $cors = $this->preFlightRequestTest();

                        if ($cors->isCorsPreflightRequest()) {
                            $responsePreFlight = $cors->getResponseHeaders();

                            $responsePreFlight = new RawResponseDTO();
                            $responsePreFlight->setCode(200);
                            $responsePreFlight->headers = $cors->getResponseHeaders();

                        }

                    }

                    if ($responsePreFlight) {
                        $result = $responsePreFlight;
                        $rawResponse = true;
                    } else {

                        $requestObjet = new RequestObject($params);

                        ///////////////////////////////////////
                        //// CALL THE HANDLER  ////////////////
                        ///////////////////////////////////////
                        $handler->setConfig($this->config);
                        $result = $handler->$methodStr($requestObjet);
                        ///////////////////////////////////////

                        $rawResponse = $bRAW;
                    }


                    if (is_a($result, '\levitarmouse\rest\Response')) {
                        if ($result->errorId != 0) {

                            throw new \Exception($result->description);
                        }
                        $result->setError(\levitarmouse\rest\Response::NO_ERRORS);
                    } else {
                        if ($rawResponse) {
                            $this->rawResponse($result);
                        } else {
                            $response = new Response();
                            $response->responseContent = $result;

                            $response->setError(\levitarmouse\core\Codes::NO_ERRORS);
                            $result = $response;
                        }
                    }

                    $result->warnings = $warnings;
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

            $result = new Response();
            $result->setError($mesg);

            if ($invalidParams) {
                $invalidParamsDescription = $invalidParams;
                //        $invalidParamsDescription = json_encode($invalidParams);
                $invalidParams = null;
                $result->errorDescription = $invalidParamsDescription;
            }
        }

        if ($apiType == 'REST') {
            $this->responseJson($result);
        }
    }

    /**
     * @return Cors
     */
    public function preFlightRequestTest() {

        $reqHeaders = getallheaders();

        $cors = new Cors($reqHeaders);

        return $cors;
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

    public function rawResponse(RawResponseDTO $response = null) {

        if (is_a($response, 'levitarmouse\kiss_rest\core\RawResponseDTO')) {

            if (count($response->headers) > 0) {
                foreach ($response->headers as $key => $value) {
                    header($key.': '.$value);
                }
            } else {
                if ($response->httpCode) {
                    header('HTTP/1.1 '.$response->httpCode);
                }
            }

            if ($response->contentType) {
                switch (strtoupper($response->contentType)) {
                    case 'HTML':
                        header('Content-Type: text/html');
                        echo $response->content;
                        die;
                    break;
                    case 'PLAIN':
                        header('Content-Type: text/plain');
                        echo $response->content;
                        die;
                    break;
                    case 'JSON':
                        header('Content-type: application/json');
                        echo json_encode($response->content);
                        die;
                    break;
                    default:
                        header('Content-type: application/json');
                    break;
                }
            }
        } else {
            echo $response;
        }

    }

    public function responseJson($result = null) {

        if (isset($result->exception)) {
            $exception = $result->exception;
            $httpCode = $exception->httpCode;

            if ($httpCode) {
                http_response_code($httpCode);
            }

            $toShow = $exception->exceptionDescription;

            $result = $toShow;
        }

	header('Content-type: application/json');

        CorsHandler::setCorsHeaders();

        echo json_encode($result, JSON_PRETTY_PRINT);
    }
}

if (!function_exists('getallheaders')) {
    function getallheaders() {
    $headers = [];
    foreach ($_SERVER as $name => $value) {
        if (substr($name, 0, 5) == 'HTTP_') {
            $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
        }
    }
    return $headers;
    }
}
