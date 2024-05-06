<?php

namespace Tests\Unit\Rest\Routes;

use Brain\Monkey\Functions;
use EightshiftLibs\Config\ConfigThemeCli;
use EightshiftLibs\Rest\Routes\RouteCli;
use Infinum\Rest\Routes\TestRoute;

use function Tests\getMockArgs;
use function Tests\mock;
use function Tests\reqOutputFiles;

beforeEach(function () {
	$configThemeCliMock = new ConfigThemeCli('boilerplate');
	$configThemeCliMock([], getMockArgs($configThemeCliMock->getDefaultArgs()));

	$routeCliMock = new RouteCli('boilerplate');
	$routeCliMock([], getMockArgs($routeCliMock->getDefaultArgs()));

	reqOutputFiles(
		'Config/Config.php',
		'Rest/Routes/TestRoute.php',
	);

	$this->wpRestServer = mock('alias:WP_REST_Server');
	$this->wpRestRequest = mock('alias:WP_REST_Request');

	$this->mockRequestKey = 'some-key';
	$this->mockRequestValue = 'here is the value';

	$this->wpRestRequest
		->shouldReceive('get_body')
		->andReturn(json_encode([$this->mockRequestKey => $this->mockRequestValue]));
});

afterEach(function () {
	unset(
		$this->mockRequestKey,
		$this->mockRequestValue,
		$this->wpRestServer,
		$this->wpRestRequest,
	);
});

test('Register method will call init hook', function () {
	(new TestRoute())->register();

	$this->assertSame(10, has_action('rest_api_init', 'Infinum\Rest\Routes\TestRoute->routeRegisterCallback()'));
});

test('Route has a valid callback', function () {
	$output = (new TestRoute())->routeCallback($this->wpRestRequest);

	$this->assertIsArray($output);
	$this->assertArrayHasKey($this->mockRequestKey, $output);
	$this->assertSame($output[$this->mockRequestKey], $this->mockRequestValue);
});

test('Route registers the callback properly', function () {
	$action = 'route_registered';
	Functions\when('register_rest_route')->justReturn(putenv("SIDEAFFECT={$action}"));

	(new TestRoute())->routeRegisterCallback($this->wpRestServer);

	$this->assertSame(\getenv('SIDEAFFECT'), $action);

	// Cleanup.
	putenv('SIDEAFFECT=');
});
