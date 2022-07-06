<?php

namespace Tests\Unit\WpCli;

use EightshiftLibs\Helpers\Components;
use EightshiftLibs\WpCli\WpCli;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new WpCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});


test('Custom command CLI command will correctly copy the Custom command class with defaults', function () {
	$mock = $this->mock;
	$mock([], $mock->getDefaultArgs());

	// Check the output dir if the generated method is correctly generated.
	$sep = \DIRECTORY_SEPARATOR;
	$mock = \file_get_contents(Components::getProjectPaths('srcDestination', "WpCli{$sep}TestWpCli.php"));

	expect($mock)
		->assertContains('class TestWpCli implements ServiceCliInterface', $mock)
		->assertContains('function register', $mock)
		->assertContains('function registerCommand', $mock)
		->assertContains('function getDocs', $mock)
		->assertContains('function __invoke', $mock);
});


test('Custom command CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});
