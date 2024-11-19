ifneq ("$(wildcard .env)","")
  include .env
endif

build-push-deploy: build push deploy
build: production-build
push: production-push
deploy: deploy-app

production-build:
	docker --log-level=debug build --pull --file=../docker/production/php-fpm/Dockerfile --tag=${REGISTRY}/${NAMESPACE}/${IMAGE_NAME_PHP_FPM}:${BUILD_NUMBER} ../
	docker create --name temp_build_php_fpm ${REGISTRY}/${NAMESPACE}/${IMAGE_NAME_PHP_FPM}:${BUILD_NUMBER}
	docker cp temp_build_php_fpm:/app/public ./../
	docker rm temp_build_php_fpm
	docker --log-level=debug build --pull --file=../docker/production/nginx/Dockerfile --tag=${REGISTRY}/${NAMESPACE}/${IMAGE_NAME_NGINX}:${BUILD_NUMBER} ../
	docker --log-level=debug build --pull --file=../docker/common/metrics/prometheus/Dockerfile --tag=${REGISTRY}/${NAMESPACE}/${IMAGE_NAME_PROMETHEUS}:${BUILD_NUMBER} ../docker/common/metrics/prometheus

production-push:
	docker login --username=${NAMESPACE} ${REGISTRY} --password=${PASSWORD}
	docker push ${REGISTRY}/${NAMESPACE}/${IMAGE_NAME_NGINX}:${BUILD_NUMBER}
	docker push ${REGISTRY}/${NAMESPACE}/${IMAGE_NAME_PHP_FPM}:${BUILD_NUMBER}
	docker push ${REGISTRY}/${NAMESPACE}/${IMAGE_NAME_PROMETHEUS}:${BUILD_NUMBER}

deploy-app:
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'docker login --username=${NAMESPACE} ${REGISTRY} --password=${PASSWORD}'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'rm -rf app_${BUILD_NUMBER}'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'mkdir app_${BUILD_NUMBER}'
	scp -o StrictHostKeyChecking=no -P ${PORT} ../docker-compose-production.yml deploy@${HOST}:app_${BUILD_NUMBER}/docker-compose-production.yml
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd app_${BUILD_NUMBER} && echo "COMPOSE_PROJECT_NAME=app" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd app_${BUILD_NUMBER} && echo "REGISTRY=${REGISTRY}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd app_${BUILD_NUMBER} && echo "NAMESPACE=${NAMESPACE}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd app_${BUILD_NUMBER} && echo "IMAGE_TAG=${BUILD_NUMBER}" >> .env'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd app_${BUILD_NUMBER} && docker compose -f docker-compose-production.yml pull'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd app_${BUILD_NUMBER} && docker compose -f docker-compose-production.yml up --build -d --remove-orphans'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'rm -f app'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'ln -sr app_${BUILD_NUMBER} app'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd app && docker compose -f docker-compose-production.yml run --rm app-php-fpm php bin/console doctrine:database:create --no-interaction --if-not-exists'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd app && docker compose -f docker-compose-production.yml run --rm app-php-fpm php bin/console doctrine:migrations:migrate --no-interaction'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd app && docker compose -f docker-compose-production.yml run --rm app-php-fpm php bin/console --env=prod cache:clear'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd app && docker compose -f docker-compose-production.yml run --rm app-php-fpm php bin/console --env=prod cache:warmup'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd app && docker compose -f docker-compose-production.yml run --rm app-php-fpm php bin/console --env=prod doctrine:cache:clear-metadata'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd app && docker compose -f docker-compose-production.yml run --rm app-php-fpm php bin/console --env=prod doctrine:cache:clear-query'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd app && docker compose -f docker-compose-production.yml run --rm app-php-fpm php bin/console --env=prod doctrine:cache:clear-result'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd app && docker compose -f docker-compose-production.yml run --rm app-php-fpm chmod a+r config/jwt/jwtRS256.key'
	ssh -o StrictHostKeyChecking=no root@${HOST} -p ${PORT} 'service nginx restart'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'docker image prune -a -f'
	ssh -o StrictHostKeyChecking=no root@${HOST} -p ${PORT} 'docker ps'

recreate-db:
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd app && docker compose -f docker-compose-production.yml run --rm app-php-fpm php bin/console doctrine:database:drop --force --no-interaction --if-exists'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd app && docker compose -f docker-compose-production.yml run --rm app-php-fpm php bin/console doctrine:database:create --no-interaction --if-not-exists'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd app && docker compose -f docker-compose-production.yml run --rm app-php-fpm php bin/console doctrine:migrations:migrate --no-interaction'

create-user-ssh:
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd app && docker compose -f docker-compose-production.yml run --rm app-php-fpm php bin/console app:auth:user:create-admin --email="${ADMIN_EMAIL}" --password="${ADMIN_PASSWORD}" --name="Admin"'

clear-unused-images:
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'docker image prune -a -f'

cache-clear-prod:
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd app && docker compose -f docker-compose-production.yml run --rm app-php-fpm php bin/console --env=prod cache:clear'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd app && docker compose -f docker-compose-production.yml run --rm app-php-fpm php bin/console --env=prod cache:warmup'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd app && docker compose -f docker-compose-production.yml run --rm app-php-fpm php bin/console --env=prod doctrine:cache:clear-metadata'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd app && docker compose -f docker-compose-production.yml run --rm app-php-fpm php bin/console --env=prod doctrine:cache:clear-query'
	ssh -o StrictHostKeyChecking=no deploy@${HOST} -p ${PORT} 'cd app && docker compose -f docker-compose-production.yml run --rm app-php-fpm php bin/console --env=prod doctrine:cache:clear-result'

test-ci:
	docker compose -f docker-compose-test.yml pull
	docker compose -f docker-compose-test.yml up --build -d
	docker compose run --rm app-php-fpm rm -f .env.test.local
	docker compose run --rm app-php-fpm cp .env.test.local.example .env.test.local
	docker compose run --rm app-php-fpm composer install
	docker compose run --rm app-php-fpm sh -c "yarn"
	docker compose run --rm app-php-fpm sh -c "yarn encore dev"
	docker compose run --rm app-php-fpm php bin/console doctrine:database:create --no-interaction --env=test --if-not-exists
	docker compose run --rm app-php-fpm php bin/console doctrine:migrations:migrate --no-interaction --env=test
	make -f Makefile before-deploy
