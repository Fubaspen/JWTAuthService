#!/bin/sh
set -e

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
cd /var/www/html
su-exec www-data php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
echo "Database migrations applied."

echo "Executing command: $@"

exec "$@"