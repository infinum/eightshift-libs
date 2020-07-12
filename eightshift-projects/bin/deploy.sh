#!/usr/bin/env sh

set -e

function deploy() {
  cd wp-content/themes/eightshift-boilerplate;
  npm install
  composer install --no-dev --no-scripts
  npm run build
}

deploy
