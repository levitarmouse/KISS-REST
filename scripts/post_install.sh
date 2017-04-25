#!/bin/bash

mv ./vendor/levitarmouse/kiss_rest/index.php .;
mv ./vendor/levitarmouse/kiss_rest/.htaccess .;
mv ./vendor/levitarmouse/kiss_rest/App .;

mkdir -p config/kissrest;
cd ./config/kissrest;
cp -rp ../../vendor/levitarmouse/kiss_rest/config/rest.ini .;
cp -rp ../../vendor/levitarmouse/kiss_rest/config/Bootstrap.php .;
cp -rp ../../vendor/levitarmouse/kiss_rest/config/Autoload.php .;