<?php

namespace Tests\Unit\Services;

use EightshiftLibs\Services\ServiceExampleCli;

use function Tests\deleteCliOutput;

/**
 * Mock before tests.
 */
beforeEach(function () {
	$wpCliMock = \Mockery::mock('alias:WP_CLI');

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
	$output = dirname(__FILE__, 3) . '/cliOutput';

	deleteCliOutput($output);
});

test('Services CLI command will correctly copy the Services class with defaults', function () {
	$services = $this->services;
	$services([], $services->getDevelopArgs([]));

	// Check the output dir if the generated method is correctly generated.
	$generatedMain = file_get_contents(dirname(__FILE__, 3) . '/cliOutput/src/TestFolder/TMP/TestTest.php');

	$this->assertStringContainsString('class TestTest implements ServiceInterface', $generatedMain);
	$this->assertStringContainsString('namespace EightshiftLibs\TestFolder\TMP', $generatedMain);
	$this->assertStringContainsString('@package EightshiftBoilerplate\TestFolder\TMP', $generatedMain);
});

test('Services CLI command will correctly copy the Services class with set arguments', function () {
	$services = $this->services;
	$services([], [
		'namespace' => 'CoolTheme',
		'folder' => 'FolderName',
		'file_name' => 'FileName',
	]);

	// Check the output dir if the generated method is correctly generated.
	$generatedMain = file_get_contents(dirname(__FILE__, 3) . '/cliOutput/src/FolderName/FileName.php');

	$this->assertStringContainsString('class FileName implements ServiceInterface', $generatedMain);
	$this->assertStringContainsString('namespace CoolTheme\FolderName', $generatedMain);
	$this->assertStringContainsString('@package EightshiftBoilerplate\FolderName', $generatedMain);
});

test('Services CLI documentation is correct', function () {
	$services = $this->services;

	$documentation = $services->getDoc();

	$key = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($key, $documentation);
	$this->assertArrayHasKey('synopsis', $documentation);
	$this->assertEquals('Generates empty generic service class.', $documentation[$key]);
});
