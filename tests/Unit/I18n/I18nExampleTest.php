<?php

namespace Tests\Unit\I18n;

use Brain\Monkey\Functions;
use EightshiftLibs\Config\ConfigThemeCli;
use EightshiftLibs\I18n\I18nCli;
use Infinum\I18n\I18n;

use function Tests\getMockArgs;
use function Tests\reqOutputFiles;

beforeEach(function() {
	$configThemeCliMock = new ConfigThemeCli('boilerplate');
	$configThemeCliMock([], getMockArgs($configThemeCliMock->getDefaultArgs()));

	$i18nCliMock = new I18nCli('boilerplate');
	$i18nCliMock([], getMockArgs($i18nCliMock->getDefaultArgs()));

	reqOutputFiles(
		'Config/Config.php',
		'I18n/I18n.php',
	);
});

test('Register method will call init hook', function () {
	(new I18n())->register();

	$this->assertSame(20, has_action('after_setup_theme', 'Infinum\I18n\I18n->loadThemeTextdomain()'));
	$this->assertSame(20, has_action('enqueue_block_editor_assets', 'Infinum\I18n\I18n->setScriptTranslations()'));
});

test('Registering load_theme_textdomain works', function () {

	// Set up a side effect.
	putenv('I18N_ABSTRACTED=false');

	// mock('alias:Infinum\Config\Config')
	// 	->shouldReceive('getProjectName', 'getProjectPath')
	// 	->andReturn('CoolProject', 'projectPath');

	Functions\when('load_theme_textdomain')->alias(function() {
		putenv('I18N_ABSTRACTED=true');
	});

	(new I18n())->loadThemeTextdomain();

	$this->assertSame(\getenv('I18N_ABSTRACTED'), 'true', 'Calling void method loadThemeTextdomain caused no side effects');
});

test('Registering wp_set_script_translations works', function () {

	// Set up a side effect.
	putenv('GUT_I18N_ABSTRACTED=false');

	// mock('alias:Infinum\Config\Config')
	// 	->shouldReceive('getProjectName', 'getProjectPath')
	// 	->andReturn('CoolProject', 'projectPath');

	// mock('alias:Infinum\Enqueue\Assets')
	// 	->shouldReceive('getAssetsPrefix')
	// 	->andReturn('cool-project');

	Functions\when('wp_set_script_translations')->alias(function () {
		putenv('GUT_I18N_ABSTRACTED=true');
	});

	(new I18n())->setScriptTranslations();

	$this->assertSame(\getenv('I18N_ABSTRACTED'), 'true', 'Calling void method setScriptTranslations caused no side effects');
});
