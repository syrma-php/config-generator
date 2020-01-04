THIS_MAKEFILE_PATH = $(word 1,$(MAKEFILE_LIST))
PATH_ROOT      = $(shell cd $(dir $(THIS_MAKEFILE_PATH));pwd)
PATH_VENDOR    = $(PATH_ROOT)/vendor
PATH_OAM       = $(PATH_ROOT)/oam

CMD_PHP = /usr/bin/env php
CMD_DOCKER = /usr/bin/env docker
CMD_COMPOSER = /usr/bin/env composer
CMD_BOX = /usr/bin/env box

UID = $(shell id -u )

DOCKER_PREFIX = syrma-config-generator-test
DOCKER_BUILD_FN = cd $(PATH_OAM) && $(CMD_DOCKER) build --force-rm -t "$(DOCKER_PREFIX)-$1" -f "$1.docker" .
DOCKER_TEST_FN = $(CMD_DOCKER) run --rm -u $(UID) -v $(PATH_ROOT):/srv "$(DOCKER_PREFIX)-$1"  make test-with-composer
DOCKER_BOX_FN = $(CMD_DOCKER) run --rm -u $(UID) -v $(PATH_ROOT):/srv "$(DOCKER_PREFIX)-$1"  make box-plain

test:
	$(CMD_PHP) $(PATH_VENDOR)/bin/phpunit --verbose

test-ct:
	$(CMD_PHP) $(PATH_VENDOR)/bin/phpunit --verbose  --coverage-text

test-with-composer: composer test

test-php71:
	$(call DOCKER_BUILD_FN,php71)
	$(call DOCKER_TEST_FN,php71)

test-php72:
	$(call DOCKER_BUILD_FN,php72)
	$(call DOCKER_TEST_FN,php72)

test-php73:
	$(call DOCKER_BUILD_FN,php73)
	$(call DOCKER_TEST_FN,php73)

test-php74:
	$(call DOCKER_BUILD_FN,php74)
	$(call DOCKER_TEST_FN,php74)

cs-fix:
	$(CMD_PHP) $(PATH_VENDOR)/bin/php-cs-fixer fix --verbose

box:
	$(call DOCKER_BUILD_FN,box)
	$(call DOCKER_BOX_FN,box)

box-plain:
	$(CMD_COMPOSER) up --no-dev
	$(CMD_BOX) compile -vvv



composer:
	composer up