#!/bin/bash
php ./bin/console doctrine:database:drop --force --env=test
php ./bin/console doctrine:database:create --env=test
php ./bin/console doctrine:schema:update --force --env=test

php ./bin/console doctrine:query:sql --env=test "insert into card_type values (null, 'task');"
php ./bin/console doctrine:query:sql --env=test "insert into card_type values (null, 'bug');"
php ./bin/console doctrine:query:sql --env=test "insert into card_type values (null, 'doc');"
php ./bin/console doctrine:query:sql --env=test "insert into card_type values (null, 'chore');"

php ./bin/console doctrine:query:sql --env=test "insert into status values (null, 'todo', 1);"
php ./bin/console doctrine:query:sql --env=test "insert into status values (null, 'in progress', 2);"
php ./bin/console doctrine:query:sql --env=test "insert into status values (null, 'done', 3);"

php ./bin/console doctrine:query:sql --env=test "insert into card values (null, 'missing persons', 'manage developers', 'todo', 'task');"
