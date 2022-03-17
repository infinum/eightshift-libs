<?php

namespace Tests\Unit\Build;

use EightshiftLibs\Build\BuildCli;

use function Tests\deleteCliOutput;
use function Tests\mock;

/**
 * Mock before tests.
 */
beforeEach(function () {
	$wpCliMock = mock('alias:WP_CLI');

	$wpCliMock
		->shouldReceive('success')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('error')
		->andReturnArg(0);

	$this->buildCli = new BuildCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	$output = \dirname(__FILE__, 3) . '/cliOutput';

	deleteCliOutput($output);
});

test('Build CLI will correctly copy the build script with defaults', function () {
	$buildCli = $this->buildCli;
	$buildCli([], [
		'root' => './'
	]);

	// Check the output dir if the generated method is correctly generated.
	$generatedFile = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/bin/build.sh');
	$this->assertStringNotContainsString('random string', $generatedFile);
});

test('Build CLI will correctly copy the build script to a given folder', function () {
	$buildCli = $this->buildCli;
	$buildCli([], [
		'root' => 'test/',
	]);

	// Check the output dir if the generated method is correctly generated.
	$generatedFile = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/test/bin/build.sh');
	$this->assertStringNotContainsString('random string', $generatedFile);
});

test('Build CLI documentation is correct', function () {
	$buildCli = $this->buildCli;

	$documentation = $buildCli->getDoc();

	$key = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($key, $documentation);
	$this->assertArrayHasKey('synopsis', $documentation);
	$this->assertSame('Initialize Command for building your project with one command, generally used on CI deployments.', $documentation[$key]);
});

test('getDevelopArgs correctly returns arguments', function() {
	$buildCli = $this->buildCli;

	$arguments = $buildCli->getDevelopArgs([]);

	$this->assertIsArray($arguments);
	$this->assertArrayHasKey('root', $arguments);
	$this->assertSame($arguments['root'], './');
});
