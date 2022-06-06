<?php

namespace Tests\Unit\Services;

use EightshiftLibs\Services\ServiceExampleCli;

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

$this->services = new ServiceExampleCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	deleteCliOutput();
});

test('Services CLI command will correctly copy the Services class with defaults', function () {
	$services = $this->services;
	$services([], $services->getDevelopArgs([]));

	$outputPath = \dirname(__FILE__, 3) . '/cliOutput/src/TestFolder/TMP/TestTest.php';

	// Check the output dir if the generated method is correctly generated.
	$generatedService = \file_get_contents($outputPath);

	$this->assertStringContainsString('class TestTest implements ServiceInterface', $generatedService);
	$this->assertStringContainsString('namespace EightshiftLibs\TestFolder\TMP', $generatedService);
	$this->assertStringContainsString('@package EightshiftLibs\TestFolder\TMP', $generatedService);
	$this->assertStringNotContainsString('footer.php', $generatedService);
	$this->assertFileExists($outputPath);
});

test('Services CLI command will correctly copy the Services class with set arguments', function () {
	$services = $this->services;
	$services([], [
		'namespace' => 'CoolTheme',
		'folder' => 'FolderName',
		'file_name' => 'FileName',
	]);

	$outputPath = \dirname(__FILE__, 3) . '/cliOutput/src/FolderName/FileName.php';

	// Check the output dir if the generated method is correctly generated.
	$generatedService = \file_get_contents($outputPath);

	$this->assertStringContainsString('class FileName implements ServiceInterface', $generatedService);
	$this->assertStringContainsString('namespace CoolTheme\FolderName', $generatedService);
	$this->assertStringContainsString('@package CoolTheme\FolderName', $generatedService);
	$this->assertFileExists($outputPath);
});

test('Services CLI documentation is correct', function () {
	expect($this->services->getDoc())->toBeArray();
});
