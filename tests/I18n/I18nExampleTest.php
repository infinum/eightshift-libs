<?php

namespace Tests\Unit\I18n;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftBoilerplate\I18n\I18nExample;

use function Tests\setupMocks;

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
});

test('Registering load_theme_textdomain works', function () {

	// Set up a side-affect.
	putenv('I18N_ABSTRACTED=false');


	\Mockery::mock('alias:EightshiftBoilerplate\Config\Config')
	->shouldReceive('getProjectName', 'getProjectPath')
	->andReturn('CoolProject', 'projectPath');

	Functions\when('load_theme_textdomain')->alias(function() {
		putenv('I18N_ABSTRACTED=true');
	});

	$this->i18n->loadThemeTextdomain();

	$this->assertSame(getenv('I18N_ABSTRACTED'), 'true', 'Calling void method load_theme_textdomain caused no sideaffects');
});
