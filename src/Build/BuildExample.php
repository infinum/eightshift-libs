#!/usr/bin/env sh

set -e

function build() {
	cd 'wp-content/%project_type%/%project_name%';
	npm install
	composer install --no-dev
	npm run build
}

build
