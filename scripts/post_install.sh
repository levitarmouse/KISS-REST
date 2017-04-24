#!/bin/bash
mkdir -p config/kissrest;
cd ./config/kissrest;
cp -rp ../../vendor/levitarmouse/kiss_rest/config/rest.ini .;
cp -rp ../../vendor/levitarmouse/kiss_rest/config/config.php .;
cp -rp ../../vendor/levitarmouse/kiss_rest/config/Autoload.php .;