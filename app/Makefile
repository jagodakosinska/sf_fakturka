SHELL := /bin/bash
  
tests: export APP_ENV=test

tests:
	make clean_db
	symfony php bin/phpunit $@

clean_db:
	symfony console doctrine:database:drop --force || true
	symfony console doctrine:database:create
	symfony console doctrine:migrations:migrate -n
	symfony console doctrine:fixtures:load -n

tests-group: export APP_ENV=test
tests-group:
	make clean_db
	symfony php bin/phpunit --group $(filter-out $@,$(MAKECMDGOALS))

.PHONY: tests clean_db tests-group