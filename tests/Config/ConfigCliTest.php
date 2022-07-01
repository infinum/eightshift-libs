<?php

namespace Tests\Unit\Config;

use EightshiftLibs\Config\ConfigCli;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new ConfigCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});

test('Custom acf meta CLI command will correctly copy the Config class with defaults', function () {
	$config = $this->mock;
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
	expect($this->mock->getDoc())->toBeArray();
});
