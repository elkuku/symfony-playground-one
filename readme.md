# Symfony Playground "one"

[![Build Status](https://www.travis-ci.com/elkuku/symfony-playground-one.svg?branch=master)](https://www.travis-ci.com/elkuku/symfony-playground-one)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/elkuku/symfony-playground-one/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/elkuku/symfony-playground-one/?branch=master)

![screen-playground-one](https://user-images.githubusercontent.com/33978/103650387-edff8280-4f2d-11eb-84c8-486662e25bd5.png)

## What's this??

* Symfony 5.*
* Docker compose file for PostgreSQL
* `dev` login form <br/> `prod` Social login with Google or GitHub (and [more](https://github.com/knpuniversity/oauth2-client-bundle#step-1-download-the-client-library))
* JQuery, Bootstrap and [Bootswatch](https://bootswatch.com/)
* Prepared for Heroku
* Likes PHP 8 ;)

## Installation

Clone the repo then use the `bin/install` command **OR** execute the following steps manually:

----

1. `symfony composer install`
1. `yarn`
1. `yarn dev`
1. `bin/start` - a custom startup script that runs `docker-compose up`, `symony server:start` and `symfony open:local`
1. `symfony console doctrine:migrations:migrate`
   
----

Use `symfony console user-admin` to create an admin user.

## Usage

Use the `bin/start` and `bin/stop` scripts to start and stop the environment.

## Testing

```shell
make tests
```

----

Happy coding `=;)`
