#!/bin/bash

bin/console doctrine:database:drop --force
bin/console doctrine:database:create
bin/console doctrine:schema:update --force
bin/console hautelook:fixtures:load --no-interaction --no-bundles
