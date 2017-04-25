# KISS-REST (Documentación bajo revisión continua)
Por Alejandro Gabriel Prieto (Levitarmouse)

Instalación:

$cd /var/www
$composer create-project levitarmouse/kiss-rest kissrest "dev-dev"

Micro framework para gestionar APIS. 
(proximamente -> (Validaciones XSS y CSRF) y soporte OracleDB, MongoDB a través de KISS-FRAMEWORK)

Como usarlo:

La instalación límpia ya presenta un controlador que es capaz de manejar todas las peticiones
HTTP. Sin embargo el uso esperado es que crees tus propios controladores y en ellos implementes
la lógica que necesita tu API.

Para crear un nuevo controlador, solo dirigete a la carpeta /App/controllers
y crea allí una clase con el nombre que desees, no hay restricciones para los 
nombres de las classes más que las impuestas por PHP.
La clase debe pertenecer al namespace /controllers
Además es recomendable que los controladores extiendan de la clase \rest\RestController,
en principio solo para obtener ayuda de los mensajes que pudiera devolver si existen problemas
de integración. Luego será requerida para gestionar seguridad y centralizar lógica inherente a todos los
controladores.


Ejemplo:

Creamos... 

/App/controllers/MensajeController.php

<?php

namespace controllers;

class MensajeController {

    public function saludo() {
        return "Bienvenido a KISS-REST. Los saluda amablemente ".__METHOD__;
    }
}

Para poder acceder a dicho controlador, debes registrarlo. (1)

Para acceder a sus funciones, también debes configurar!
Debes asociarlas a los distintos métodos HTTP (2).
Ver: https://es.wikipedia.org/wiki/Hypertext_Transfer_Protocol
Estas dos cosas las haces en el archivo de configuración /config/rest.ini

(1)
En la sección [CONTROLLERS_ROUTING]
Asocias el nombre de la entidad requerida en la URL con el controlador que la atenderá

(2)
En la sección [METHODS_ROUTING]
Indicas la entidad asociandola al HTTP Method con un @.
El valor de cada registro es el nombre de la función que debe gestionar 
el método HTTP, en el Controller que se indicó en la sección [CONTROLLERS_ROUTING]


Por ejemplo, para el caso del Controller MensajeController ejemplificado más arriba podría ser:

[CONTROLLERS_ROUTING]
/mensaje = "MensajeController"

[METHODS_ROUTING]
/mensaje@GET = 'saludo'

Luego podrás ver el resultado accediendo a http://localhost/kissrest/mensaje

También es parte de la instalación un sencillo controlador que devuelve la fecha y hora de PHP.
Solo informativo, pero seguro evolucionará en un microservicio de calendario
