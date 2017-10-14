<?php

namespace controllers;

class DemoKissMessageController extends \rest\RestController{

    public function saludo() {
        return "Bienvenido a KISS-REST. Los saluda amablemente ".__METHOD__;
    }
}
