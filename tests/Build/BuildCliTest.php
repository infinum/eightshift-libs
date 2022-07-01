<?php

namespace Tests\Unit\Build;

use EightshiftLibs\Build\BuildCli;
use EightshiftLibs\Helpers\Components;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new BuildCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});

test('Build CLI will correctly copy the build script with defaults', function () {
	$sep = \DIRECTORY_SEPARATOR;

	$mock = $this->mock;
	$mock([], [
		'path' => Components::getProjectPaths('cliOutput', 'bin'),
	]);

	$output = \file_get_contents(Components::getProjectPaths('cliOutput', "bin{$sep}build.sh"));

	$this->assertStringNotContainsString('random string', $output);
});

test('Build CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});
