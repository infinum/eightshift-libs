<?php

namespace Tests\Unit\Enqueue\Admin;

use Brain\Monkey\Functions;
use EightshiftLibs\Cache\AbstractManifestCache;
use EightshiftLibs\Enqueue\Admin\AbstractEnqueueAdmin;

use function Tests\copyPublicManifestData;

class EnqueueAdminExampleTest extends AbstractEnqueueAdmin
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

beforeEach(function () {
	copyPublicManifestData();
});

test('Register method will call login_enqueue_scripts and admin_enqueue_scripts hook', function () {
	$output = (new EnqueueAdminExampleTest(new ManifestCache()))->register();

	$this->assertSame(10, has_action('login_enqueue_scripts', 'Tests\Unit\Enqueue\Admin\EnqueueAdminExampleTest->enqueueStyles()'));
	$this->assertSame(50, has_action('admin_enqueue_scripts', 'Tests\Unit\Enqueue\Admin\EnqueueAdminExampleTest->enqueueStyles()'));
	$this->assertSame(10, has_action('admin_enqueue_scripts', 'Tests\Unit\Enqueue\Admin\EnqueueAdminExampleTest->enqueueScripts()'));
});

test('getAssetsPrefix method will return string', function () {
	$output = (new EnqueueAdminExampleTest(new ManifestCache()))->getAssetsPrefix();

	$this->assertIsString($output, 'getAssetsPrefix method must return a string');
});

test('getAssetsVersion method will return string', function () {
	$output = (new EnqueueAdminExampleTest(new ManifestCache()))->getAssetsVersion();

	$this->assertIsString($output, 'getAssetsVersion method must return a string');
});

test('enqueueStyles method enqueue styles in WP Admin', function () {
	(new EnqueueAdminExampleTest(new ManifestCache()))->enqueueStyles('MyProject-styles');

	$this->assertSame(\getenv('REGISTER_STYLE'), 'MyProject-styles', 'Method enqueueStyles() register style with success');
	$this->assertSame(\getenv('ENQUEUE_STYLE'), 'MyProject-styles', 'Method enqueueStyles() enqueue style with success');
});

test('enqueueScripts method enqueue scripts in WP Admin', function () {

	(new EnqueueAdminExampleTest(new ManifestCache()))->enqueueScripts('MyProject-scripts');

	$this->assertSame(\getenv('REGISTER_SCRIPT'), 'MyProject-scripts', 'Method enqueueStyles() register script with success');
	$this->assertSame(\getenv('ENQUEUE_SCRIPT'), 'MyProject-scripts', 'Method enqueueScripts() enqueue script with success');
	$this->assertSame(\getenv('LOCALIZE_SCRIPT'), 'MyProject-scripts', 'Method wp_localize_script() failed');
});

test('Localization will return empty array if not initialized', function() {
	$output = (new EnqueueAdminExampleTest(new ManifestCache()));

	$this->assertIsArray($output->getLocalizations());
	$this->assertNotEmpty($output->getLocalizations());
});

test('getAdminStyleHandle will return string', function () {
	$output = (new EnqueueAdminExampleTest(new ManifestCache()))->getAdminStyleHandle();

	expect($output)
		->toBeString()
		->not->toBeArray();
});

test('getAdminScriptHandle will return string', function () {
	$output = (new EnqueueAdminExampleTest(new ManifestCache()))->getAdminScriptHandle();

	expect($output)
		->toBeString()
		->not->toBeArray();
});

test('getConditionUse will be false if outside of admin', function () {
	$output = (new EnqueueAdminExampleTest(new ManifestCache()))->getConditionUse();

	expect($output)
		->toBeFalse()
		->not->toBeNull();
});

test('getConditionUse will be true if inside block editor', function () {
	Functions\when('is_admin')->justReturn(true);
	Functions\when('get_current_screen')->alias(function () {
		return new class
		{
			public $is_block_editor = true; // We are in the block editor.
		};
	});

	$output = (new EnqueueAdminExampleTest(new ManifestCache()))->getConditionUse();

	expect($output)
		->toBeTrue()
		->not->toBeNull();
});
