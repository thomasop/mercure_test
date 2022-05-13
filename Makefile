SHELL := /bin/bash

.PHONY: tests cache controller form curl start stop user entity migration password subscriber gitinit gitmaster crud

symfony:
	symfony check:requirements
	symfony check:security

tests: export APP_ENV=test
tests:
	symfony console doctrine:database:drop --force || true
	symfony console doctrine:database:create
	symfony console doctrine:schema:create
	symfony console doctrine:fixtures:load -n
	symfony php bin/phpunit $@

cache:
	php bin/console cache:clear

curl:
	curl -s -I -X GET http://127.0.0.1:8000/

start:
	symfony server:start -d
	symfony open:local

stop:
	symfony server:stop

form:
	php bin/console make:form

crud:
	php bin/console make:crud

controller:
	php bin/console make:controller

user:
	php bin/console make:user

entity:
	php bin/console make:entity

migration:
	php bin/console make:migration
	php bin/console doctrine:migrations:migrate -n

password:
	php bin/console security:encode-password

auth:
	php bin/console make:auth

subscriber:
	php bin/console make:subscriber

gitinit:
	git init
	git add .
	git commit -m "Initial commit"

gitmaster:
	git push origin dev
	git checkout master
	git merge dev
	git push origin master

fix:
	/Users/thomasdasilva/.composer/vendor/bin/php-cs-fixer fix src/ --rules=@Symfony,@PSR12
	/Users/thomasdasilva/.composer/vendor/bin/php-cs-fixer fix src/
.PHONY: fix