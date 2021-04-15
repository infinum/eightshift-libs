<?php

namespace Tests\Unit\Enqueue\Admin;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftBoilerplate\Enqueue\Admin\EnqueueAdminExample;
use EightshiftLibs\Manifest\ManifestInterface;
use EightshiftBoilerplate\Manifest\ManifestExample;

use function Tests\setupMocks;
use function Tests\mock;

class EnqueueAdminTest extends EnqueueAdminExample {

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

beforeEach(function() {
	Monkey\setUp();
	setupMocks();

	mock('alias:EightshiftBoilerplate\Config\Config')
	->shouldReceive('getProjectName', 'getProjectVersion')
	->andReturn('tests/data');

	mock('alias:EightshiftBoilerplate\Config\Config')
	->shouldReceive('getProjectName', 'getProjectVersion')
	->andReturn('tests/data', '1.0');

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

	$manifest = new ManifestExample();
	$this->example = new EnqueueAdminTest($manifest);

	$this->hookSuffix = 'test';

});

afterEach(function() {
	Monkey\tearDown();
	putenv("REGISTER_STYLE");
	putenv("ENQUEUE_STYLE");
	putenv("REGISTER_SCRIPT");
	putenv("ENQUEUE_SCRIPT");
});


test('Register method will call login_enqueue_scripts and admin_enqueue_scripts hook', function () {
	$this->example->register();

	$this->assertSame(10, has_action('login_enqueue_scripts', 'Tests\Unit\Enqueue\Admin\EnqueueAdminTest->enqueueStyles()'));
	$this->assertSame(50, has_action('admin_enqueue_scripts', 'Tests\Unit\Enqueue\Admin\EnqueueAdminTest->enqueueStyles()'));
	$this->assertSame(10, has_action('admin_enqueue_scripts', 'Tests\Unit\Enqueue\Admin\EnqueueAdminTest->enqueueScripts()'));
	$this->assertNotSame(10, has_action('wp_enqueue_scripts', 'Tests\Unit\Enqueue\Admin\EnqueueAdminTest->enqueueStyles()'));
	$this->assertNotSame(10, has_action('wp_enqueue_scripts', 'Tests\Unit\Enqueue\Admin\EnqueueAdminTest->enqueueScripts()'));
});

test('getAssetsPrefix method will return string', function () {
	$assetsPrefix = $this->example->getAssetsPrefix();

	$this->assertIsString($assetsPrefix, 'getAssetsPrefix method must return a string');
});

test('getAssetsVersion method will return string', function () {
	$assetsVersion = $this->example->getAssetsVersion();

	$this->assertIsString($assetsVersion, 'getAssetsVersion method must return a string');
});

test('enqueueStyles method enqueue styles in WP Admin', function () {
	$this->example->enqueueStyles($this->hookSuffix);
	$this->assertSame(getenv('REGISTER_STYLE'), 'tests/data-styles', "Method enqueueStyles() failed to register style");
	$this->assertSame(getenv('ENQUEUE_STYLE'), 'tests/data-styles', "Method enqueueStyles() failed to enqueue style");
});

test('enqueueScripts method enqueue scripts in WP Admin', function () {
	$this->example->enqueueScripts($this->hookSuffix);
	$this->assertSame(getenv('REGISTER_SCRIPT'), 'tests/data-scripts', "Method enqueueStyles() failed to register style");
	$this->assertSame(getenv('ENQUEUE_SCRIPT'), 'tests/data-scripts', "Method enqueueScripts() failed to enqueue style");
	$this->assertSame(getenv('SIDEAFFECT'), 'localize', "Method wp_localize_script() failed");
});
