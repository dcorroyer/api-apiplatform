up:
	docker-compose up -d

down:
	docker-compose down

build:
	docker-compose build

rebuild:
	docker-compose build --no-cache

back:
	docker-compose exec php8-service sh

symfonycreate:
	docker-compose run --rm php8-service composer create-project symfony/website-skeleton .

symfonyrun:
	docker-compose run --rm php8-service symfony serve

composer:
	docker-compose run --rm php8-service composer install

dbcreate:
	docker-compose run --rm php8-service php bin/console d:d:c

dbupdate:
	docker-compose run --rm php8-service php bin/console d:s:u --force

dbrebuild:
	docker-compose run --rm php8-service php bin/console d:d:d --force
	docker-compose run --rm php8-service php bin/console d:d:c
	docker-compose run --rm php8-service php bin/console d:s:u --force

dbfixtures:
	docker-compose run --rm php8-service php bin/console d:f:l

yarninstall:
	docker-compose run --rm node-service yarn install

yarnbuild:
	docker-compose run --rm node-service yarn build

yarnwatch:
	docker-compose run --rm node-service yarn encore dev --watch
