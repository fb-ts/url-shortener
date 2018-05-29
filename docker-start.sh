#!/bin/sh

# docker-machine create machine-url-shortener

docker-machine start machine-url-shortener
eval $(docker-machine env machine-url-shortener)
docker-compose up -d

# docker exec -it --user www-data url-shortener_php_1 /bin/bash
