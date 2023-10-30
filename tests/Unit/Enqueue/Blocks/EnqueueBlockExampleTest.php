<?php

namespace Tests\Unit\Enqueue\Blocks;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftBoilerplate\Enqueue\Blocks\EnqueueBlocksExample;
use EightshiftBoilerplate\Manifest\ManifestExample;
use EightshiftLibs\Manifest\ManifestInterface;

use function Tests\mock;

class EnqueueBlockExampleTest extends EnqueueBlocksExample {

	public function __construct(ManifestInterface $manifest)
	{
		parent::__construct($manifest);
	}

	protected function getLocalizations(): array
	{
		return [
			'someKey' => ['someValue'],
			'anotherKey' => ['anotherValue']
		];
	}
};

/**
 * Setup before each test.
 */
beforeEach(function() {
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

	$config
		->shouldReceive('getProjectPath')
		->andReturn('tests/data');

	Functions\when('wp_register_style')->alias(function($args) {
		putenv("REGISTER_STYLE={$args}");
	});

	Functions\when('wp_enqueue_style')->alias(function($args) {
		putenv("ENQUEUE_STYLE={$args}");
	});

	Functions\when('wp_register_script')->alias(function($args) {
		putenv("REGISTER_SCRIPT={$args}");
	});

	Functions\when('wp_enqueue_script')->alias(function($args) {
		putenv("ENQUEUE_SCRIPT={$args}");
	});

	$localize = 'localize';
	Functions\when('wp_localize_script')->justReturn(putenv("SIDEAFFECT={$localize}"));

	// Creating manifest from manifest data.
	$manifest = new ManifestExample();
	// We need to 'kickstart' the manifest registration manually during tests.
	$manifest->setAssetsManifestRaw();

	$this->blockEnqueue = new EnqueueBlockExampleTest($manifest);

	$this->hookSuffix = 'test';
});

/**
 * Cleanup after each test.
 */
afterEach(function() {
	unset(
		$this->projectName,
		$this->projectVersion,
		$this->blockEnqueue,
		$this->hookSuffix
	);

	putenv('REGISTER_STYLE');
	putenv('ENQUEUE_STYLE');
	putenv('REGISTER_SCRIPT');
	putenv('ENQUEUE_SCRIPT');
	putenv('SIDEAFFECT');
});

/**
 * Checking if register method will register the actions.
 */
test('Enqueue Blocks\' register method will set hooks.', function () {
	$this->blockEnqueue->register();

	$this->assertSame(10, has_action('enqueue_block_editor_assets', 'Tests\Unit\Enqueue\Blocks\EnqueueBlockExampleTest->enqueueBlockEditorScript()'));
	$this->assertSame(50, has_action('enqueue_block_editor_assets', 'Tests\Unit\Enqueue\Blocks\EnqueueBlockExampleTest->enqueueBlockEditorStyle()'));
	$this->assertSame(50, has_action('enqueue_block_assets', 'Tests\Unit\Enqueue\Blocks\EnqueueBlockExampleTest->enqueueBlockStyle()'));
	$this->assertSame(10, has_action('wp_enqueue_scripts', 'Tests\Unit\Enqueue\Blocks\EnqueueBlockExampleTest->enqueueBlockFrontendScript()'));
	$this->assertSame(50, has_action('wp_enqueue_scripts', 'Tests\Unit\Enqueue\Blocks\EnqueueBlockExampleTest->enqueueBlockFrontendStyle()'));
});

/**
 * Checking if method getAssetsPrefix works correctly.
 */
test('Enqueue Blocks will get assets prefix.', function () {
	$assetsPrefix = $this->blockEnqueue->getAssetsPrefix();

	$this->assertSame($assetsPrefix, $this->projectName);
});

/**
 * Checking if method getAssetsVersion works correctly.
 */
test('Enqueue Blocks will get assets version.', function () {
	$assetsVersion= $this->blockEnqueue->getAssetsVersion();

	$this->assertSame($assetsVersion, $this->projectVersion);
});

