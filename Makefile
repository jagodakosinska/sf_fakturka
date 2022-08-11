SHELL := /bin/bash
  
server-start:
	docker-compose exec php8-service symfony server:start -d --port=8100

server-stop:
	docker-compose exec php8-service symfony server:stop

server-restart:
	server-stop server-start

server-log:
	docker-compose exec php8-service symfony server:log

error-log:
	make server-log | grep -E "ERROR|CRIT"

yarn-watch:
	docker-compose run --rm node-service yarn watch
	
symfony-tests:
	docker-compose exec -u root php8-service make tests

tests-group:
	docker-compose exec -u root php8-service make tests-group $(filter-out $@,$(MAKECMDGOALS))

symfony-clean_db:
	docker-compose exec -u root php8-service make clean_db

schema-validate:
	docker-compose exec -u root php8-service symfony console doctrine:schema:validate

.PHONY: server-start server-stop server-log error-log yarn-wacht symfony-tests symfony-clean_db schema-validate tests-group