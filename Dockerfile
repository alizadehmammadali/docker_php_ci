FROM prosol/docker-lamp
RUN apt-get install -y sudo
COPY .  /var/www/html/
WORKDIR /var/www/html/
RUN chown -R www-data:www-data .

#USE docker_host(which is added to bitbucket or github.com) SSH_PRIVATE_KEY which is send by when building container user (docker-compose build --build-arg SSH_PRIVATE_KEY="$(cat ~/.ssh/id_rsa)")

ARG SSH_PRIVATE_KEY
RUN mkdir /root/.ssh/
RUN echo "${SSH_PRIVATE_KEY}" >> /root/.ssh/id_rsa && chmod 600 /root/.ssh/id_rsa
RUN touch /root/.ssh/known_hosts
RUN ssh-keyscan bitbucket.org >> /root/.ssh/known_hosts

#ADD www-data user to sudoers to use commands with sudo without password
RUN echo "www-data ALL=(ALL) NOPASSWD: ALL" >> /etc/sudoers

EXPOSE 9999

