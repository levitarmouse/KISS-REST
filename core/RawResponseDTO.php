<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace levitarmouse\kiss_rest\core;

/**
 * Description of RawResponseDTO
 *
 * @author gabriel
 */

/* content type options
//    application/json
//    application/x-www-form-urlencoded
//    application/pdf
//    multipart/form-data
//    text/html
//    image/png
//    image/jpeg
//    image/gif
*/
class RawResponseDTO {
    public $httpCode;
    public $contentType;

    public $content;

    function __construct($httpCode = null, $contentType = null) {
        $this->httpCode = $httpCode;
        $this->contentType = $contentType;
        $this->content = null;
    }

    public function setCode($code) {
        $this->httpCode = $code;
    }

    public function setContentType($contentType) {
        $this->contentType = $contentType;
    }

    public function setContent($content) {
        $this->content = $content;
    }
}

