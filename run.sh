docker run \
--rm \
-it \
-p 8080:80 \
-v $PWD/data:/var/www/data \
-v $PWD/LocalSettings.php:/var/www/html/LocalSettings.php \
-v $PWD/Sketchfab:/var/www/html/extensions/Sketchfab \
--name devwiki \
mediawiki