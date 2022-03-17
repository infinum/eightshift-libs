<?php

namespace Tests\Unit\Manifest;

use Brain\Monkey;
use EightshiftBoilerplate\Manifest\ManifestExample;

use function Tests\mock;
use function Tests\setupMocks;

beforeEach(function() {
	Monkey\setUp();
	setupMocks();

	// Setup Config mock.
	mock('alias:EightshiftBoilerplate\Config\Config')
		->shouldReceive([
			'getProjectName' => 'MyProject',
			'getProjectPath' => 'tests/data',
		]);

	$this->example = new ManifestExample();
});

afterEach(function() {
	Monkey\tearDown();
});

test('Register method will call init hook', function () {
	$this->example->register();

	$this->assertSame(10, has_action('init', 'EightshiftBoilerplate\Manifest\ManifestExample->setAssetsManifestRaw()'));
	$this->assertSame(10, has_filter(ManifestExample::MANIFEST_ITEM, 'EightshiftBoilerplate\Manifest\ManifestExample->getAssetsManifestItem()'));
});

test('Manifest example contains correct manifest file path', function () {
	$manifestFilePath = $this->example->getManifestFilePath();

	$manifestData = \json_decode(\file_get_contents($manifestFilePath), true);

	$this->assertStringContainsString('tests/data/public/manifest.json', $manifestFilePath);
	$this->assertIsArray($manifestData);
	$this->assertArrayHasKey('0.js', $manifestData, 'Array key not present in the manifest data');
	$this->assertArrayHasKey('application.css', $manifestData, 'Array key not present in the manifest data');
	$this->assertArrayNotHasKey('index.js', $manifestData);

});

test('Setting manifest data manually works', function() {
	$this->example->setAssetsManifestRaw();

	$item = $this->example->getAssetsManifestItem('application.css');

	$this->assertIsString($item);
	$this->assertSame('https://example.com/wp-content/themes/eightshift-boilerplate/public/application.css', $item);
});
