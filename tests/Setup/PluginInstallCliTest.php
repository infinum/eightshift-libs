<?php

namespace Tests\Unit\Setup;

use EightshiftLibs\Exception\FileMissing;
use EightshiftLibs\Setup\PluginInstallCli;

use function Brain\Monkey\Functions\stubEscapeFunctions;
use function Brain\Monkey\Functions\stubTranslationFunctions;
use function Brain\Monkey\Functions\when;
use function Tests\deleteCliOutput;
use function Tests\mock;

/**
 * Mock before tests.
 */
beforeEach(function () {
	$wpCliMock = mock('alias:WP_CLI');

	$wpCliMock
		->shouldReceive('success')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('error')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('runcommand')
		->withSomeOfArgs('plugin list --field=name --format=json')
		->andReturn('["contact-form-7", "wp-rocket", "wordpress-seo"]');

	$wpCliMock
		->shouldReceive('log')
		->andReturnArg(0);

	stubTranslationFunctions();
	stubEscapeFunctions();

	$this->pluginInstall = new PluginInstallCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	deleteCliOutput();
});

test('Plugin Install CLI command will correctly throw an exception if setup.json does not exist or has the wrong filename', function () {
	when('file_exists')->justReturn(false);

	$pluginInstall = $this->pluginInstall;
	$pluginInstall([], []);
})->throws(FileMissing::class);

test('Plugin install CLI documentation is correct', function () {
	expect($this->pluginInstall->getDoc())->toBeArray();
});

test('Plugin install CLI command will work with default action', function () {
	$pluginInstall = $this->pluginInstall;
	$pluginInstall([], []);


});

test('Plugin install CLI command will work when only core plugins should be installed', function () {
	$pluginInstall = $this->pluginInstall;
	$pluginInstall([], ['install-core']);


});
