#!/bin/bash

rm -rf var/cache/test/*

printf "Resetting database..."
bin/console d:d:d --force --if-exists --env=test --quiet
bin/console d:d:c --env=test --quiet
bin/console d:s:c --env=test --quiet
bin/console hautelook:fixtures:load --no-interaction --env=test --quiet
printf " Ok.\n"

bin/phpunit
