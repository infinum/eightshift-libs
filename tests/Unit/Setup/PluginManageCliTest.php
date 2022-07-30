<?php

namespace Tests\Unit\Setup;

use EightshiftLibs\Setup\PluginManageCli;

use function Brain\Monkey\Functions\when;
use function Tests\mock;

/**
 * Mock before tests.
 */
beforeEach(function () {
	$this->pluginManage = new PluginManageCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	unset($this->pluginManage);
});

test('Plugin Install CLI command will correctly throw an exception if setup.json does not exist or has the wrong filename', function () {
	when('file_exists')->alias(function($argument) {
		if (strpos($argument, 'setup.json') !== false) {
			return false;
		}
	});

	$pluginManage = $this->pluginManage;
	$pluginManage([], []);
})->expectException(\Exception::class);

test('Plugin Install CLI command will correctly throw an exception if env.json does not exist', function () {
	when('file_exists')->alias(function($argument) {
		if (strpos($argument, 'setup.json') !== false) {
			return true;
		}

		if (strpos($argument, 'env.json') !== false) {
			return false;
		}
	});

	$pluginManage = $this->pluginManage;
	$pluginManage([], []);
})->expectException(\Exception::class);

test('Plugin install CLI documentation is correct', function () {
	expect($this->pluginManage->getDoc())->toBeArray();
});

test('Plugin install CLI command will work with default action', function () {
	// Reset the previously set mock.
	when('file_exists')->justReturn(true);

	$pluginManage = $this->pluginManage;
	$pluginManage([], []);

	expect(getenv('ES_CLI_SUCCESS_HAPPENED'))
		->toBeString()
		->toContain('Plugin eightshift-forms updated');
});

test('Plugin install CLI command will work when only core plugins should be installed', function () {
	$pluginManage = $this->pluginManage;
	$pluginManage([], ['install-core']);

	expect(getenv('ES_CLI_RUN_COMMAND_PLUGIN_INSTALL_QM_HAPPENED'))
		->toBeString()
		->toBe('plugin install query-monitor --force');
});

test('Plugin install CLI command will work when only GitHub plugins should be installed', function () {
	$pluginManage = $this->pluginManage;
	$pluginManage([], ['install-github']);

	expect(getenv('ES_CLI_RUN_COMMAND_PLUGIN_GH_INSTALL_HAPPENED'))
		->toBeString()
		->toBe('plugin install "https://github.com/infinum/eightshift-forms/releases/download/1.2.4/release.zip" --force');
});

test('Plugin install CLI command will work when only paid plugins should be installed', function () {
	$pluginManage = $this->pluginManage;
	$pluginManage([], ['install-paid']);

	expect(getenv('ES_CLI_RUN_COMMAND_PLUGIN_PAID_1_INSTALL_HAPPENED'))
		->toBeString()
		->toBe('plugin install "https://example.com" --force')
		->and(getenv('ES_CLI_RUN_COMMAND_PLUGIN_PAID_2_INSTALL_HAPPENED')) // Version replacement test.
		->toBeString()
		->toBe('plugin install "https://example.com&t=5.10.2" --force');
});

test('Plugins install CLI command will delete plugins', function () {
	$pluginManage = $this->pluginManage;
	$pluginManage([], ['delete-plugins']);

	expect(getenv('ES_CLI_RUN_COMMAND_PLUGIN_DELETE_HAPPENED'))
		->toBeString()
		->toBe('plugin delete wordpress-seo');
});
