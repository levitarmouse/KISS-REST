<?php
/**
 * ConfigIni
 *
 * @category  CORE
 * @package   Core
 * @created   2014
 * @author    Gabriel Prieto <gab307@gmail.com>
 * @copyright 2012 Levitarmouse
 * @license
 * @link
 */

namespace levitarmouse\core;

use Exception;
use stdClass;

class SmartObject
{
    private $_source;

    public function __construct($source = null)
    {
        $this->_source       = $source;

//        if (!empty($source)) {
//            $this->analize();
//        }
    }

    public function getObject($source = null) {

        if ($source) {
            $this->_source = $source;
        }

        $obj = $this->analize();

        return $obj;
    }

    protected function analize() {

        $src = $this->_source;

        $obj = new Object();

        if (is_string($src)) {
            if ($oFromJson = json_decode($src)) {
                foreach ($oFromJson as $key => $value) {
                    $obj->$key = $value;
                }
            } else {
                $obj->string = $src;
            }
        }

        if (is_object($src)) {
            foreach ($oFromJson as $key => $value) {
                $obj->$key = $value;
            }
        }

        return $obj;
    }

}