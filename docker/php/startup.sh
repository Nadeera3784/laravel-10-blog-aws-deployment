#!/bin/bash

set -e


handle_error_startup() {
  echo "Error in startup.sh. Sleeping for ${EXIT_WAIT_TIME} seconds then will exit ..."
  sleep ${EXIT_WAIT_TIME}
  echo "Exiting..."
}

trap 'handle_error_startup' ERR

echo "Running startup.sh..."

#composer install
echo "Running composer install..."
composer install --no-interaction --no-dev --prefer-dist

# Generate  key
echo "Generate key."

php artisan key:generate

# Check if Artisan exists, lets clear its cache
if [ -f artisan ]; then
  echo "Initialization Portal Application, Environment:$APP_ENV"

  echo "Initializing files and updating permissions..."


  # Update permissions
  touch storage/logs/laravel.log
  touch storage/logs/debug_laravel.log
  chmod -R 0777 storage

  # Clear cache - laravel will rebuild it on first run
  mkdir -p bootstrap/cache
  chmod -R 0777 bootstrap
  rm -f bootstrap/cache/*

  echo "Clearing cache"

  # Clearing Cache
  php artisan optimize:clear

  echo "Finished environment initialization"

fi
