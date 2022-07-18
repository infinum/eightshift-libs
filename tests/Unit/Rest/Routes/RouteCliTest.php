<?php

namespace Tests\Unit\CustomPostType;

use EightshiftLibs\Helpers\Components;
use EightshiftLibs\Rest\Routes\RouteCli;

beforeEach(function () {
	$this->mock = new RouteCli('boilerplate');
});

afterEach(function () {
	unset($this->mock);
});


test('REST route CLI command will correctly copy the field class with defaults', function () {
	$mock = $this->mock;
	$mock([], $mock->getDefaultArgs());

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Components::getProjectPaths('cliOutput', "src{$sep}Rest{$sep}Routes{$sep}TestRoute.php"));

	$this->assertStringContainsString('class TestRoute extends AbstractRoute implements CallableRouteInterface', $output);
	$this->assertStringContainsString('\'methods\' => ', $output);
	$this->assertStringContainsString('\'callback\' => ', $output);
	$this->assertStringContainsString('\'permission_callback\' => ', $output);
	$this->assertStringContainsString('function getCallbackArguments', $output);
	$this->assertStringContainsString('function routeCallback', $output);
});


test('REST route CLI command will correctly copy the field class with arguments', function ($mockArguments) {
	$mock = $this->mock;
	$mock([], $mockArguments);

	$full_route_name = "{$this->mock->getFileName($mockArguments['endpoint_slug'])}Route";
	$method_to_const = RouteCli::VERB_ENUM[\strtolower($mockArguments['method'])] ?? '';

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Components::getProjectPaths('cliOutput', "src{$sep}Rest{$sep}Routes{$sep}{$full_route_name}.php"));

	$this->assertStringContainsString("class {$full_route_name} extends AbstractRoute implements CallableRouteInterface", $output);
	$this->assertStringContainsString("'methods' => {$method_to_const}", $output);
})->with('correctRouteArguments');
