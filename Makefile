THIS_MAKEFILE_PATH = $(word 1,$(MAKEFILE_LIST))
PATH_ROOT      = $(shell cd $(dir $(THIS_MAKEFILE_PATH));pwd)
PATH_VENDOR    = $(PATH_ROOT)/vendor

CMD_PHP = /usr/bin/env php

test:
	$(CMD_PHP) $(PATH_VENDOR)/bin/phpunit --verbose

test-ct:
	$(CMD_PHP) $(PATH_VENDOR)/bin/phpunit --verbose  --coverage-text

cs-fix:
	$(CMD_PHP) $(PATH_VENDOR)/bin/php-cs-fixer fix --verbose