#!/bin/bash
php ./bin/console doctrine:query:sql --env=test "insert into board values (null, 'sensorario', 4);"
