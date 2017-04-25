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

use rest\Response;

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
        $this->httpMethod = $method = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
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

        if ($thisClass != 'rest\RestController') {
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
            $response = new \rest\HelloResponse();
        }

        $response->sessionId = session_id();
        $response->time      = date('d-m-Y H:i:s');

        return $response;
    }

    /**
     *
     * @param levitarmouse\util\security\InjectionTestResult $params
     */
    protected function validateRequestParams($params, $omissions = array(), $specialChars = array()) {

        if (is_a($params, 'levitarmouse\core\Object')) {
            $params = $params->getAttribs();
        }

        $dto = new \levitarmouse\util\security\InjectionCheckerRequest();
        $dto->omissions = $omissions;
        $dto->specialChars = $specialChars;

        $checker = new \levitarmouse\util\security\InjectionChecker($dto);

        $result = $checker->check($params, true);

        return $result;
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
