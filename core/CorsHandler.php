<?php

namespace levitarmouse\kiss_rest\core;

class CorsHandler {

    public function options() {

        self::setCorsHeaders();

        return response()->json('ALLOWED', 200, $corsHeaders);
    }

    public static function setCorsHeaders() {

        $corsHeaders = self::getCorsHeaders();

        foreach ($corsHeaders as $key => $value) {
            header('Content-type: application/json');
            header($key.': '.$value);
        }
    }

    public static function getCorsHeaders() {

        $responseArray = array();

        $allHeaders = getallheaders();

        $bCtrlMethod   = isset($allHeaders['Access-Control-Request-Method']);
        $bCtrlMethods  = isset($allHeaders['Access-Control-Request-Methods']);
        $bCtrlHeader      = isset($allHeaders['Access-Control-Request-Header']);
        $bCtrlHeaders     = isset($allHeaders['Access-Control-Request-Headers']);
        $bCtrlOriginShort = isset($allHeaders['Origin']);
        $bCtrlOriginLarge = isset($allHeaders['Access-Control-Allow-Origin']);
        $bCtrlCredential  = isset($allHeaders['Access-Control-Allow-Headers']);

        if ($bCtrlMethod) {
            $responseArray['Access-Control-Allow-Method']  =  $allHeaders['Access-Control-Request-Method'];
            $responseArray['Access-Control-Allow-Methods'] =  $allHeaders['Access-Control-Request-Method'];
        }

        if ($bCtrlMethods) {
            $responseArray['Access-Control-Allow-Method']  =  $allHeaders['Access-Control-Request-Methods'];
            $responseArray['Access-Control-Allow-Methods'] =  $allHeaders['Access-Control-Request-Methods'];
        }
        if ($bCtrlHeader) {
            $responseArray['Access-Control-Allow-Header'] = $allHeaders['Access-Control-Request-Header'];
            $responseArray['Access-Control-Allow-Headers'] = $allHeaders['Access-Control-Request-Header'];
        }
        if ($bCtrlHeaders) {
            $responseArray['Access-Control-Allow-Header'] = $allHeaders['Access-Control-Request-Headers'];
            $responseArray['Access-Control-Allow-Headers'] = $allHeaders['Access-Control-Request-Headers'];
        }
        if ($bCtrlOriginShort) {
            $responseArray['Origin'] = $allHeaders['Origin'];
            $responseArray['Access-Control-Allow-Origin'] = $allHeaders['Origin'];
        }
        if ($bCtrlOriginLarge) {
            $responseArray['Origin'] = $allHeaders['Access-Control-Allow-Origin'];
            $responseArray['Access-Control-Allow-Origin'] =  $allHeaders['Access-Control-Allow-Origin'];
        }
        if ($bCtrlCredential) {
            $responseArray['Access-Control-Allow-Headers'] = $allHeaders['Access-Control-Allow-Headers'];
        }

        return $responseArray;
    }
}
