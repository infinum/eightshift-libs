<?php

// Autoload everything for unit tests.
require_once dirname(__FILE__, 2) . '/vendor/autoload.php';

/**
 * Include core bootstrap for an integration test suite
 *
 * This will only work if you run the tests from the command line.
 * Running the tests from IDE such as PhpStorm will require you to
 * add additional argument to the test run command if you want to run
 * integration tests.
 */
if (isset($GLOBALS['argv']) && isset($GLOBALS['argv'][1]) && strpos($GLOBALS['argv'][1], 'integration') !== false) {
	if (!file_exists(dirname(__FILE__, 2) . '/wp/tests/phpunit/wp-tests-config.php')) {
		// We need to set up core config details and test details
		copy(dirname(__FILE__, 2) . '/wp/wp-tests-config-sample.php', dirname(__FILE__, 2) . '/wp/tests/phpunit/wp-tests-config.php');

		// Change certain constants from the test's config file.
		$testConfigPath = dirname(__FILE__, 2) . '/wp/tests/phpunit/wp-tests-config.php';
		$testConfigContents = file_get_contents($testConfigPath);

		$testConfigContents = str_replace("dirname( __FILE__ ) . '/src/'", "dirname(__FILE__, 3) . '/src/'", $testConfigContents);
		$testConfigContents = str_replace("youremptytestdbnamehere", $_SERVER['DB_NAME'], $testConfigContents);
		$testConfigContents = str_replace("yourusernamehere", $_SERVER['DB_USER'], $testConfigContents);
		$testConfigContents = str_replace("yourpasswordhere", $_SERVER['DB_PASSWORD'], $testConfigContents);
		$testConfigContents = str_replace("localhost", $_SERVER['DB_HOST'], $testConfigContents);

		file_put_contents($testConfigPath, $testConfigContents);
	}

	// Give access to tests_add_filter() function.
	require_once dirname(__FILE__, 2) . '/wp/tests/phpunit/includes/functions.php';

	/**
	 * Register mock theme.
	 */
	function _register_theme()
	{
		$themeDir = dirname(__FILE__, 2);
		$currentTheme = basename($themeDir);
		$themeRoot = dirname($themeDir);

		add_filter('theme_root', function () use ($themeRoot) {
			return $themeRoot;
		});

		register_theme_directory($themeRoot);

		add_filter('pre_option_template', function () use ($currentTheme) {
			return $currentTheme;
		});

		add_filter('pre_option_stylesheet', function () use ($currentTheme) {
			return $currentTheme;
		});
	}

	tests_add_filter( 'muplugins_loaded', '_register_theme' );

	// Modify the bootstrap so that Pest works.
	$basicBootstrapPath = dirname(__FILE__, 2) . '/wp/tests/phpunit/includes/bootstrap.php';
	$basicBootstrapContents = file_get_contents($basicBootstrapPath);

	if (strpos($basicBootstrapContents, "require __DIR__ . '/phpunit-adapter-testcase.php';") !== false) {
		// We need to remove these because we already included them in Pest.php
		$basicBootstrapContents = str_replace("require __DIR__ . '/phpunit-adapter-testcase.php';", '', $basicBootstrapContents);
		$basicBootstrapContents = str_replace("require __DIR__ . '/abstract-testcase.php';", '', $basicBootstrapContents);
		$basicBootstrapContents = str_replace("require __DIR__ . '/testcase.php';", '', $basicBootstrapContents);

		file_put_contents($basicBootstrapPath, $basicBootstrapContents);
	}

	require_once $basicBootstrapPath;
}
