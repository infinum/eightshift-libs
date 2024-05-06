<?php

namespace Tests\Unit\Geolocation;

use EightshiftLibs\Cli\ParentGroups\CliCreate;
use EightshiftLibs\Geolocation\GeolocationCli;
use EightshiftLibs\Helpers\Helpers;

use function Tests\getMockArgs;

beforeEach(function () {
	$this->mock = new GeolocationCli('boilerplate');
});

afterEach(function () {
	unset($this->mock);
});

test('Geolocation CLI command parent name is correct', function () {
	$mock = $this->mock;
	$commandName = $mock->getCommandParentName();

	expect($commandName)->toEqual(CliCreate::COMMAND_NAME);
});

test('Geolocation CLI command name is correct', function () {
	$mock = $this->mock;
	$commandName = $mock->getCommandName();

	expect($commandName)->toEqual('geolocation');
});

test('Geolocation CLI command will correctly copy the geolocation example class with default args', function () {
	$mock = $this->mock;
	$args = getMockArgs();
	$mock([], $args);

	// Check the output dir if the generated method is correctly generated.
	$output = \file_get_contents(Helpers::getProjectPaths('srcDestination', 'Geolocation/Geolocation.php'));

	expect($output)
		->toContain('class Geolocation extends AbstractGeolocation', $args['cookie_name'])
		->not->toContain('%cookie_name%');
});

test('Geolocation CLI command will correctly copy the geolocation example class with develop args', function () {
	$mock = $this->mock;
	$args = getMockArgs();
	$mock([], $args);

	// Check the output dir if the generated method is correctly generated.
	$output = \file_get_contents(Helpers::getProjectPaths('srcDestination', 'Geolocation/Geolocation.php'));

	expect($output)
		->toContain('class Geolocation extends AbstractGeolocation', $args['cookie_name'])
		->not->toContain('%cookie_name%');
});

test('Geolocation CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray()->toHaveKey('synopsis');
	expect($this->mock->getDoc()['synopsis'][0]['name'])->toEqual('cookie_name');
});
