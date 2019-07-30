install:
	composer install
lint:
	composer run-script phpcs -- --standard=PSR12 bin
test:
	composer run-script phpunit tests
