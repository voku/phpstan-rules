# Agent-loop workflow tooling.
#
# `voku/agent-loop` lives in an isolated Composer project below tools/agent-loop
# so that this library keeps its PHP 7.4 support in the root composer.json.
AGENT_LOOP_BIN := php tools/agent-loop/vendor/bin/agent-loop

-include tools/agent-loop/vendor/voku/agent-loop/make/agent-loop.mk

.PHONY: agent_loop_install ## install the isolated agent-loop tool project
agent_loop_install:
	composer install --working-dir=tools/agent-loop

.PHONY: test ## run the PHPUnit test-suite
test:
	php vendor/bin/phpunit -c phpunit.xml

.PHONY: phpstan ## run PHPStan on this repository
phpstan:
	php vendor/bin/phpstan analyse
