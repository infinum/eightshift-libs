<?php

namespace Tests\Unit\WpCli;

use EightshiftLibs\Helpers\Helpers;
use EightshiftLibs\WpCli\WpCli;

use function Tests\getMockArgs;

beforeEach(function () {
	$this->mock = new WpCli('boilerplate');
});

afterEach(function () {
	unset($this->mock);
});

test('Custom command CLI command will correctly copy the Custom command class with defaults', function () {
	$mock = $this->mock;
	$mock([], getMockArgs());

	// Check the output dir if the generated method is correctly generated.
	$sep = \DIRECTORY_SEPARATOR;
	$mock = \file_get_contents(Helpers::getProjectPaths('srcDestination', "WpCli{$sep}TestWpCli.php"));

	expect($mock)
		->toContain(
			'class TestWpCli implements ServiceCliInterface',
			'function register',
			'function registerCommand',
			'function getDocs',
			'function __invoke',
		);
});

test('Custom command CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});
