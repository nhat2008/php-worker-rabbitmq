FROM debian:wheezy

MAINTAINER nhat.nguyen <nhat.nguyen@tiki.vn>

# Install All-things for PHP
RUN apt-get update -q && \
    apt-get install wget curl -yq && \
    echo "deb http://packages.dotdeb.org wheezy all" >> /etc/apt/sources.list && \
    echo "deb-src http://packages.dotdeb.org wheezy all" >> /etc/apt/sources.list && \
    echo "deb http://packages.dotdeb.org wheezy-php56 all" >> /etc/apt/sources.list && \
    echo "deb-src http://packages.dotdeb.org wheezy-php56 all" >> /etc/apt/sources.list && \
    wget http://www.dotdeb.org/dotdeb.gpg && \
    apt-key add dotdeb.gpg && \
    apt-get update -q && \
    apt-get install -yq php5 && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Supervisord
RUN apt-get install -y supervisor

#COPY run.sh /opt/
#RUN chmod +x /opt/run.sh

## Start Supervisord ##
ADD 	./start.sh /start.sh
RUN 	chmod 755 /start.sh

## Default docker process ##
CMD 	["/bin/bash", "/start.sh"]
RUN echo "Success"