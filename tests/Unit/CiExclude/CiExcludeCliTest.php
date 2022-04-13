<?php

namespace Tests\Unit\CiExclude;

use EightshiftLibs\CiExclude\CiExcludeCli;

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

$this->ciexclude = new CiExcludeCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	deleteCliOutput();
});

test('CiExclude CLI command will correctly copy the ci-exclude text file with defaults', function () {
	$ciexclude = $this->ciexclude;
	$ciexclude([], $ciexclude->getDevelopArgs([]));

	$outputPath = \dirname(__FILE__, 4) . '/cliOutput/ci-exclude.txt';

	// Check the output dir if the generated method is correctly generated.
	$generatedExclude = \file_get_contents($outputPath);

	$this->assertStringContainsString('eightshift-boilerplate', $generatedExclude);
	$this->assertStringNotContainsString('footer.php', $generatedExclude);
	$this->assertFileExists($outputPath);
});

test('CiExclude CLI command will run under custom command name', function () {
	$ciexclude = $this->ciexclude;
	$result = $ciexclude->getCommandName();

	$this->assertStringContainsString('init_ci_exclude', $result);
});

test('CiExclude CLI command will correctly copy the ci-exclude file in the custom folder with set arguments', function () {
	$ciexclude = $this->ciexclude;
	$ciexclude([], [
		'root' => './test',
	]);

	$this->assertFileExists(\dirname(__FILE__, 4) . '/cliOutput/test/ci-exclude.txt');
});

test('CiExclude CLI command will correctly copy the ci-exclude file with set arguments', function () {
	$ciexclude = $this->ciexclude;
	$ciexclude([], [
		'root' => './',
		'project_name' => 'coolPlugin',
		'project_type' => 'plugin',
	]);

	// Check the output dir if the generated method is correctly generated.
	$generatedExclude = \file_get_contents(\dirname(__FILE__, 4) . '/cliOutput/ci-exclude.txt');

	$this->assertStringContainsString('/wp-content/plugin/coolPlugin/node_modules', $generatedExclude);
});


test('CiExclude CLI documentation is correct', function () {
	$ciexclude = $this->ciexclude;

	$documentation = $ciexclude->getDoc();

	$key = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($key, $documentation);
	$this->assertArrayHasKey('synopsis', $documentation);
	$this->assertSame('Initialize Command for building your projects CI exclude file.', $documentation[$key]);
});
