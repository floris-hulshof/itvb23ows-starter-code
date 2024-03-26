FROM jenkins/jenkins:2.437-jdk17

USER root
RUN apt-get update && apt-get install -y docker-compose=1.29.2-3 && rm -rf /var/lib/apt/lists/*
RUN usermod -aG docker jenkins
USER jenkins
