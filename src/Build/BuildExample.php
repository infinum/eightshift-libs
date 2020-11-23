#!/usr/bin/env sh

set -e

function build() {
	cd 'wp-content/themes/eightshift-boilerplate';
	npm install
	composer install --no-dev
	npm run build
}

build
