# turbo_test

Welcome in your turbo_test.

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/55e0ecb09cac4fb786b220d2413af6cc)](https://www.codacy.com/gh/thomasop/mercure_test/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=thomasop/mercure_test&amp;utm_campaign=Badge_Grade)

[![Maintainability](https://api.codeclimate.com/v1/badges/55ad048d2174693cd3d7/maintainability)](https://codeclimate.com/github/thomasop/mercure_test/maintainability)

[![Build Status](https://app.travis-ci.com/thomasop/mercure_test.svg?branch=master)](https://app.travis-ci.com/thomasop/mercure_test)

## How to install the project

### Prerequisite
PHP 8

Download Wamp, Xampp, Mamp or WebHost

Symfony 5.4

Composer

### Clone
Go in directory.
Make a clone with git clone https://github.com/thomasop/turbo_test.git

### Configuration
Update environnements variables in the .env file with your values.
At the very least you need to define the SYMFONY_ENV=prod
MAILER_URL
MERCURE_JWT_SECRET => set token https://jwt.io
MESSENGER_TRANSPORT_DSN => doctrine://default
MERCURE_PUBLIC_URL => http://localhost:3000/.well-known/mercure
MERCURE_URL => http://localhost:3000/.well-known/mercure

### Composer
Install composer with composer install and init the projet with composer init in turbo_test

### Start the project
At the root of your project use the command php bin/console server:start -d

### Test
For run test: make tests 

### Mercure config
Documentation : https://mercure.rocks/docs/hub/install
https://caddyserver.com for Caddyfile
Download archive https://github.com/dunglas/mercure/releases and put this on mercure folder on root of the project
and for mac run :
MERCURE_PUBLISHER_JWT_KEY='!ChangeMe!' \
MERCURE_SUBSCRIBER_JWT_KEY='!ChangeMe!' \
./mercure run -config Caddyfile.dev

or windows :
$env:MERCURE_PUBLISHER_JWT_KEY='!ChangeMe!'; $env:MERCURE_SUBSCRIBER_JWT_KEY='!ChangeMe!'; .\mercure.exe run -config Caddyfile.dev
