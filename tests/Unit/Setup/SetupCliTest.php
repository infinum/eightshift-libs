<?php

namespace Tests\Unit\Setup;

use EightshiftLibs\Helpers\Components;
use EightshiftLibs\Setup\SetupCli;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new SetupCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});

test('Setup CLI command will correctly copy the Setup class with defaults', function () {
	$sep = \DIRECTORY_SEPARATOR;

	$mock = $this->mock;
	$mock([], [
		'path' => Components::getProjectPaths('cliOutput', 'setup'),
		'source_path' => Components::getProjectPaths('testsData', 'setup'),
	]);

	$output = \file_get_contents(Components::getProjectPaths('cliOutput', "setup{$sep}setup.json"));

	expect($output)->toContain('staging');
});

test('Setup CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});
