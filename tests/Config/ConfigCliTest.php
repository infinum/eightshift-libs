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
	$output = dirname(__FILE__, 3) . '/cliOutput';

	deleteCliOutput($output);
});


test('Custom acf meta CLI command will correctly copy the Config class with defaults', function () {
	$config = $this->config;
	$config([], $config->getDevelopArgs([]));

	// Check the output dir if the generated method is correctly generated.
	$generatedConfig = file_get_contents(dirname(__FILE__, 3) . '/cliOutput/src/Config/Config.php');

	$this->assertStringContainsString('class Config extends AbstractConfigData', $generatedConfig);
	$this->assertStringContainsString('getProjectName', $generatedConfig);
	$this->assertStringContainsString('getProjectVersion', $generatedConfig);
	$this->assertStringContainsString('getProjectRoutesNamespace', $generatedConfig);
	$this->assertStringContainsString('getProjectRoutesVersion', $generatedConfig);
	$this->assertStringNotContainsString('someRandomMethod', $generatedConfig);
});


test('Custom acf meta CLI documentation is correct', function () {
	$config = $this->config;

	$documentation = $config->getDoc();

	$descKey = 'shortdesc';
	$synopsisKey = 'synopsis';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($descKey, $documentation);
	$this->assertArrayHasKey($synopsisKey, $documentation);
	$this->assertIsArray($documentation[$synopsisKey]);
	$this->assertSame('Generates project config class.', $documentation[$descKey]);

	$this->assertSame('assoc', $documentation[$synopsisKey][0]['type']);
	$this->assertSame('name', $documentation[$synopsisKey][0]['name']);
	$this->assertSame('Define project name.', $documentation[$synopsisKey][0]['description']);
	$this->assertSame(true, $documentation[$synopsisKey][0]['optional']);

	$this->assertSame('assoc', $documentation[$synopsisKey][1]['type']);
	$this->assertSame('version', $documentation[$synopsisKey][1]['name']);
	$this->assertSame('Define project version.', $documentation[$synopsisKey][1]['description']);
	$this->assertSame(true, $documentation[$synopsisKey][1]['optional']);

	$this->assertSame('assoc', $documentation[$synopsisKey][2]['type']);
	$this->assertSame('routes_version', $documentation[$synopsisKey][2]['name']);
	$this->assertSame('Define project REST version.', $documentation[$synopsisKey][2]['description']);
	$this->assertSame(true, $documentation[$synopsisKey][2]['optional']);
});