test('enqueueBlockEditorScript method will enqueue scripts for block editor', function () {
	$this->blockEnqueue->enqueueBlockEditorScript($this->hookSuffix);

	$this->assertSame(\getenv('REGISTER_SCRIPT'), "{$this->projectName}-block-editor-scripts", 'Method enqueueStyles() failed to register style');
	$this->assertSame(\getenv('ENQUEUE_SCRIPT'), "{$this->projectName}-block-editor-scripts", 'Method enqueueScripts() failed to enqueue style');
	$this->assertSame(\getenv('SIDEAFFECT'), 'localize', "Method wp_localize_script() failed");
});

test('enqueueBlockEditorStyle method will enqueue styles for block editor', function () {
	$this->blockEnqueue->enqueueBlockEditorStyle($this->hookSuffix);

	$this->assertSame(\getenv('REGISTER_STYLE'), "{$this->projectName}-block-editor-style", 'Method enqueueStyles() failed to register style');
	$this->assertSame(\getenv('ENQUEUE_STYLE'), "{$this->projectName}-block-editor-style", 'Method enqueueStyles() failed to enqueue style');
});

test('enqueueBlockStyle method will enqueue styles for a block', function () {
	$this->blockEnqueue->enqueueBlockStyle($this->hookSuffix);

	$this->assertSame(\getenv('REGISTER_STYLE'), "{$this->projectName}-block-style", 'Method enqueueStyles() failed to register style');
	$this->assertSame(\getenv('ENQUEUE_STYLE'), "{$this->projectName}-block-style", 'Method enqueueStyles() failed to enqueue style');
});

test('enqueueBlockFrontendScript method will enqueue scripts for a block', function () {
	$this->blockEnqueue->enqueueBlockFrontendScript($this->hookSuffix);

	$this->assertSame(\getenv('REGISTER_SCRIPT'), "{$this->projectName}-block-frontend-scripts", 'Method enqueueStyles() failed to register style');
	$this->assertSame(\getenv('ENQUEUE_SCRIPT'), "{$this->projectName}-block-frontend-scripts", 'Method enqueueScripts() failed to enqueue style');
	$this->assertSame(\getenv('SIDEAFFECT'), 'localize', 'Method wp_localize_script() failed');
});

test('enqueueBlockFrontendStyle method will enqueue styles for a block', function () {
	$this->blockEnqueue->enqueueBlockFrontendStyle($this->hookSuffix);

	$this->assertSame(\getenv('REGISTER_STYLE'), "{$this->projectName}-block-frontend-style", 'Method enqueueStyles() failed to register style');
	$this->assertSame(\getenv('ENQUEUE_STYLE'), "{$this->projectName}-block-frontend-style", 'Method enqueueScripts() failed to enqueue style');
});

test('getBlockEditorScriptsHandle will return string', function () {
	$adminHandle = $this->blockEnqueue->getBlockEditorScriptsHandle();

	expect($adminHandle)
		->toBeString()
		->not->toBeArray();
});

test('getBlockEditorStyleHandle will return string', function () {
	$adminHandle = $this->blockEnqueue->getBlockEditorStyleHandle();

	expect($adminHandle)
		->toBeString()
		->not->toBeArray();
});

test('getBlockFrontentScriptHandle will return string', function () {
	$adminHandle = $this->blockEnqueue->getBlockFrontentScriptHandle();

	expect($adminHandle)
		->toBeString()
		->not->toBeArray();
});

test('getBlockFrontentStyleHandle will return string', function () {
	$adminHandle = $this->blockEnqueue->getBlockFrontentStyleHandle();

	expect($adminHandle)
		->toBeString()
		->not->toBeArray();
});

test('getBlockStyleHandle will return string', function () {
	$adminHandle = $this->blockEnqueue->getBlockStyleHandle();

	expect($adminHandle)
		->toBeString()
		->not->toBeArray();
});
