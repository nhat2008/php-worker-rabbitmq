#  Worker for Rabbitmq
1. install rabbittmq
   - Ref: https://www.rabbitmq.com/install-debian.html
   - or Docker: $ docker run -d --hostname my-rabbit --name some-rabbit -e RABBITMQ_ERLANG_COOKIE='secret cookie here' rabbitmq:3
2. config 
    2.1 the config.json in php-worker-rabbitmq/source/config/config.json if don't use the deploy
    2.2 Using the ansible for deploying: config the ENV in group_vars/main.yml or roles/worker/vars/main.yml
3. Run
    3.1 php app.php
    3.2 Using ansible for deployingansi [we will control and monitoring the workers with supervisord]
        3.2.1 install ansible , ansible-playbook
        3.2.2 ansible-playbook -i hosts worker.yml
