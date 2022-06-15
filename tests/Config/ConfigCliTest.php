<?php

namespace Tests\Unit\Config;

use EightshiftLibs\Config\ConfigCli;

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

	$this->config = new ConfigCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	deleteCliOutput();
});


test('Custom acf meta CLI command will correctly copy the Config class with defaults', function () {
	$config = $this->config;
	$config([], $config->getDefaultArgs());

	// Check the output dir if the generated method is correctly generated.
	$generatedConfig = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/Config/Config.php');

	$this->assertStringContainsString('class Config extends AbstractConfigData', $generatedConfig);
	$this->assertStringContainsString('getProjectName', $generatedConfig);
	$this->assertStringContainsString('getProjectVersion', $generatedConfig);
	$this->assertStringContainsString('getProjectRoutesNamespace', $generatedConfig);
	$this->assertStringContainsString('getProjectRoutesVersion', $generatedConfig);
	$this->assertStringNotContainsString('someRandomMethod', $generatedConfig);
});


test('Custom acf meta CLI documentation is correct', function () {
	expect($this->config->getDoc())->toBeArray();
});
