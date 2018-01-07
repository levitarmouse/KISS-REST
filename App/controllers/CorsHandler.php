<?php

namespace controllers;

class CorsHandler {

    public function options() {

        $corsHeaders = self::getCorsHeaders();

        $allowHeaders = array();
        foreach ($corsHeaders as $key => $value) {
            $allowHeaders[str_replace('Request', 'Allow', $key)] = $value;
        }

        $corsHeaders = array_merge($corsHeaders, $allowHeaders);

        return response()->json('ALLOWED', 200, $corsHeaders);
    }

    public static function setCorsHeaders() {

        $allHeaders = getallheaders();

        $bCtrlMethod      = isset($allHeaders['Access-Control-Request-Method']);
        $bCtrlHeaders     = isset($allHeaders['Access-Control-Request-Headers']);
        $bCtrlOriginShort = isset($allHeaders['Origin']);
        $bCtrlOriginLarge = isset($allHeaders['Access-Control-Allow-Origin']);
        $bCtrlCredential  = isset($allHeaders['Access-Control-Allow-Headers']);

        if ($bCtrlMethod) {
            header('Access-Control-Request-Method', $allHeaders['Access-Control-Request-Method']);
        }
        if ($bCtrlHeaders) {
            header('Access-Control-Request-Headers', $allHeaders['Access-Control-Request-Headers']);
        }
        if ($bCtrlOriginShort) {
            header('Origin', $allHeaders['Origin']);
            header('Access-Control-Allow-Origin', $allHeaders['Origin']);
        }
        if ($bCtrlOriginLarge) {
            header('Origin', $allHeaders['Access-Control-Allow-Origin']);
            header('Access-Control-Allow-Origin', $allHeaders['Access-Control-Allow-Origin']);
        }
        if ($bCtrlCredential) {
            header('Access-Control-Allow-Headers', $allHeaders['Access-Control-Allow-Headers']);
        }
    }

    public static function getCorsHeaders() {

        $responseArray = array();

        $allHeaders = getallheaders();

        $bCtrlMethod      = isset($allHeaders['Access-Control-Request-Method']);
        $bCtrlHeaders     = isset($allHeaders['Access-Control-Request-Headers']);
        $bCtrlOriginShort = isset($allHeaders['Origin']);
        $bCtrlOriginLarge = isset($allHeaders['Access-Control-Allow-Origin']);
        $bCtrlCredential  = isset($allHeaders['Access-Control-Allow-Headers']);

        if ($bCtrlMethod) {
            $responseArray['Access-Control-Request-Method'] =  $allHeaders['Access-Control-Request-Method'];
        }
        if ($bCtrlHeaders) {
            $responseArray['Access-Control-Request-Headers'] = $allHeaders['Access-Control-Request-Headers'];
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
