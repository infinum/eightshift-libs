<?php

namespace Tests\Unit\Manifest;

use EightshiftLibs\Manifest\ManifestCli;

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

	$this->manifest = new ManifestCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	$output = dirname(__FILE__, 3) . '/cliOutput';

	deleteCliOutput($output);
});

test('Manifest CLI command will correctly copy the Manifest class with defaults', function () {
	$manifest = $this->manifest;
	$manifest([], []);

	// Check the output dir if the generated method is correctly generated.
	$generatedManifest = file_get_contents(dirname(__FILE__, 3) . '/cliOutput/src/Manifest/Manifest.php');
	$this->assertStringContainsString('class Manifest extends AbstractManifest', $generatedManifest);
	$this->assertStringContainsString('setAssetsManifestRaw', $generatedManifest);
	$this->assertStringContainsString('manifest-item', $generatedManifest);
	$this->assertStringNotContainsString('random string', $generatedManifest);
});

test('Manifest CLI command will correctly copy the Manifest class with set arguments', function () {
	$manifest = $this->manifest;
	$manifest([], [
		'namespace' => 'MyTheme',
	]);

	// Check the output dir if the generated method is correctly generated.
	$generatedManifest = file_get_contents(dirname(__FILE__, 3) . '/cliOutput/src/Manifest/Manifest.php');

	$this->assertStringContainsString('namespace MyTheme\Manifest;', $generatedManifest);
});

test('Manifest CLI documentation is correct', function () {
	$manifest = $this->manifest;

	$documentation = $manifest->getDoc();

	$key = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($key, $documentation);
	$this->assertArrayNotHasKey('synopsis', $documentation);
	$this->assertEquals('Generates Manifest class.', $documentation[$key]);
});
