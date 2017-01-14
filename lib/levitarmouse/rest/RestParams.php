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

class RestParams
{
    protected $params;

    public function __construct($data, $method = '')
    {
//        if ($method == 'GET' || $method == 'POST') {
        if ($method == 'GET') {
            $querystring = $data;

            if (isset($querystring['params'])) {
                if (is_string($querystring['params'])) {
                    $data = json_decode($querystring['params']);
            }
//            if (is_string($querystring)) {
            }
        }

//        $params = new \stdClass();
        $params = new \levitarmouse\core\Object();
        if ($data) {
            foreach ($data as $key => $value) {
                $params->$key = $value;
            }
        }
        $this->params = $params;
//        $this->returnParams();
    }

    public function getParams($method = '') {

//        if ($method == 'GET') {
//
//        }
        return $this->params;
    }
}
