<?php

namespace Tests\Unit\Geolocation;

use EightshiftLibs\Cli\ParentGroups\CliCreate;
use EightshiftLibs\Geolocation\GeolocationCli;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->geolocationCli = new GeolocationCli('boilerplate');
});

afterEach(function () {
	setAfterEach();
});

test('Geolocation CLI command parent name is correct', function () {
	$mock = $this->geolocationCli;
	$commandName = $mock->getCommandParentName();

	expect($commandName)->toEqual(CliCreate::COMMAND_NAME);
});

test('Geolocation CLI command name is correct', function () {
	$mock = $this->geolocationCli;
	$commandName = $mock->getCommandName();

	expect($commandName)->toEqual('geolocation');
});

test('Geolocation CLI command will correctly copy the geolocation example class with default args', function () {
	$mock = $this->geolocationCli;
	$args = $mock->getDefaultArgs();
	$mock([], $args);

	// Check the output dir if the generated method is correctly generated.
	$output = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/Geolocation/Geolocation.php');

	expect($output)
		->toContain('class Geolocation extends AbstractGeolocation', $args['cookie_name'])
		->not->toContain('%cookie_name%');
});

test('Geolocation CLI command will correctly copy the geolocation example class with develop args', function () {
	$mock = $this->geolocationCli;
	$args = $mock->getDefaultArgs();
	$mock([], $args);

	// Check the output dir if the generated method is correctly generated.
	$output = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/Geolocation/Geolocation.php');

	expect($output)
		->toContain('class Geolocation extends AbstractGeolocation', $args['cookie_name'])
		->not->toContain('%cookie_name%');
});

test('Geolocation CLI documentation is correct', function () {
	expect($this->geolocationCli->getDoc())->toBeArray()->toHaveKey('synopsis');
	expect($this->geolocationCli->getDoc()['synopsis'][0]['name'])->toEqual('cookie_name');
});
