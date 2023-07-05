#! /bin/bash
DB_CONTAINER_ID=a97c252ff909
WORDPRESS_CONTAINER_ID=7a70038d9113
OLD_IP='http://130.162.214.241'
NEW_IP='http://141.147.36.16'

DB_USER=wordpress
DB_PWD=wordpress
DB_NAME=wordpress

ALFRESCO_IP=141.147.36.16

docker exec -ti $DB_CONTAINER_ID mysql -u${DB_USER} -p${DB_PWD} ${DB_NAME} -e "UPDATE wp_options SET option_value = replace(option_value, '${OLD_IP}', '${NEW_IP}') WHERE option_name = 'home' OR option_name = 'siteurl'"
docker exec -ti $DB_CONTAINER_ID mysql -u${DB_USER} -p${DB_PWD} ${DB_NAME} -e "UPDATE wp_posts SET guid = replace(guid, '${OLD_IP}', '${NEW_IP}')"
docker exec -ti $DB_CONTAINER_ID mysql -u${DB_USER} -p${DB_PWD} ${DB_NAME} -e "UPDATE wp_posts SET post_content = replace(post_content, '${OLD_IP}', '${NEW_IP}')"
docker exec -ti $DB_CONTAINER_ID mysql -u${DB_USER} -p${DB_PWD} ${DB_NAME} -e "UPDATE wp_postmeta SET meta_value = replace(meta_value,'${OLD_IP}', '${NEW_IP}')"
docker exec -ti $DB_CONTAINER_ID mysql -u${DB_USER} -p${DB_PWD} ${DB_NAME} -e "UPDATE wp_posts SET post_excerpt = replace(post_excerpt, '${OLD_IP}', '${NEW_IP}')"
docker exec -ti $DB_CONTAINER_ID mysql -u${DB_USER} -p${DB_PWD} ${DB_NAME} -e "a2enmod alias allowmethods autoindex deflate ext_filter filter headers include mime mpm_prefork negotiation proxy proxy_ajp proxy_balancer proxy_html proxy_http remoteip reqtimeout request rewrite sed session session_cookie setenvif slotmem_shm socache_memcache socache_shmcb ssl status substitute xml2enc"

sed -i "s/CHANGE_ALFRESCO_IP/${ALFRESCO_IP}/g" 000-default.conf 

docker cp -q 000-default.conf $WORDPRESS_CONTAINER_ID:/etc/apache2/sites-enabled/000-default.conf

docker compose restart 
