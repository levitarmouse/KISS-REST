<?php

namespace controllers;

use \levitarmouse\kiss_rest\core\RawResponseDTO;

/**
 * Description of HomeController
 *
 * @author gabriel
 */
class HomeController extends KissBaseController {
    public function apiRoot($request) {

        $timeCtrl = new DemoKissTimeController();

        $timeResponse = $timeCtrl->dateTime();

        $phpTime = $timeResponse['phpTime'];

        $response = new RawResponseDTO();

        $response->setContentType(RawResponseDTO::JSON);
        $response->setContent($phpTime);

        return $response;
    }
}
