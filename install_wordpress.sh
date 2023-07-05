#! /bin/bash
sudo mkdir -p /opt/wordpress
sudo cp -r db web /opt/wordpress/
sudo chmod 777 -R /opt/wordpress
docker compose up -d
