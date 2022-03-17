<?php

namespace Tests\Unit\I18n;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftBoilerplate\I18n\I18nExample;

use function Tests\setupMocks;
use function Tests\mock;

beforeEach(function() {
	Monkey\setUp();
	setupMocks();

	$this->i18n = new I18nExample();
});

afterEach(function() {
	Monkey\tearDown();
});

test('Register method will call init hook', function () {
	$this->i18n->register();

	$this->assertSame(20, has_action('after_setup_theme', 'EightshiftBoilerplate\I18n\I18nExample->loadThemeTextdomain()'));
	$this->assertSame(20, has_action('enqueue_block_editor_assets', 'EightshiftBoilerplate\I18n\I18nExample->setScriptTranslations()'));
});

test('Registering load_theme_textdomain works', function () {

	// Set up a side effect.
	putenv('I18N_ABSTRACTED=false');

	mock('alias:EightshiftBoilerplate\Config\Config')
		->shouldReceive('getProjectName', 'getProjectPath')
		->andReturn('CoolProject', 'projectPath');

	Functions\when('load_theme_textdomain')->alias(function() {
		putenv('I18N_ABSTRACTED=true');
	});

	$this->i18n->loadThemeTextdomain();

	$this->assertSame(\getenv('I18N_ABSTRACTED'), 'true', 'Calling void method loadThemeTextdomain caused no side effects');
});

test('Registering wp_set_script_translations works', function () {

	// Set up a side effect.
	putenv('GUT_I18N_ABSTRACTED=false');

	mock('alias:EightshiftBoilerplate\Config\Config')
		->shouldReceive('getProjectName', 'getProjectPath')
		->andReturn('CoolProject', 'projectPath');

	mock('alias:EightshiftBoilerplate\Enqueue\Assets')
		->shouldReceive('getAssetsPrefix')
		->andReturn('cool-project');

	Functions\when('wp_set_script_translations')->alias(function () {
		putenv('GUT_I18N_ABSTRACTED=true');
	});

	$this->i18n->setScriptTranslations();

	$this->assertSame(\getenv('I18N_ABSTRACTED'), 'true', 'Calling void method setScriptTranslations caused no side effects');
});
