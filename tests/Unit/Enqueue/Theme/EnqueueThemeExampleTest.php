<?php

namespace Tests\Unit\Enqueue\Theme;

use EightshiftLibs\Cache\AbstractManifestCache;
use EightshiftLibs\Enqueue\Theme\AbstractEnqueueTheme;

class EnqueueThemeExampleTest extends AbstractEnqueueTheme
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

test('Register method will call wp_enqueue_scripts hook', function () {
	$this->themeEnqueue->register();

	$this->assertSame(10, has_action('wp_enqueue_scripts', 'Tests\Unit\Enqueue\Theme\EnqueueThemeExampleTest->enqueueStyles()'));
	$this->assertSame(10, has_action('wp_enqueue_scripts', 'Tests\Unit\Enqueue\Theme\EnqueueThemeExampleTest->enqueueScripts()'));
	$this->assertNotSame(10, has_action('admin_enqueue_scripts', 'Tests\Unit\Enqueue\Theme\EnqueueThemeExampleTest->enqueueStyles()'));
	$this->assertNotSame(10, has_action('admin_enqueue_scripts', 'Tests\Unit\Enqueue\Theme\EnqueueThemeExampleTest->enqueueScripts()'));
});

test('getAssetsPrefix method will return string', function () {
	$assetsPrefix = $this->themeEnqueue->getAssetsPrefix();

	$this->assertIsString($assetsPrefix, 'getAssetsPrefix method must return a string');
});

test('getAssetsVersion method will return string', function () {
	$assetsVersion = $this->themeEnqueue->getAssetsVersion();

	$this->assertIsString($assetsVersion, 'getAssetsVersion method must return a string');
});

test('enqueueStyles method will enqueue styles in a theme', function () {
	$this->themeEnqueue->enqueueStyles($this->hookSuffix);
	$this->assertSame(\getenv('REGISTER_STYLE'), 'MyProject-theme-styles', 'Method enqueueStyles() failed to register style');
	$this->assertSame(\getenv('ENQUEUE_STYLE'), 'MyProject-theme-styles', 'Method enqueueStyles() failed to enqueue style');
});

test('enqueueScripts method will enqueue scripts in a theme', function () {

	$this->themeEnqueue->enqueueScripts($this->hookSuffix);
	$this->assertSame(\getenv('REGISTER_SCRIPT'), 'MyProject-scripts', 'Method enqueueStyles() failed to register style');
	$this->assertSame(\getenv('ENQUEUE_SCRIPT'), 'MyProject-scripts', 'Method enqueueScripts() failed to enqueue style');
	$this->assertSame(\getenv('SIDEAFFECT'), 'localize', 'Method wp_localize_script() failed');
});

test('getThemeScriptHandle will return string', function () {
	$adminHandle = $this->themeEnqueue->getThemeScriptHandle();

	expect($adminHandle)
		->toBeString()
		->not->toBeArray();
});

test('getThemeStyleHandle will return string', function () {
	$adminHandle = $this->themeEnqueue->getThemeStyleHandle();

	expect($adminHandle)
		->toBeString()
		->not->toBeArray();
});
