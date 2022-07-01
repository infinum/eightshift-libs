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
	$mock = $this->mock;
	$mock([], [
		'root' => Components::getProjectPaths('setupJson'),
	]);

	$output = \file_get_contents(Components::getProjectPaths('setupJson', 'setup.json'));

	expect($output)->toContain('staging');
});

test('Setup CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});
