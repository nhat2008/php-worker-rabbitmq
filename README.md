#  Worker for Rabbitmq
1. install rabbittmq
   - Ref: https://www.rabbitmq.com/install-debian.html
   - or Docker: $ docker run -d --hostname my-rabbit --name some-rabbit -e RABBITMQ_ERLANG_COOKIE='secret cookie here' rabbitmq:3
2. config the config.json in miki-notification/config/config.json
3. install composer.phar to install php package: curl -s http://getcomposer.org/installer | php
4. install package in composer.json php composer.phar install
5. Run by: php app.php &

# Example Publisher for Worker 
1. check in folder publisher