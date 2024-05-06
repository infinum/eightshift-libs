<?php

namespace Tests\Unit\Enqueue\Admin;

use Brain\Monkey\Functions;
use EightshiftBoilerplate\Enqueue\Admin\EnqueueAdminExample;
use EightshiftBoilerplate\Manifest\ManifestExample;
use EightshiftLibs\Cache\ManifestCacheCli;
use EightshiftLibs\Config\ConfigThemeCli;
use EightshiftLibs\Enqueue\AbstractAssets;
use EightshiftLibs\Enqueue\Admin\EnqueueAdminCli;
use EightshiftLibs\Helpers\Components;
use EightshiftLibs\Manifest\ManifestInterface;
use Infinum\Cache\ManifestCache;
use Infinum\Enqueue\Admin\EnqueueAdmin;

use function Tests\getMockArgs;
use function Tests\mock;
use function Tests\copyPublicManifestData;
use function Tests\reqOutputFiles;

// class EnqueueAdminExampleTest extends EnqueueAdminExample {

// 	public function __construct(ManifestInterface $manifest)
// 	{
// 		parent::__construct($manifest);
// 	}

// 	protected function getLocalizations(): array
// 	{
// 		return [
// 			'someKey' => ['someValue'],
// 			'anotherKey' => ['anotherValue']
// 		];
// 	}
// };

beforeEach(function () {
	$cliConfig = new ConfigThemeCli('boilerplate');
	$cliConfig([], getMockArgs($cliConfig->getDefaultArgs()));

	$cliManifestCache = new ManifestCacheCli('boilerplate');
	$cliManifestCache([], getMockArgs($cliManifestCache->getDefaultArgs()));

	$cliEnqueueAdmin = new EnqueueAdminCli('boilerplate');
	$cliEnqueueAdmin([], getMockArgs($cliEnqueueAdmin->getDefaultArgs()));

	reqOutputFiles(
		'Config/Config.php',
		'Cache/ManifestCache.php',
		'Enqueue/Admin/EnqueueAdmin.php'
	);

	$this->manifestCache = new ManifestCache;
	$this->enqueueAdmin = new EnqueueAdmin($this->manifestCache);

	copyPublicManifestData();
});

afterEach(function () {
	unset($this->manifestCache);
	unset($this->enqueueAdmin);
});

// beforeEach(function() {
// 	$this->mock = new EnqueueAdminCli('boilerplate');

// 	// Setup Config mock.
// 	mock('alias:EightshiftBoilerplate\Config\Config')
// 		->shouldReceive([
// 			'getProjectName' => 'MyProject',
// 			'getProjectPath' => 'tests/data',
// 			'getProjectVersion' => '1.0',
// 		]);

// 	Functions\when('is_admin')->justReturn(true);

// 	Functions\when('wp_register_style')->alias(function($args) {
// 		putenv("REGISTER_STYLE={$args}");
// 	});

// 	Functions\when('get_current_screen')->alias(function() {
// 		return new class{
// 			public $is_block_editor = false; // We're not in the block editor.
// 		};
// 	});

// 	Functions\when('wp_enqueue_style')->alias(function($args) {
// 		putenv("ENQUEUE_STYLE={$args}");
// 	});

// 	Functions\when('wp_register_script')->alias(function($args) {
// 		putenv("REGISTER_SCRIPT={$args}");
// 	});

// 	Functions\when('wp_enqueue_script')->alias(function($args) {
// 		putenv("ENQUEUE_SCRIPT={$args}");
// 	});

// 	$localize = 'localize';
// 	Functions\when('wp_localize_script')->justReturn(putenv("SIDEAFFECT={$localize}"));

// 	$manifest = new ManifestExample();
// 	// We need to 'kickstart' the manifest registration manually during tests.
// 	$manifest->setAssetsManifestRaw();

// 	$this->adminEnqueue = new EnqueueAdminExampleTest($manifest);

// 	$this->hookSuffix = 'test';
// });

// afterEach(function() {
// 	unset($this->adminEnqueue, $this->hookSuffix);

// 	putenv('REGISTER_STYLE');
// 	putenv('ENQUEUE_STYLE');
// 	putenv('REGISTER_SCRIPT');
// 	putenv('ENQUEUE_SCRIPT');
// 	putenv('SIDEAFFECT');
// });

