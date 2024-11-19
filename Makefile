init: docker-compose-override-init docker-down-clear docker-pull docker-build docker-up init-app
before-deploy: php-lint twig-lint rector-dry-run php-cs-dry-run php-stan psalm doctrine-schema-validate test deptrac-lint
fix-linters: rector-fix php-cs-fix
init-and-check: init before-deploy

reports: php-metrics-report

first-init: jwt-keys chmod-password-key init

asset-watch:
	yarn watch

up: docker-up
down: docker-down
init-app: env-init composer-install database-create migrations-up create-default-admin init-assets
recreate-database: database-drop database-create
update-deps: composer-update before-deploy composer-outdated

up-test-down: docker-compose-override-init docker-down-clear docker-pull docker-build docker-up env-init \
	composer-install database-create make-migration-no-interaction migrations-up create-default-admin init-assets \
	before-deploy docker-down-clear

make-migration-no-interaction:
	docker compose run --rm app-php-fpm php bin/console make:migration --no-interaction

consume-cron:
	docker compose exec app-php-fpm bin/console messenger:consume -vv scheduler_base

consume:
	docker compose exec app-php-fpm bin/console messenger:consume -vv

consume-all:
	@docker compose exec app-php-fpm bin/console messenger:consume \
	common-command-transport

jwt-keys:
	mkdir -p config/jwt
	ssh-keygen -t rsa -b 4096 -m PEM -f ./config/jwt/jwtRS256.key
	openssl rsa -in ./config/jwt/jwtRS256.key -pubout -outform PEM -out ./config/jwt/jwtRS256.key.pub

chmod-password-key:
	docker compose run --rm app-php-fpm chmod a+r config/jwt/jwtRS256.key

create-default-admin:
	docker compose run --rm app-php-fpm php bin/console app:auth:user:create-admin --email="admin@dev.com" --password="root" --name="Admin"

debug-router:
	docker compose run --rm app-php-fpm bin/console debug:router

stub-composer-operation:
	docker compose run --rm app-php-fpm composer require ...

docker-compose-override-init:
	cp docker-compose.override-example.yml docker-compose.override.yml

cache-clear:
	docker compose run --rm app-php-fpm php bin/console cache:clear
	docker compose run --rm app-php-fpm php bin/console cache:warmup
	docker compose run --rm app-php-fpm php bin/console doctrine:cache:clear-metadata
	docker compose run --rm app-php-fpm php bin/console doctrine:cache:clear-query
	docker compose run --rm app-php-fpm php bin/console doctrine:cache:clear-result

env-init:
	docker compose run --rm app-php-fpm rm -f .env.local
	docker compose run --rm app-php-fpm rm -f .env.test.local
	docker compose run --rm app-php-fpm cp .env.local.example .env.local
	docker compose run --rm app-php-fpm cp .env.test.local.example .env.test.local

fixtures:
	docker compose run --rm app-php-fpm php bin/console doctrine:fixtures:load --no-interaction
	docker compose run --rm app-php-fpm php bin/console doctrine:fixtures:load --no-interaction --env=test

make-migration:
	docker compose run --rm app-php-fpm php bin/console make:migration

migrations-next:
	docker compose run --rm app-php-fpm php bin/console doctrine:migrations:migrate next -n
	docker compose run --rm app-php-fpm php bin/console --env=test doctrine:migrations:migrate next -n

migrations-prev:
	docker compose run --rm app-php-fpm php bin/console doctrine:migrations:migrate prev -n
	docker compose run --rm app-php-fpm php bin/console --env=test doctrine:migrations:migrate prev -n

migrations-up:
	docker compose run --rm app-php-fpm php bin/console doctrine:migrations:migrate --no-interaction
	docker compose run --rm app-php-fpm php bin/console doctrine:migrations:migrate --no-interaction --env=test

migrations-down:
	docker compose run --rm app-php-fpm php bin/console doctrine:migrations:migrate prev --no-interaction
	docker compose run --rm app-php-fpm php bin/console doctrine:migrations:migrate prev --no-interaction --env=test

database-create:
	docker compose run --rm app-php-fpm php bin/console doctrine:database:create --no-interaction --if-not-exists
	docker compose run --rm app-php-fpm php bin/console doctrine:database:create --no-interaction --env=test --if-not-exists

database-drop:
	docker compose run --rm app-php-fpm php bin/console doctrine:database:drop --force --no-interaction --if-exists
	docker compose run --rm app-php-fpm php bin/console doctrine:database:drop --force --no-interaction --env=test --if-exists

