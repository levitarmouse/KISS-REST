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
    const ALREADY_EXIST = 'ALREADY_EXIST';
    const DOESNT_EXIST = 'DOESNT_EXIST';
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
    const INVALID_LONG_PARAMS = 'INVALID_LONG_PARAMS';
    const INVALID_LIST_PARAMS = 'INVALID_LIST_PARAMS';
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
    const ACCESS_DENIED = 'ACCESS_DENIED';
    const ACCESS_ORG_DENIED = 'ACCESS_ORG_DENIED';
    const PASSWORD_FORMAT_ERROR = 'PASSWORD_FORMAT_ERROR';
    const PASSWORD_VERIFICATION_ERROR = 'PASSWORD_VERIFICATION_ERROR';
    const VALID_CSRF_TOKEN_IS_REQUIRED = 'VALID_CSRF_TOKEN_IS_REQUIRED';
    const VALID_TOKEN_IS_REQUIRED = 'VALID_TOKEN_IS_REQUIRED';
    const ADMIN_UPDATE_WARNING = 'ADMIN_UPDATE_WARNING';
    const ADMIN_ACCESS_INVALID = 'ADMIN_ACCESS_INVALID';

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
            self::INTERNAL_ERROR               => array('id' => -1, 'description' => 'Se produjo un error desconocido'),
            self::DEPLOYMENT_EXCEPTION         => array('id' => -1, 'description' => 'There is no method in the registered controller that receive the HTTP method used'),
            self::NO_ERRORS                    => array('id' => 0, 'description' => 'SUCCESS'),
            self::NOTHING_TO_DO                => array('id' => 0, 'description' => 'NOTHING_TO_DO'),
            self::TOKEN_IS_REQUIRED            => array('id' => 1, 'description' => 'The token is required'),
            self::VALID_TOKEN_IS_REQUIRED      => array('id' => 2, 'description' => 'The token is invalid'),
            self::LOGIN_IS_REQUIRED            => array('id' => 3, 'description' => 'Login is required'),
            self::UNAUTHORIZED_ACCESS          => array('id' => 4, 'description' => 'Autenticación fallida'),
            self::INVALID_COMPONENT            => array('id' => 5, 'description' => 'Invalid Component'),
            self::INVALID_CONFIGURATION        => array('id' => 6, 'description' => 'Configuration is not available'),
            self::USER_DOES_NOT_EXIST          => array('id' => 7, 'description' => 'User does not exist'),
            self::USER_ALREADY_EXIST           => array('id' => 8, 'description' => 'User already exist'),
            self::MAIL_ALREADY_IN_USE          => array('id' => 9, 'description' => 'EMail ya utilizado por otro usuario'),
            self::PASSWORD_FORMAT_ERROR        => array('id' => 10, 'description' => 'La Constraseña no tiene un formato válido'),
            self::PASSWORD_VERIFICATION_ERROR  => array('id' => 11, 'description' => 'La contraseña y su confirmación no coinciden'),
            self::COMPONENT_ALREADY_EXIST      => array('id' => 100, 'description' => 'el elemento que desea crear ya existe'),
            self::EXPIRED_SESSION              => array('id' => 150, 'description' => 'Expired Session'),
            self::EXPIRED_LG_SESSION           => array('id' => 151, 'description' => 'Expired Session'),
            self::ALREADY_EXIST                => array('id' => 152, 'description' => 'El elemento que desea crear ya existe'),
            self::DOESNT_EXIST                 => array('id' => 153, 'description' => 'El elemento que desea modificar no existe'),
            self::VALID_CSRF_TOKEN_IS_REQUIRED => array('id' => 505, 'description' => 'CSRF detected'),

            self::NICK_NAME_OR_PASSTOKEN_EMPTY => array('id' => 110, 'description' => 'Usuario o Clave invalidos'),
            self::ACCESS_DENIED                => array('id' => 114, 'description' => 'El acceso está restringido para sus credenciales de acceso'),
            self::ACCESS_ORG_DENIED            => array('id' => 115, 'description' => 'El acceso está restringido por Inhabilitación en jerarquía'),
            self::ADMIN_ACCESS_INVALID         => array('id' => 116, 'description' => 'Autenticación con provilegios inválida'),
            self::INVALID_LONG_PARAMS          => array('id' => 117, 'description' => 'Los parámetros exceden la longitud permitida'),

            self::PARAMETERS_TOO_LONG => array('id' => 108, 'description' => 'Valores demasiado extensos'),
            self::INVALID_PARAMS => array('id' => 109, 'description' => 'Valores invalidos o faltantes'),
            self::INVALID_NUMBER => array('id' => 111, 'description' => 'Se esperaba un valor numerico'),
            self::INVALID_LIST_PARAMS => array('id' => 113, 'description' => 'Parámetros invalidos o faltantes'),
            self::INVALID_SIZE => array('id' => 112, 'description' => 'El valor de campo excede la longitud permitida'),
            self::INVALID_NUMBER => array('id' => 111, 'description' => 'Se esperaba un valor numerico'),
            self::INACTIVE_ERROR => array('id' => 1000, 'description' => 'Unknown activity'),
            self::ADMIN_UPDATE_WARNING => array('id' => 1001, 'description' => 'Un usuario Administrador no se puede autocambiar de Rol. Otro Administrador debe hacerlo.'),
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
