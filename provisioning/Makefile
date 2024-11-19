ifneq ("$(wildcard .env)","")
  include .env
else
  include .env.local
endif

app:
	ansible-playbook -i hosts.yml app.yml

jwt:
	ansible-playbook -i hosts.yml jwt.yml

upgrade:
	ansible-playbook -i hosts.yml upgrade.yml

generate-deploy-key:
	ssh-keygen -q -t rsa -N '' -f files/deploy_rsa

authorize-deploy:
	ansible-playbook -i hosts.yml authorize-deploy.yml

authorize:
	ansible-playbook -i hosts.yml authorize.yml

jobs:
	ansible-playbook -i hosts.yml jobs.yml

docker-login:
	ansible-playbook -i hosts.yml docker-login.yml

ping-host:
	ansible all -i hosts.yml -u ${DEPLOY_USERNAME} -m ping

check-nginx:
	ansible all -i hosts.yml -u ${DEPLOY_USERNAME} -m command -a 'systemctl status nginx'
