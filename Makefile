### Variables

# Applications
COMPOSER ?= /usr/bin/env composer

### Helpers
all: clean depend

.PHONY: all

### Dependencies
depend:
	${COMPOSER} install --prefer-source --no-interaction

.PHONY: depend

### QA
qa: lint phpstan phpcs phpcpd

lint:
	find ./src -name "*.php" -exec /usr/bin/env php -l {} \; | grep "Parse error" > /dev/null && exit 1 || exit 0
	find ./infrastructures -name "*.php" -exec /usr/bin/env php -l {} \; | grep "Parse error" > /dev/null && exit 1 || exit 0

phploc:
	vendor/bin/phploc src
	vendor/bin/phploc infrastructures

phpstan:
	vendor/bin/phpstan analyse src infrastructures --level max

phpcs:
	vendor/bin/phpcs --standard=PSR12 --extensions=php src/
	vendor/bin/phpcs --standard=PSR12 --extensions=php infrastructures/

phpcpd:
	vendor/bin/phpcpd src/
	vendor/bin/phpcpd infrastructures/

.PHONY: qa lint phploc phpstan phpcs phpcpd

### Testing
test:
	php -dzend_extension=xdebug.so -dxdebug.coverage_enable=1 vendor/bin/phpunit -c phpunit.xml -v --colors --coverage-text

.PHONY: test

### Cleaning
clean:
	rm -rf vendor

.PHONY: clean
