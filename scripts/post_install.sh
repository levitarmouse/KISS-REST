#!/bin/bash

mkdir -p config/kissrest;
mkdir -p App/controllers;

cp -rp ./vendor/levitarmouse/kiss_rest/App/controllers/* ./App/controllers;
cp -rp ./vendor/levitarmouse/kiss_rest/index.php.dist ./index.php;
cp -rp ./vendor/levitarmouse/kiss_rest/dist.htaccess ./.htaccess;

cd ./config/kissrest;
cp -rp ../../vendor/levitarmouse/kiss_rest/config/rest.ini .;
cp -rp ../../vendor/levitarmouse/kiss_rest/config/Bootstrap.php .;
cp -rp ../../vendor/levitarmouse/kiss_rest/config/Autoload.php .;