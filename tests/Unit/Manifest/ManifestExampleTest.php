<?php

namespace Tests\Unit\Manifest;

use EightshiftBoilerplate\Manifest\ManifestExample;

use function Tests\mock;

beforeEach(function() {
	// Setup Config mock.
	mock('alias:EightshiftBoilerplate\Config\Config')
		->shouldReceive([
			'getProjectName' => 'MyProject',
			'getProjectPath' => 'tests/data',
		]);

	$this->example = new ManifestExample();
});

afterEach(function () {
	unset($this->example);
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
	/** getAssetsManifestItem returns an empty string when ES_TEST is set, to prevent issues with automatically parsing manifest data
	*	during tests. However, when setting manifest data manually, these issues aren't present, so the ES_TEST environment variable
	*	is temporarily unset during this test.
	*/
	putenv('ES_TEST');
	$this->example->setAssetsManifestRaw();

	$item = $this->example->getAssetsManifestItem('application.css');

	$this->assertIsString($item);
	$this->assertSame('https://example.com/wp-content/themes/eightshift-boilerplate/public/application.css', $item);
	putenv('ES_TEST=true');
});
