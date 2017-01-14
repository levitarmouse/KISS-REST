<?php

namespace levitarmouse\core;

use Exception;
use stdClass;

class Object
{
    private $_aData;

    public function __construct()
    {
        $this->_aData = array();
    }

    public function getAttribs($bAsObject = false, $bAsXml = false)
    {
        $mReturn = $this->_aData;
        if ($bAsObject) {
            $mReturn = $this->_arrayToObject($mReturn);
        } else if ($bAsXml) {
            $mReturn = $this->_arrayToXML($mReturn);
        }
        return $mReturn;
    }

    public function __get($name)
    {
        $return = ( isset($this->_aData[$name]) ) ? $this->_aData[$name] : null;
        return $return;
    }

    public function __set($name, $value)
    {
        $this->_aData[$name] = $value;
    }

    public function __isset($name)
    {
        $return = ( array_key_exists($name, $this->_aData) ) ? true : false;
        return $return;
    }

    public function __call($name, $arguments)
    {
        throw new Exception('ERROR_METHOD_DOES_NOT_EXIST ['.$name.']');
    }

    public static function __callStatic($name, $arguments)
    {
        throw new Exception('ERROR_STATIC_METHOD_DOES_NOT_EXIST ['.$name.']');
    }

    /**
     * Unset
     *
     * @param type $name Name
     *
     * @return none
     */
    public function __unset($name)
    {
    }

    /**
     * ArrayToObject
     *
     * @param type $aArray Array
     *
     * @return \stdClass
     */
    private function _arrayToObject($aArray = null)
    {
        $obj = new stdClass();
        ksort($aArray, SORT_STRING);
        if (is_array($aArray) && count($aArray) > 0) {
            foreach ($aArray as $sAttrib => $sValue) {
                $obj->$sAttrib = $sValue;
            }
        }
        $obj->objectStatus = $this->objectStatus;
        return $obj;
    }

    /**
     * ArrayToXml
     *
     * @param type $aArray String
     *
     * @return type
     */
    private function _arrayToXML($aArray = null)
    {
        ksort($aArray, SORT_STRING);
        $xml = '';
        if (is_array($aArray)) {
            foreach ($aArray as $sAttrib => $sValue) {
                $xml .= "<{$sAttrib}>{$sValue}</{$sAttrib}>\n";
            }
        }
        return $xml;
    }
}