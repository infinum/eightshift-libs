<?php

namespace Tests\Integration;

use EightshiftLibs\Rest\Routes\RouteCli;

use function Tests\deleteCliOutput;
use function Tests\setupTheme;
use function Tests\replaceStringInFile;

beforeEach(function () {
	parent::setUp();

	// Set up a REST server instance.
	global $wp_rest_server;

	$this->server = $wp_rest_server = new \WP_REST_Server();
	do_action('rest_api_init');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	global $wp_rest_server;
	$wp_rest_server = null;

	deleteCliOutput(\dirname(__FILE__, 4) . '/cliOutput');

	parent::tearDown();
});

test('Rest API endpoints work', function () {
	$routes = $this->server->get_routes();

	expect($routes)
		->toBeArray()
		->toHaveKey('/wp/v2/posts');
});

test('REST route CLI command will correctly set up a custom REST route', function () {
	// Setup theme.
	setupTheme();

	// Create default custom route.
	$route = new RouteCli('boilerplate');
	$route([], $route->getDevelopArgs([]));

	$routes = $this->server->get_routes();

	expect($routes)
		->toBeArray()
		->toHaveKey('eightshift-libs/v1');
});
