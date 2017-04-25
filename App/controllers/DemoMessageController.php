<?php

namespace controllers;

class DemoMessageController extends \rest\RestController{

    public function saludo() {
        return "Bienvenido a KISS-REST. Los saluda amablemente ".__METHOD__;
    }
}
