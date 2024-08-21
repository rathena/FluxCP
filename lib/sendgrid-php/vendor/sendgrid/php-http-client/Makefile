.PHONY: clean install test

clean:
	@rm -rf vendor composer.lock php-http-client.zip

ci-install: clean
	composer install --no-dev

install: clean
	composer install --no-scripts --no-progress --no-interaction

test: install
	vendor/bin/phpunit test/unit

bundle: ci-install
	zip -r php-http-client.zip . -x \*.git\* \*composer.json\* \*test\*
