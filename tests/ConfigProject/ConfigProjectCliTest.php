<?php

namespace Tests\Unit\ConfigProject;

use EightshiftLibs\ConfigProject\ConfigProjectCli;

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

	$this->configProject = new ConfigProjectCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	deleteCliOutput();
});

test('ConfigProject CLI command will correctly copy the ConfigProject class with defaults', function () {
	$configProject = $this->configProject;
	$configProject([], $configProject->getDevelopArgs([]));

	$outputPath = \dirname(__FILE__, 3) . '/cliOutput/wp-config-project.php';

	// Check the output dir if the generated method is correctly generated.
	$generatedConfigProject = \file_get_contents($outputPath);

	$this->assertStringContainsString('!\defined(\'WP_ENVIRONMENT_TYPE\')', $generatedConfigProject);
	$this->assertStringContainsString('@package EightshiftLibs', $generatedConfigProject);
	$this->assertStringNotContainsString('footer.php', $generatedConfigProject);
	$this->assertFileExists($outputPath);
});

test('ConfigProject CLI command will run under custom command name', function () {
	$configProject = $this->configProject;
	$result = $configProject->getCommandName();

	$this->assertStringContainsString('config_project', $result);
});

test('ConfigProject CLI command will correctly copy the ConfigProject class with set arguments', function () {
	$configProject = $this->configProject;
	$configProject([], [
		'root' => './test',
	]);

	// Check the output dir if the generated method is correctly generated.
	$generatedConfigProject = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/test/wp-config-project.php');

	$this->assertStringContainsString('!\defined(\'WP_ENVIRONMENT_TYPE\')', $generatedConfigProject);
});

test('ConfigProject CLI documentation is correct', function () {
	expect($this->configProject->getDoc())->toBeArray();
});
