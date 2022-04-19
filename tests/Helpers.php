<?php

namespace Tests;

use Brain\Monkey\Functions;
use EightshiftBoilerplate\Main\MainExample;
use EightshiftLibs\Config\ConfigCli;
use EightshiftLibs\Main\MainCli;
//use EightshiftLibsTest\Main\Main;
use Mockery;
use Mockery\MockInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Helper function that will setup some repeating mocks in every tests.
 *
 * This is a way to circumvent the issue I was having described here:
 * https://github.com/pestphp/pest/issues/259
 */
function setupUnitTestMocks() {
	// Mock WP functions
	Functions\stubTranslationFunctions();
	Functions\stubEscapeFunctions();

	// Mock the template dir location.
	Functions\when('get_template_directory')->justReturn(\dirname(__FILE__) . '/Unit/data');

	// Mock the template dir location.
	Functions\when('get_stylesheet_directory')->justReturn(\dirname(__FILE__) . '/Unit/');

	// Mock escaping function.
	Functions\when('wp_kses_post')->returnArg();

	// Mock escaping function.
	Functions\when('esc_html__')->returnArg();

	// Mock rand function.
	Functions\when('wp_rand')->justReturn(1154790670);

	// Mock json success and error handlers.
	Functions\when('wp_send_json_success')->alias(function ($data = null, $statusCode = null, $options = 0) {
		$response = ['success' => true];

		if (isset($data)) {
			$response['data'] = $data;
		}

		echo json_encode($response, $options);
	});

	Functions\when('wp_send_json_error')->alias(function($data = null, $statusCode = null, $options = 0) {
		$response = ['success' => false];

		if (isset($data)) {
			$response['data'] = $data;
		}

	    echo json_encode($response, $options);
	});

	// Mock rest response handler.
	Functions\when('rest_ensure_response')->returnArg();

	// Mock site_url function.
	Functions\when('site_url')->justReturn('https://example.com');
}

/**
 * Used for cleaning out the cliOutput created after every CLI test
 *
 * @param string $dir Directory to remove.
 *
 * @return void
 */
function deleteCliOutput(string $dir = '') : void
{
	if (!$dir) {
		$dir = \dirname(__FILE__, 2) . '/cliOutput';
	}

	if (!\is_dir($dir)) {
		return;
	}

	$iterator = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
	$files = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST);

	foreach ($files as $file) {
		if ($file->isDir()) {
			rmdir($file->getRealPath());
		} else {
			unlink($file->getRealPath());
		}
	}

	rmdir($dir);
}

/**
 * Mockery shorthand
 *
 * @param string $class Class name to mock.
 *
 * @return \Mockery\MockInterface
 */
function mock(string $class): MockInterface
{
	return Mockery::mock($class);
}

/**
 * Helper that will run setup theme command and bootstrap the theme
 *
 * @return void
 */
function setupTheme()
{
//	$config = new ConfigCli('boilerplate');
//	$config([], $config->getDevelopArgs([]));
//
//	$main = new MainCli('boilerplate');
//	$main([], $main->getDevelopArgs([]));
//
//	$configFile = dirname(__FILE__, 2) . '/cliOutput/src/Config/Config.php';
//	$mainFile = dirname(__FILE__, 2) . '/cliOutput/src/Main/Main.php';
//
//	// Conflict prevention with the libs.
//	replaceStringInFile($configFile);
//	replaceStringInFile($mainFile);

	$loader = require dirname(__FILE__, 2) . '/vendor/autoload.php';

	(new MainExample($loader->getPrefixesPsr4(), 'EightshiftBoilerplate'))->register();
}

/**
 * Helper method that will replace strings in a file
 *
 * Used to replace the namespaces and imports in integration test classes,
 * so that we can actually test the added functionality.
 *
 * @param string $fileLocation String file location.
 * @param string $original Original string to replace.
 * @param string $replacement Replacement string.
 *
 * @return void
 */
function replaceStringInFile(
	string $fileLocation,
	string $original = 'namespace EightshiftLibs',
	string $replacement = 'namespace EightshiftLibsTest')
{
	if (file_exists($fileLocation)) {
		$content = file_get_contents($fileLocation);
		$content = str_replace($original, $replacement, $content);

		file_put_contents($fileLocation, $content);
	}
}
