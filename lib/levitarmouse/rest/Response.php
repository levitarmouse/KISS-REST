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

class Response
{
    const COMPONENT_ALREADY_EXIST = 'COMPONENT_ALREADY_EXIST';
    const EXPIRED_LG_SESSION = 'EXPIRED_LG_SESSION';
    const EXPIRED_SESSION = 'EXPIRED_SESSION';
    const INACTIVE_ERROR = 'INACTIVE_ERROR';
    const INTERNAL_ERROR = 'INTERNAL_ERROR';
    const DEPLOYMENT_EXCEPTION = 'DEPLOYMENT_EXCEPTION';
    const INVALID_COMPONENT = 'INVALID_COMPONENT';
    const INVALID_CONFIGURATION = 'INVALID_CONFIGURATION';
    const INVALID_NUMBER = 'INVALID_NUMBER';
    const INVALID_PARAMS = 'INVALID_PARAMS';
    const INVALID_SIZE = 'INVALID_SIZE';
    const LOGIN_IS_REQUIRED = 'LOGIN_IS_REQUIRED';
    const MAIL_ALREADY_IN_USE = 'MAIL_ALREADY_IN_USE';
    const NICK_NAME_OR_PASSTOKEN_EMPTY = 'NICK_NAME_OR_PASSTOKEN_EMPTY';
    const NOTHING_TO_DO = 'NOTHING_TO_DO';
    const NO_ERRORS = 'NO_ERRORS';
    const PARAMETERS_TOO_LONG = 'PARAMETERS_TOO_LONG';
    const TOKEN_IS_REQUIRED = 'TOKEN_IS_REQUIRED';
    const UNAUTHORIZED_ACCESS = 'UNAUTHORIZED_ACCESS';
    const USER_ALREADY_EXIST = 'USER_ALREADY_EXIST';
    const USER_DOES_NOT_EXIST = 'USER_DOES_NOT_EXIST';
    const VALID_CSRF_TOKEN_IS_REQUIRED = 'VALID_CSRF_TOKEN_IS_REQUIRED';
    const VALID_TOKEN_IS_REQUIRED = 'VALID_TOKEN_IS_REQUIRED';

    private $_errors;
    public $errorId;
    public $errorCode;
    public $errorDescription;
    
    public $responseType;

    protected function init()
    {
        if (isset($this->_errors)) {
            return;
        }
        $this->errors = array();

        $this->_errors = array(
            self::INTERNAL_ERROR          => array('id' => -1, 'description' => 'Se produjo un error desconocido'),
            self::DEPLOYMENT_EXCEPTION    => array('id' => -1, 'description' => 'There is no method in the registered controller that receive the HTTP method used'),
            self::NO_ERRORS               => array('id' => 0, 'description' => 'SUCCESS'),
            self::NOTHING_TO_DO           => array('id' => 0, 'description' => 'NOTHING_TO_DO'),
            self::TOKEN_IS_REQUIRED       => array('id' => 1, 'description' => 'The token is required'),
            self::VALID_TOKEN_IS_REQUIRED => array('id' => 2, 'description' => 'The token is invalid'),
            self::LOGIN_IS_REQUIRED       => array('id' => 3, 'description' => 'Login is required'),
            self::UNAUTHORIZED_ACCESS     => array('id' => 4, 'description' => 'Unauthorized access'),
            self::INVALID_COMPONENT       => array('id' => 5, 'description' => 'Invalid Component'),
            self::INVALID_CONFIGURATION   => array('id' => 6, 'description' => 'Configuration is not available'),
            self::USER_DOES_NOT_EXIST     => array('id' => 7, 'description' => 'User does not exist'),
            self::USER_ALREADY_EXIST      => array('id' => 8, 'description' => 'User already exist'),
            self::MAIL_ALREADY_IN_USE     => array('id' => 9, 'description' => 'mail already in use'),
            self::COMPONENT_ALREADY_EXIST => array('id' => 100, 'description' => 'el elemento que desea crear ya existe'),
            self::EXPIRED_SESSION         => array('id' => 150, 'description' => 'Expired Session'),
            self::EXPIRED_LG_SESSION      => array('id' => 151, 'description' => 'Expired Session'),
            self::VALID_CSRF_TOKEN_IS_REQUIRED => array('id' => 505, 'description' => 'CSRF detected'),

            self::NICK_NAME_OR_PASSTOKEN_EMPTY => array('id' => 110, 'description' => 'Usuario o Clave invalidos'),

            self::PARAMETERS_TOO_LONG => array('id' => 108, 'description' => 'Valores demasiado extensos'),
            self::INVALID_PARAMS => array('id' => 109, 'description' => 'Valores invalidos o faltantes'),
            self::INVALID_SIZE => array('id' => 112, 'description' => 'El valor de campo excede la longitud permitida'),
            self::INVALID_NUMBER => array('id' => 111, 'description' => 'Se esperaba un valor numerico'),
            self::INACTIVE_ERROR => array('id' => 1000, 'description' => 'Unknown activity'),
        );
    }

    public function setError($code, $detail = '')
    {
        $this->init();

        $this->errorCode        = $code;

        if (isset($this->_errors[$code])) {
            $this->errorId          = $this->_errors[$code]['id'];
            $this->errorDescription = $this->_errors[$code]['description']. ' '.$detail;
        } else {
            $this->errorId          = $this->_errors['INTERNAL_ERROR']['id'];
            $this->errorDescription = $this->_errors['INTERNAL_ERROR']['description']. ' '.$detail;
        }

    }

}
