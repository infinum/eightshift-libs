<?php

namespace Tests\Unit\EnqueueBlock;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftBoilerplate\Enqueue\Blocks\EnqueueBlocksExample;

use EightshiftBoilerplate\Manifest\ManifestExample;

use function Tests\setupMocks;
use function Tests\mock;

/**
 * Setup before each test.
 */
beforeEach(function() {
	Monkey\setUp();
	setupMocks();

	// Setting imaginary values for mock and testing.
	$this->projectName = 'NewProject';
	$this->projectVersion = '3.1.23';

	// Setting WPCLI mock.
	mock('alias:WP_CLI')
		->shouldReceive('success', 'error')
		->andReturnArg(0);

	// Setting up Eightshift Boilerplate Config class mock.
	$config = mock('alias:EightshiftBoilerplate\Config\Config');

	// Mocking functions from EB Config.
	$config
		->shouldReceive('getProjectName')
		->andReturn($this->projectName);

	$config
		->shouldReceive('getProjectVersion')
		->andReturn($this->projectVersion);

	// Getting test data from manifest, mocking manifest's path.
	$manifestPath = dirname(__FILE__, 2) . '/data/public/manifest.json';
	Functions\when('getManifestFilePath')->justReturn($manifestPath);

	// Creating manifest from manifest data.
	$manifest = new ManifestExample();
	$this->eb = new EnqueueBlocksExample($manifest);
});

/**
 * Cleanup after each test.
 */
afterEach(function() {
	Monkey\tearDown();
});

/**
 * Checking if register method will register the actions.
 */
test('Enqueue Blocks\' register method will set hooks.', function () {
	$this->eb->register();

	$this->assertSame(10, has_action('enqueue_block_editor_assets', 'EightshiftBoilerplate\Enqueue\Blocks\EnqueueBlocksExample->enqueueBlockEditorScript()'));
	$this->assertSame(50, has_action('enqueue_block_editor_assets', 'EightshiftBoilerplate\Enqueue\Blocks\EnqueueBlocksExample->enqueueBlockEditorStyle()'));
	$this->assertSame(50, has_action('enqueue_block_assets', 'EightshiftBoilerplate\Enqueue\Blocks\EnqueueBlocksExample->enqueueBlockStyle()'));
	$this->assertSame(10, has_action('wp_enqueue_scripts', 'EightshiftBoilerplate\Enqueue\Blocks\EnqueueBlocksExample->enqueueBlockScript()'));
});

/**
 * Checking if method getAssetsPrefix works correctly.
 */
test('Enqueue Blocks will get assets prefix.', function () {
	$assetsPrefix = $this->eb->getAssetsPrefix();

	$this->assertSame($assetsPrefix, $this->projectName);
});

/**
 * Checking if method getAssetsVersion works correctly.
 */
test('Enqueue Blocks will get assets version.', function () {
	$assetsVersion= $this->eb->getAssetsVersion();

	$this->assertSame($assetsVersion, $this->projectVersion);
});
