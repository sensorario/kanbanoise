#!/bin/bash
php ./bin/console doctrine:database:drop --force --env=test
php ./bin/console doctrine:database:create --env=test
php ./bin/console doctrine:schema:update --force --env=test

php ./bin/console doctrine:query:sql --env=test "insert into user values (null, 'admin', 'password', 'email', 1);"

php ./bin/console doctrine:query:sql --env=test "insert into member values (null, 'sensorario');"

php ./bin/console doctrine:query:sql --env=test "insert into board values (null, 'sensorario', 4);"

php ./bin/console doctrine:query:sql --env=test "insert into status values (null, 'backlog', 1, null);"
php ./bin/console doctrine:query:sql --env=test "insert into status values (null, 'todo', 2, null);"
php ./bin/console doctrine:query:sql --env=test "insert into status values (null, 'in progress', 3, 1);"
php ./bin/console doctrine:query:sql --env=test "insert into status values (null, 'verify', 4, 1);"
php ./bin/console doctrine:query:sql --env=test "insert into status values (null, 'done', 5, null);"

php ./bin/console doctrine:query:sql --env=test "insert into card_type values (null, 'task');"
php ./bin/console doctrine:query:sql --env=test "insert into card_type values (null, 'bug');"
