#!/bin/bash

echo "asignando permisos a las carpetas logs";
find . -name logs -print0 | xargs -0 chmod -R 777;

echo "asignando permisos a la carpeta uploads";
find . -name uploads -print0 | xargs -0 chmod -R 777;
