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
		->andReturn('["contact-form-7", "wordpress-seo"]');

	$wpCliMock
		->shouldReceive('runcommand')
		->withSomeOfArgs('plugin list --fields=name,version --format=json')
		->andReturn('[{"name":"advanced-custom-fields-pro","version":"5.10.2"},{"name":"contact-form-7","version":"5.1.8"},{"name":"elementor-pro","version":"3.7.0"},{"name":"eightshift-forms","version":"1.0.0"},{"name":"query-monitor","version":"3.6.7"},{"name":"wp-rocket","version":"3.11.2"}]');

	$wpCliMock
		->shouldReceive('runcommand')
		->withSomeOfArgs('plugin delete wordpress-seo')
		->andReturnUsing(function ($message) {
			putenv("ES_CLI_RUN_COMMAND_PLUGIN_DELETE_HAPPENED={$message}");
		});

	$wpCliMock
		->shouldReceive('runcommand')
		->withSomeOfArgs('plugin update')
		->andReturnUsing(function ($message) {
			putenv("ES_CLI_RUN_COMMAND_PLUGIN_UPDATE_HAPPENED={$message}");
		});

	$wpCliMock
		->shouldReceive('runcommand')
		->withSomeOfArgs('plugin install')
		->andReturnUsing(function ($message) {
			putenv("ES_CLI_RUN_COMMAND_PLUGIN_INSTALL_HAPPENED={$message}");
		});

	$wpCliMock
		->shouldReceive('runcommand')
		->withSomeOfArgs('plugin install query-monitor --force')
		->andReturnUsing(function ($message) {
			putenv("ES_CLI_RUN_COMMAND_PLUGIN_INSTALL_QM_HAPPENED={$message}");
		});

	$wpCliMock
		->shouldReceive('runcommand')
		->withSomeOfArgs('plugin install "https://github.com/infinum/eightshift-forms/releases/download/1.2.4/release.zip" --force')
		->andReturnUsing(function ($message) {
			putenv("ES_CLI_RUN_COMMAND_PLUGIN_GH_INSTALL_HAPPENED={$message}");
		});

	$wpCliMock
		->shouldReceive('runcommand')
		->withSomeOfArgs('for f in ./wp-content/plugins/release*/; do rsync -avh --delete "$f" "./wp-content/plugins/eightshift-forms/" && rm -rf "$f"; done')
		->andReturnTrue();

	$wpCliMock
		->shouldReceive('runcommand')
		->withSomeOfArgs('plugin install "https://example.com" --force')
		->andReturnUsing(function ($message) {
			putenv("ES_CLI_RUN_COMMAND_PLUGIN_PAID_1_INSTALL_HAPPENED={$message}");
		});

	$wpCliMock
		->shouldReceive('runcommand')
		->withSomeOfArgs('plugin install "https://example.com&t=5.10.2" --force')
		->andReturnUsing(function ($message) {
			putenv("ES_CLI_RUN_COMMAND_PLUGIN_PAID_2_INSTALL_HAPPENED={$message}");
		});

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
	when('file_exists')->alias(function($argument) {
		if (strpos($argument, 'setup.json') !== false) {
			return false;
		}
	});

	$pluginInstall = $this->pluginInstall;
	$pluginInstall([], []);
})->throws(FileMissing::class);

test('Plugin Install CLI command will correctly throw an exception if env.json does not exist', function () {
	when('file_exists')->alias(function($argument) {
		if (strpos($argument, 'setup.json') !== false) {
			return true;
		}

		if (strpos($argument, 'env.json') !== false) {
			return false;
		}
	});

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
