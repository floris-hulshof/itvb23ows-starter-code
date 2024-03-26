FROM jenkins/jenkins:lts
COPY plugins.txt /usr/share/jenkins/ref/plugins.txt
RUN  jenkins-plugin-cli -f /usr/share/jenkins/ref/plugins.txt

USER root
RUN apt-get update \
    && apt-get install -y php-cli php-xml \
    && apt-get install -y php-curl \
    && apt-get install -y php-mbstring\
    && apt-get install -y php-mysql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
ENV PATH="/usr/local/bin/composer/vendor/bin:${PATH}"

USER jenkins