#!/bin/sh
set -e

export COMPOSER_CACHE_DIR=${COMPOSER_CACHE_DIR:-/tmp/composer-cache}

echo "Installing PHP dependencies..."
composer install --no-interaction --optimize-autoloader

echo "Fixing permissions on var/..."
chown -R www-data:www-data var
chmod -R 775 var

echo "Clearing Symfony cache..."
su-exec www-data php bin/console cache:clear

# JWT
JWT_PRIVATE_KEY_PATH="/var/www/html/config/jwt/private.pem"
JWT_CONFIG_DIR="/var/www/html/config/jwt"

if [ ! -f "$JWT_PRIVATE_KEY_PATH" ]; then
  echo "JWT private key not found. Generating new key pair..."
  mkdir -p "$JWT_CONFIG_DIR"
  chown www-data:www-data "$JWT_CONFIG_DIR"
  su-exec www-data php bin/console lexik:jwt:generate-keypair
  echo "JWT key pair generated."
else
  echo "JWT private key found. Skipping generation."
fi

# Применение миграций Doctrine
echo "Applying database migrations..."
su-exec www-data php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
echo "Database migrations applied."

exec "$@"
