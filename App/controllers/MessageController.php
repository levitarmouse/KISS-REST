<?php

namespace controllers;

class MessageController extends \levitarmouse\rest\RestController{

    public function saludo() {
        return "Bienvenido a KISS-REST. Los saluda amablemente ".__METHOD__;
    }
}
