<?php

namespace Tests\Integration;

use EightshiftLibs\Rest\Routes\RouteCli;

use function Tests\setupTheme;
use function Tests\deleteTheme;

beforeEach(function () {
	parent::setUp();

	// Set up a REST server instance.
	global $wp_rest_server;

	$this->server = $wp_rest_server = new \WP_REST_Server();
	do_action('rest_api_init', $this->server);
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	global $wp_rest_server;
	$wp_rest_server = null;

	deleteTheme();

	parent::tearDown();
});

test('Rest API endpoints work', function () {
	$routes = $this->server->get_routes();

	expect($routes)
		->toBeArray()
		->toHaveKey('/wp/v2/posts');
});

test('REST route CLI command will correctly set up a custom REST route', function () {
	// Create default custom route.
	$route = new RouteCli('boilerplate');
	$route([], $route->getDevelopArgs([]));

	// Setup theme.
	setupTheme();

	$routes = $this->server->get_routes();

	expect($routes)
		->toBeArray()
		->toHaveKey('eightshift-libs/v1');
});
