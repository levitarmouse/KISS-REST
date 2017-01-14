#!/bin/bash
/usr/bin/find . -name tmp -print0 | xargs -0 chmod -R 777;
/usr/bin/find . -name cache -print0 | xargs -0 chmod -R 777;
/usr/bin/find . -name logs -print0 | xargs -0 chmod -R 777;
