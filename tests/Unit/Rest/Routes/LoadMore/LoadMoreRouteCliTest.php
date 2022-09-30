<?php

namespace Tests\Unit\Rest\Routes\LoadMore;

use EightshiftLibs\Helpers\Components;
use EightshiftLibs\Rest\Routes\LoadMore\LoadMoreRouteCli;

beforeEach(function () {
	$this->mock = new LoadMoreRouteCli('boilerplate');
});

afterEach(function () {
	unset($this->mock);
});


test('REST load more route CLI command will correctly copy the field class with defaults', function () {
	$mock = $this->mock;
	$mock([], $mock->getDefaultArgs());

	$sep = \DIRECTORY_SEPARATOR;
	$output = \file_get_contents(Components::getProjectPaths('cliOutput', "src{$sep}Rest{$sep}Routes{$sep}LoadMore{$sep}LoadMoreRoute.php"));

	expect($output)
		->toContain('class LoadMoreRoute extends AbstractRoute implements CallableRouteInterface')
		->toContain("'methods'")
		->toContain("'callback'")
		->toContain("'permission_callback'")
		->toContain('public function getMappedData')
		->toContain('function routeCallback');
});

test('REST load more route CLI command returns correct command parent name', function () {
	expect($this->mock->getCommandParentName())->toBe('create');
});

test('REST load more route CLI command returns correct command name', function () {
	expect($this->mock->getCommandName())->toBe('rest-route-load-more');
});

test('REST load more route CLI command has correct documentation', function () {
	expect($this->mock->getDoc())
		->toBeArray()
		->toHaveKey('shortdesc')
		->toHaveKey('longdesc')
		->and($this->mock->getDoc()['shortdesc'])->toBe('Create REST-API load more route class.');
});

