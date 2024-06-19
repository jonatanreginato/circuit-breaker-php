#!/bin/bash

echo ""
echo "$(date '+%Y-%m-%d %H:%M:%S,%3N') INFO Starting development environment..."

CONTAINER_BASENAME="nerd-time"
RUNNING_CONTAINER=$(docker ps --filter name=${CONTAINER_BASENAME} -q)

if [[ -n $RUNNING_CONTAINER ]]; then
    echo "$(date '+%Y-%m-%d %H:%M:%S,%3N') WARNING Containers already running."
    for c in $RUNNING_CONTAINER; do
        docker stop "$c" && docker rm "$c"
    done
    echo "$(date '+%Y-%m-%d %H:%M:%S,%3N') INFO Applied docker stop command."
fi

# Setup bin (folder permission)
chmod -R +x ./bin
echo "$(date '+%Y-%m-%d %H:%M:%S,%3N') INFO Changed ./bin folder permission."

# Setup environment (Folder permission)
chmod -R 777 ./environment
echo "$(date '+%Y-%m-%d %H:%M:%S,%3N') INFO Changed ./environment folder permission."

rm -Rf ./environment/nginx/log
mkdir -m 777 ./environment/nginx/log

rm -Rf ./environment/php/log
mkdir -m 777 ./environment/php/log
mkdir -m 777 ./environment/php/log/php
mkdir -m 777 ./environment/php/log/supervisor

rm -Rf ./environment/redis/log
mkdir -m 777 ./environment/redis/log

echo "$(date '+%Y-%m-%d %H:%M:%S,%3N') INFO Cleaned logs."

# Docker up
echo "$(date '+%Y-%m-%d %H:%M:%S,%3N') INFO Loading environment."
docker compose up --build

# Docker down
echo ""
echo "$(date '+%Y-%m-%d %H:%M:%S,%3N') That's all, folks..."
echo "$(date '+%Y-%m-%d %H:%M:%S,%3N') Goodbye!"
echo "$(date '+%Y-%m-%d %H:%M:%S,%3N') ;)"
echo ""
