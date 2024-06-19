FROM nginx:alpine

ENV ACCEPT_EULA=Y

ARG PHP_CONTAINER_HOST="nerd-time-php"
ARG PHP_CONTAINER_PORT=9000
ARG SUPERVISOR_GUI_PORT=9001

RUN apk add --no-cache sed

COPY environment/nginx/rootfilesystem/ /

EXPOSE 80
EXPOSE 443

RUN sed -i "s/{PHP_CONTAINER_HOST}/$PHP_CONTAINER_HOST/" /etc/nginx/conf.d/internal.conf \
 && sed -i "s/{PHP_CONTAINER_PORT}/$PHP_CONTAINER_PORT/" /etc/nginx/conf.d/internal.conf

RUN sed -i "s/{PHP_CONTAINER_HOST}/$PHP_CONTAINER_HOST/" /etc/nginx/conf.d/supervisor.conf \
 && sed -i "s/{SUPERVISOR_GUI_PORT}/$SUPERVISOR_GUI_PORT/" /etc/nginx/conf.d/supervisor.conf \
 && sed -i "s/{SERVER_NAME}/$SERVER_NAME/" /etc/nginx/conf.d/supervisor.conf
