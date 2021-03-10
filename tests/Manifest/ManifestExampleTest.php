<?php

namespace Tests\Unit\Manifest;

use Brain\Monkey;
use EightshiftBoilerplate\Manifest\ManifestExample;

use function Tests\setupMocks;

beforeEach(function() {
	Monkey\setUp();
	setupMocks();

	// Setup Config mock
	\Mockery::mock('alias:EightshiftBoilerplate\Config\Config')
		->shouldReceive([
			'getProjectName' => 'MyProject',
			'getProjectPath' => 'projectPath',
		]);

	$this->example = new ManifestExample();
});

afterEach(function() {
	Monkey\tearDown();
});

test('Register method will call init hook', function () {
	$this->example->register();

	$this->assertSame(10, has_action('init', 'EightshiftBoilerplate\Manifest\ManifestExample->setAssetsManifestRaw()'));
});

test('Manifest example contains correct manifest file path', function () {
	$manifestFilePath = $this->example->getManifestFilePath();

	$this->assertStringContainsString('projectPath/public/manifest.json', $manifestFilePath);
	$this->assertStringNotContainsString('random string', $manifestFilePath);
});
