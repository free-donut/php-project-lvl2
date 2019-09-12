install:
	composer install
lint:
	composer run-script phpcs -- --standard=PSR12 bin src tests
test:
	composer run-script phpunit tests