#!/bin/sh
set -e

if [ ! -f vendor/autoload.php ]; then
    echo "vendor/ absent — lancement de composer install..."
    composer install --no-interaction --prefer-dist
elif [ composer.json -nt vendor/autoload.php ]; then
    echo "composer.json modifié — mise à jour des dépendances..."
    composer install --no-interaction --prefer-dist
fi

exec "$@"
