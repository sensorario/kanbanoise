#!/bin/bash
mkdir -p html
php ./bin/phpunit --coverage-html=html
open html/index.html
