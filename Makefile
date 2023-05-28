include .env

up: -docker-up
down: -docker-down
restart: -docker-down -docker-up
fpm-restart:
	@docker-compose stop php-fpm
	@docker-compose up -d php-fpm
cli:
	@docker-compose run --rm -e "XDEBUG=$(XDEBUG_CLI_ENABLED)" php-cli genesis-entrypoint bash
init: -docker-down-clear \
	-docker-pre-permission \
	-clean \
	-docker-build \
	-docker-up \
	-genesis-init \
	-docker-post-permission
ci: -genesis-composer-install

-clean:
	sudo rm -rf data/

-docker-up: -ensureRequirements
	@docker-compose up -d

-docker-down:
	@docker-compose down

-docker-down-clear:
	@docker-compose down -v --remove-orphans

-docker-build: -ensureRequirements
	@docker-compose build --force

-docker-pre-permission:
	sudo chown ${USER_ID}:${GROUP_ID} -R src/

-docker-post-permission:
	sudo chown ${USER_ID}:${GROUP_ID} -R src/
	sudo chmod 0777 -R data

-ensureRequirements:
	@if [ ! -f .env ]; then echo ".env is missing!"; exit 2; fi
	@if [ ! -f ./docker-compose.override.yml ]; then echo "docker-compose.override.yml is missing!"; exit 2; fi

-genesis-init: -genesis-composer-install

-genesis-composer-install:
	@docker-compose run --rm php-cli composer install --no-scripts
