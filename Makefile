### Variables

# Applications
COMPOSER ?= /usr/bin/env composer

### Helpers
all: clean depend

.PHONY: all

### Dependencies
depend:
	${COMPOSER} install --prefer-source --no-interaction --ignore-platform-reqs

.PHONY: depend

### QA
qa: lint phpstan phpcs phpcpd

lint:
	find ./src -name "*.php" -exec /usr/bin/env php -l {} \; | grep "Parse error" > /dev/null && exit 1 || exit 0
	find ./package -name "*.php" -exec /usr/bin/env php -l {} \; | grep "Parse error" > /dev/null && exit 1 || exit 0

phploc:
	vendor/bin/phploc src
	vendor/bin/phploc package

phpstan:
	vendor/bin/phpstan analyse src package --level max

phpcs:
	vendor/bin/phpcs --standard=PSR12 --extensions=php src/
	vendor/bin/phpcs --standard=PSR12 --extensions=php package/

phpcpd:
	vendor/bin/phpcpd src/
	vendor/bin/phpcpd package/

.PHONY: qa lint phploc phpstan phpcs phpcpd

### Testing
test:
	php -dxdebug.coverage_enable=1 vendor/bin/phpunit -c phpunit.xml -v --colors --coverage-text

.PHONY: test

### Cleaning
clean:
	rm -rf vendor

.PHONY: clean