test('Register method will call login_enqueue_scripts and admin_enqueue_scripts hook', function () {
	$this->enqueueAdmin->register();

	$this->assertSame(10, has_action('login_enqueue_scripts', 'Infinum\Enqueue\Admin\EnqueueAdmin->enqueueStyles()'));
	$this->assertSame(50, has_action('admin_enqueue_scripts', 'Infinum\Enqueue\Admin\EnqueueAdmin->enqueueStyles()'));
	$this->assertSame(10, has_action('admin_enqueue_scripts', 'Infinum\Enqueue\Admin\EnqueueAdmin->enqueueScripts()'));
});

test('getAssetsPrefix method will return string', function () {
	$output = $this->enqueueAdmin->getAssetsPrefix();

	$this->assertIsString($output, 'getAssetsPrefix method must return a string');
});

test('getAssetsVersion method will return string', function () {
	$output = $this->enqueueAdmin->getAssetsVersion();

	$this->assertIsString($output, 'getAssetsVersion method must return a string');
});

test('enqueueStyles method enqueue styles in WP Admin', function () {
	$this->enqueueAdmin->enqueueStyles('MyProject-styles');

	mock('alias:Infinum\Config\Config')
		->shouldReceive([
			'getProjectName' => 'MyProject',
			'getProjectVersion' => '1.0',
			'getProjectTextDomain' => 'inifnum',
		]);

	var_dump($this->enqueueAdmin->getAssetsPrefix());

	$this->assertSame(\getenv('REGISTER_STYLE'), 'MyProject-styles', 'Method enqueueStyles() register style with success');
	$this->assertSame(\getenv('ENQUEUE_STYLE'), 'MyProject-styles', 'Method enqueueStyles() enqueue style with success');
});

// test('enqueueScripts method enqueue scripts in WP Admin', function () {
// 	$this->adminEnqueue->enqueueScripts($this->hookSuffix);

// 	$this->assertSame(\getenv('REGISTER_SCRIPT'), 'MyProject-scripts', 'Method enqueueStyles() failed to register style');
// 	$this->assertSame(\getenv('ENQUEUE_SCRIPT'), 'MyProject-scripts', 'Method enqueueScripts() failed to enqueue style');
// 	$this->assertSame(\getenv('SIDEAFFECT'), 'localize', 'Method wp_localize_script() failed');
// });

// test('Localization will return empty array if not initialized', function() {
// 	class ExampleLocalization extends AbstractAssets {

// 		public function getAssetsPrefix(): string
// 		{
// 			return 'prefix';
// 		}

// 		public function getAssetsVersion(): string
// 		{
// 			return '1.0.0';
// 		}

// 		public function register(): void
// 		{
// 		}

// 		public function getLocalizations(): array
// 		{
// 			return parent::getLocalizations();
// 		}
// 	}

// 	$localizationExample = new ExampleLocalization();

// 	$this->assertIsArray($localizationExample->getLocalizations());
// 	$this->assertEmpty($localizationExample->getLocalizations());
// });

// test('getAdminStyleHandle will return string', function () {
// 	$adminHandle = $this->adminEnqueue->getAdminStyleHandle();

// 	expect($adminHandle)
// 		->toBeString()
// 		->not->toBeArray();
// });

// test('getAdminScriptHandle will return string', function () {
// 	$adminHandle = $this->adminEnqueue->getAdminScriptHandle();

// 	expect($adminHandle)
// 		->toBeString()
// 		->not->toBeArray();
// });

// test('getConditionUse will be false if outside of admin', function () {
// 	Functions\when('is_admin')->justReturn(false);

// 	$conditionUse = $this->adminEnqueue->getConditionUse();

// 	expect($conditionUse)
// 		->toBeFalse()
// 		->not->toBeNull();
// });

// test('getConditionUse will be true if inside block editor', function () {
// 	Functions\when('get_current_screen')->alias(function () {
// 		return new class
// 		{
// 			public $is_block_editor = true; // We are in the block editor.
// 		};
// 	});

// 	$conditionUse = $this->adminEnqueue->getConditionUse();

// 	expect($conditionUse)
// 		->toBeTrue()
// 		->not->toBeNull();
// });
