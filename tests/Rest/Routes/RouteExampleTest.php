<?php

namespace Tests\Unit\CustomPostType;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftBoilerplate\Rest\Routes\RouteExample;

use function Tests\setupMocks;

beforeEach(function() {
	Monkey\setUp();
	setupMocks();

	$this->route = new RouteExample();
	$this->projectNamespace = 'LibsTests';
	$this->projectVersion = '1.0';
	$this->mockRequestKey = 'some-key';
	$this->mockRequestValue = 'here is the value';

	// Setting up Eightshift Boilerplate Config class mock.
	$config = \Mockery::mock('alias:EightshiftBoilerplate\Config\Config');

	// Mocking functions from EB Config.
	$config
		->shouldReceive('getProjectRoutesNamespace')
		->andReturn($this->projectNamespace);

	$config
		->shouldReceive('getProjectRoutesVersion')
		->andReturn($this->projectVersion);

	$this->wpRestServer = \Mockery::mock('alias:WP_REST_Server');
	$this->wpRestRequest = \Mockery::mock('alias:WP_REST_Request');

	$this->wpRestRequest
		->shouldReceive('get_body')
		->andReturn(json_encode([$this->mockRequestKey => $this->mockRequestValue]));
});

afterEach(function() {
	Monkey\tearDown();
});


test('Register method will call init hook', function () {
	$this->route->register();

	$this->assertSame(10, has_action('rest_api_init', 'EightshiftBoilerplate\Rest\Routes\RouteExample->routeRegisterCallback()'));
});


test('Route has a valid callback', function () {
	$output = $this->route->routeCallback($this->wpRestRequest);

	$this->assertIsArray($output);
	$this->assertArrayHasKey($this->mockRequestKey, $output);
	$this->assertEquals($output[$this->mockRequestKey], $this->mockRequestValue);
});


test('Route registers the callback properly', function () {
	$action = 'route_registered';
	Functions\when('register_rest_route')->justReturn(putenv("SIDEAFFECT={$action}"));

	$this->route->routeRegisterCallback($this->wpRestServer);

	$this->assertEquals(getenv('SIDEAFFECT'), $action);

	// Cleanup.
	putenv('SIDEAFFECT=');
});
