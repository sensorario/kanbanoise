#!/bin/bash
php ./bin/console doctrine:database:drop --force --env=test
php ./bin/console doctrine:database:create --env=test
php ./bin/console doctrine:schema:update --force --env=test

php ./bin/console doctrine:query:sql --env=test "insert into card_type values (null, 'task');"
php ./bin/console doctrine:query:sql --env=test "insert into card_type values (null, 'bug');"
php ./bin/console doctrine:query:sql --env=test "insert into card_type values (null, 'doc');"
php ./bin/console doctrine:query:sql --env=test "insert into card_type values (null, 'chore');"

php ./bin/console doctrine:query:sql --env=test "insert into status values (null, 'backlog', 1);"
php ./bin/console doctrine:query:sql --env=test "insert into status values (null, 'todo', 2);"
php ./bin/console doctrine:query:sql --env=test "insert into status values (null, 'in progress', 3);"
php ./bin/console doctrine:query:sql --env=test "insert into status values (null, 'verification', 4);"
php ./bin/console doctrine:query:sql --env=test "insert into status values (null, 'done', 5);"
php ./bin/console doctrine:query:sql --env=test "insert into status values (null, 'skipped', 6);"

php ./bin/console doctrine:query:sql --env=test "insert into card values (null, 'missing collaboratos', 'this project have zero collaborators =(', 'in progress', 'bug');"
php ./bin/console doctrine:query:sql --env=test "insert into card values (null, 'missing devs', 'assign dev to a card', 'backlog', 'task');"
php ./bin/console doctrine:query:sql --env=test "insert into card values (null, 'missing persons', 'manage developers', 'backlog', 'task');"
php ./bin/console doctrine:query:sql --env=test "insert into card values (null, 'missing owner', 'assign person to card', 'todo', 'task');"
