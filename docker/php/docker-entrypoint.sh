#!/bin/sh
set -e

READY_FILE="/var/www/html/.ready"
rm -f "$READY_FILE"
TOTAL=5
STEP=1

export COMPOSER_CACHE_DIR=${COMPOSER_CACHE_DIR:-/tmp/composer-cache}

echo "[$STEP/$TOTAL] Installing PHP dependencies..."
composer install --no-interaction --optimize-autoloader
STEP=$((STEP + 1))

echo "[$STEP/$TOTAL] Fixing permissions on var/..."
chown -R www-data:www-data var
chmod -R 775 var
STEP=$((STEP + 1))

echo "[$STEP/$TOTAL] Clearing Symfony cache..."
su-exec www-data php bin/console cache:clear
STEP=$((STEP + 1))

echo "[$STEP/$TOTAL] Checking JWT key pair..."
JWT_PRIVATE_KEY_PATH="/var/www/html/config/jwt/private.pem"
JWT_CONFIG_DIR="/var/www/html/config/jwt"

if [ ! -f "$JWT_PRIVATE_KEY_PATH" ]; then
  echo "JWT key not found. Generating..."
  mkdir -p "$JWT_CONFIG_DIR"
  chown www-data:www-data "$JWT_CONFIG_DIR"
  su-exec www-data php bin/console lexik:jwt:generate-keypair
  echo "JWT key pair generated."
else
  echo "JWT key exists. Skipping generation."
fi
STEP=$((STEP + 1))

echo "[$STEP/$TOTAL] Applying database migrations..."
su-exec www-data php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

echo "[$STEP/$TOTAL] All done. App is ready!"
touch "$READY_FILE"

exec "$@"
