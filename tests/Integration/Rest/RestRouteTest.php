<?php

namespace Tests\Integration;

use EightshiftLibs\Rest\Routes\RouteCli;
use InvalidArgumentException;

use function Tests\deleteCliOutput;
use function Tests\mock;

beforeEach(function () {
	parent::setUp();

	// Set up a REST server instance.
	global $wp_rest_server;

	$this->server = $wp_rest_server = new \WP_REST_Server();
	do_action('rest_api_init');

	$wpCliMock = mock('alias:WP_CLI');

	$wpCliMock
		->shouldReceive('success')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('error')
		->andReturnUsing(
			function ($errorMessage) {
					throw new InvalidArgumentException($errorMessage);
			}
	);

	$this->route = new RouteCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	global $wp_rest_server;
	$wp_rest_server = null;

	deleteCliOutput(\dirname(__FILE__, 5) . '/cliOutput');

	parent::tearDown();
});

test('Rest API endpoints work', function () {
	$routes = $this->server->get_routes();

	expect($routes)
		->toBeArray()
		->toHaveKey('/wp/v2/posts');
});

test('REST route CLI command will correctly set up a custom REST route', function () {
	$route = $this->route;
	$route([], $route->getDevelopArgs(['endpoint_slug' => 'books', 'method' => 'get']));

	$routes = $this->server->get_routes();
die(var_export($routes));
	expect($routes)
		->toBeArray()
		->toHaveKey('/wp/v2/posts');
});
