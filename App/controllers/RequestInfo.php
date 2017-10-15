<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace controllers;

/**
 * Description of RequestInfo
 *
 * @author gabriel
 */
class RequestInfo extends KissBaseController {
    public function requestInfo(\levitarmouse\core\Object $request) {

        $return = $request->getAttribs();

        return $return;
    }
}
