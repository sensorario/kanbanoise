#!/bin/bash
php ./bin/console doctrine:database:drop --force --env=test
php ./bin/console doctrine:database:create --env=test
php ./bin/console doctrine:schema:update --force --env=test

php ./bin/console doctrine:query:sql --env=test "insert into status values (null, 'backlog', 1, null);"
php ./bin/console doctrine:query:sql --env=test "insert into status values (null, 'todo', 2, null);"
php ./bin/console doctrine:query:sql --env=test "insert into status values (null, 'in progress', 3, 1);"
php ./bin/console doctrine:query:sql --env=test "insert into status values (null, 'done', 4, null);"

php ./bin/console doctrine:query:sql --env=test "insert into card_type values (null, 'task');"
php ./bin/console doctrine:query:sql --env=test "insert into card_type values (null, 'bug');"
