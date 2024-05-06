<?php

namespace Tests\Unit\Enqueue\Blocks;

use EightshiftLibs\Cache\AbstractManifestCache;
use EightshiftLibs\Enqueue\Blocks\AbstractEnqueueBlocks;

class EnqueueBlockExampleTest extends AbstractEnqueueBlocks
{
	public function register(): void
	{
		\add_action('login_enqueue_scripts', [$this, 'enqueueStyles']);
		\add_action('admin_enqueue_scripts', [$this, 'enqueueStyles'], 50);
		\add_action('admin_enqueue_scripts', [$this, 'enqueueScripts']);
	}
	public function getAssetsPrefix(): string
	{
		return 'MyProject';
	}
	public function getAssetsVersion(): string
	{
		return '1.0.0';
	}
	public function getLocalizations(): array
	{
		return [
			'example' => 'example',
		];
	}
}

class ManifestCache extends AbstractManifestCache
{
	public function getCacheName(): string
	{
		return 'my-project';
	}
}

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
