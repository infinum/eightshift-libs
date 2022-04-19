<?php

namespace Tests\Unit\CustomPostType;

use EightshiftLibs\Rest\Routes\RouteCli;
use InvalidArgumentException;

use function Tests\deleteCliOutput;
use function Tests\mock;
/**
 * Mock before tests.
 */
beforeEach(function () {
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
	deleteCliOutput(\dirname(__FILE__, 5) . '/cliOutput');
});


test('REST route CLI command will correctly copy the field class with defaults', function () {
	$route = $this->route;
	$route([], $route->getDevelopArgs([]));

	// Check the output dir if the generated method is correctly generated.
	$generatedField = \file_get_contents(\dirname(__FILE__, 5) . '/cliOutput/src/Rest/Routes/TestRoute.php');

	$this->assertStringContainsString('class TestRoute extends AbstractRoute implements CallableRouteInterface', $generatedField);
	$this->assertStringContainsString('\'methods\' => ', $generatedField);
	$this->assertStringContainsString('\'callback\' => ', $generatedField);
	$this->assertStringContainsString('\'permission_callback\' => ', $generatedField);
	$this->assertStringContainsString('function getCallbackArguments', $generatedField);
	$this->assertStringContainsString('function routeCallback', $generatedField);
});


test('REST route CLI command will correctly copy the field class with arguments', function ($routeArguments) {
	$route = $this->route;
	$route([], $routeArguments);

	$full_route_name = "{$this->route->getFileName($routeArguments['endpoint_slug'])}Route";
	$method_to_const = RouteCli::VERB_ENUM[\strtolower($routeArguments['method'])] ?? '';

	// Check the output dir if the generated method is correctly generated.
	$generatedField = \file_get_contents(\dirname(__FILE__, 5) . "/cliOutput/src/Rest/Routes/{$full_route_name}.php");

	$this->assertStringContainsString("class {$full_route_name} extends AbstractRoute implements CallableRouteInterface", $generatedField);
	$this->assertStringContainsString("'methods' => {$method_to_const}", $generatedField);
})->with('correctRouteArguments');