test:
	docker compose run --rm app-php-fpm ./vendor/bin/phpunit

test-coverage:
	docker compose run --rm app-php-fpm ./vendor/bin/phpunit --coverage-clover var/clover.xml --coverage-html var/coverage

test-unit-coverage:
	docker compose run --rm app-php-fpm ./vendor/bin/phpunit --testsuite=unit --coverage-clover var/clover.xml --coverage-html var/coverage

test-integration-coverage:
	docker compose run --rm app-php-fpm ./vendor/bin/phpunit --testsuite=integration --coverage-clover var/clover.xml --coverage-html var/coverage

test-functional-coverage:
	docker compose run --rm app-php-fpm ./vendor/bin/phpunit --testsuite=functional --coverage-clover var/clover.xml --coverage-html var/coverage

test-unit:
	docker compose run --rm app-php-fpm ./vendor/bin/phpunit --testsuite=unit

test-functional:
	docker compose run --rm app-php-fpm ./vendor/bin/phpunit --testsuite=functional

test-integration:
	docker compose run --rm app-php-fpm ./vendor/bin/phpunit --testsuite=integration

test-acceptance:
	docker compose run --rm app-php-fpm ./vendor/bin/phpunit --testsuite=acceptance

php-stan:
	docker compose run --rm app-php-fpm ./vendor/bin/phpstan --memory-limit=-1

twig-lint:
	docker compose run --rm app-php-fpm php bin/console lint:twig templates src --show-deprecations

deptrac-lint:
	docker compose run --rm app-php-fpm ./vendor/bin/deptrac analyse --config-file="deptrac-modules.yaml"
	docker compose run --rm app-php-fpm ./vendor/bin/deptrac analyse --config-file="deptrac-layers.yaml"

php-lint:
	docker compose run --rm app-php-fpm ./vendor/bin/phplint

rector-dry-run:
	docker compose run --rm app-php-fpm ./vendor/bin/rector --dry-run

rector-fix:
	docker compose run --rm app-php-fpm ./vendor/bin/rector

php-cs-fix:
	docker compose run --rm app-php-fpm ./vendor/bin/php-cs-fixer fix -v --using-cache=no

php-cs-dry-run:
	docker compose run --rm app-php-fpm ./vendor/bin/php-cs-fixer fix --dry-run --diff --using-cache=no

psalm:
	docker compose run --rm app-php-fpm ./vendor/bin/psalm --no-cache $(ARGS)

doctrine-schema-validate:
	docker compose run --rm app-php-fpm php bin/console --env=test doctrine:schema:validate

php-metrics-report:
	docker compose run --rm app-php-fpm ./vendor/bin/phpmetrics --report-html="var/report/phpmetrics" ./src

composer-install:
	docker compose run --rm app-php-fpm composer install

composer-audit:
	docker compose run --rm app-php-fpm composer audit

composer-dump:
	docker compose run --rm app-php-fpm composer dump-autoload

composer-update:
	docker compose run --rm app-php-fpm composer update

composer-outdated:
	docker compose run --rm app-php-fpm composer outdated

composer-dry-run:
	docker compose run --rm app-php-fpm composer update --dry-run

yarn-install:
	docker compose run node sh -c "yarn install"

yarn-upgrade:
	docker compose run node sh -c "yarn upgrade"

docker-up:
	docker compose up -d

docker-rebuild:
	docker compose down -v --remove-orphans
	docker compose up -d --build

docker-down:
	docker compose down --remove-orphans

docker-down-clear:
	docker compose down -v --remove-orphans

docker-pull:
	docker compose pull

docker-build:
	docker compose build

phpmetrics:
	docker compose run --rm app-php-fpm php ./vendor/bin/phpmetrics --report-html=var/myreport ./src

init-assets:
	docker compose run node sh -c "yarn"
	docker compose run node sh -c "yarn encore dev"

test-ci:
	docker compose -f docker-compose-test.yml pull
	docker compose -f docker-compose-test.yml up --build -d
	docker compose run --rm app-php-fpm rm -f .env.test.local
	docker compose run --rm app-php-fpm cp .env.test.local.example .env.test.local
	docker compose run --rm app-php-fpm composer install
	docker compose run --rm app-php-fpm php bin/console assets:install
	docker compose run --rm app-php-fpm sh -c "yarn"
	docker compose run --rm app-php-fpm sh -c "yarn encore prod"
	make database-create
	make migrations-up
	make make-migration-no-interaction
	make migrations-up
	make before-deploy
