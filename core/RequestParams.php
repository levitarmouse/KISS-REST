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

class RequestParams
{
    protected $params;

    public function __construct($data, $method = '')
    {
        if ($data == null) {
            $data = array();
        }

        if ($method == 'GET') {

            $querystring = $data;

            if (is_string($querystring)) {
                $data = json_decode($querystring['params']);
            }
        }

        $params = new \levitarmouse\core\SmartObject();
        if (is_array($data) || is_object($data)) {
            $oParams = $params->analize($data);

        }
        $this->params = $oParams;
    }

    public function getContent($method = '') {

        if (is_a($this->params, 'levitarmouse\core\BasicObject')) {
            $content = $this->params;
        } else {
            if (is_array($this->params) || is_object($this->params) ) {
                $content = new \levitarmouse\core\BasicObject();
                foreach ($this->params as $attrib => $value) {

                    $currValue = $value;

                    if (is_string($currValue) ) {
                        $jsonInside = false;

                        $prospect = json_decode($currValue);
                        if (is_object($prospect)) {
                            $jsonInside = false;
                        }

                        if ($jsonInside) {
                            $objectInside = new \levitarmouse\core\BasicObject();
                            foreach ($prospect as $insideKey => $insideValue) {
                                $objectInside->$insideKey = $insideValue;
                            }
                            $currValue = $objectInside;
                        }
                    }

                    $content->$attrib = $currValue;
                }
            } else {
                $content = $this->params;
            }
        }

        return $content;
    }
}
