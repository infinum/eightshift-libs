<?php

namespace Tests\Unit\Setup;

use EightshiftLibs\Helpers\Helpers;
use EightshiftLibs\Setup\SetupCli;

use function Tests\getMockArgs;

beforeEach(function () {
	$this->mock = new SetupCli('boilerplate');
});

afterEach(function () {
	unset($this->mock);
});

test('Setup CLI command will correctly copy the Setup class with defaults', function () {
	$sep = \DIRECTORY_SEPARATOR;

	$mock = $this->mock;
	$mock([], getMockArgs([
		'path' => Helpers::getProjectPaths('srcDestination', 'setup'),
		'source_path' => Helpers::getProjectPaths('testsData', 'setup'),
	]));

	$output = \file_get_contents(Helpers::getProjectPaths('srcDestination', "setup{$sep}setup.json"));

	expect($output)->toContain('staging');
});

test('Setup CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});
