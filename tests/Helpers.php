<?php

namespace Tests;

use Brain\Monkey\Functions;
use EightshiftLibs\Config\ConfigCli;
use EightshiftLibs\Main\MainCli;
use FilesystemIterator;
use Mockery;
use Mockery\MockInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Helper function that will set up some repeating mocks in every test.
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

	$fs = new Filesystem();
	$fs->remove($dir);
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
	// Set up the Main file in the cliOutput.
	$main = new MainCli('boilerplate');
	$main([], $main->getDevelopArgs([]));
	$config = new ConfigCli('boilerplate');
	$config([], $config->getDevelopArgs([]));

	// Create functions.php file on the fly, inside the cliOutput folder.
	copy(dirname(__FILE__) . '/Stubs/style.css', dirname(__DIR__) . '/cliOutput/style.css');
	copy(dirname(__FILE__) . '/Stubs/functions.php', dirname(__DIR__) . '/cliOutput/functions.php');
	copy(dirname(__FILE__) . '/Stubs/index.php', dirname(__DIR__) . '/cliOutput/index.php');

	// Go through each file in cliOutput and change 'namespace EightshiftLibs' with 'namespace Testing'.
	// Change EightshiftBoilerplate with Testing.
	$outputFolder = dirname(__DIR__) . '/cliOutput';
	$iterator = new RecursiveDirectoryIterator($outputFolder, FilesystemIterator::SKIP_DOTS);
	$files = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST);

	foreach ($files as $file) {
		if (!$file->isDir()) {
			$content = file_get_contents($file->getRealPath());
			$content = str_replace('namespace EightshiftLibs', 'namespace Testing', $content);
			$content = str_replace('EightshiftBoilerplate', 'Testing', $content);
			file_put_contents($file->getRealPath(), $content);
		}
	}

	// Move all files and folders to a sub folder called 'testing'.
	$fs = getFilesystem();

	$themeDir = dirname(__DIR__) . '/themes/testing';
	$fs->mirror($outputFolder, $themeDir);
	$fs->remove($outputFolder);

	register_theme_directory(dirname(__DIR__) . '/themes');
	switch_theme('testing');
}

/**
 * Helper to clean theme files after creation
 *
 * @return void
 */
function deleteTheme(): void {
	$fs = getFilesystem();
	$fs->remove(dirname(__DIR__) . '/themes');
}

/**
 * Memoization helper for filesystem
 *
 * @return mixed|Filesystem
 */
function getFilesystem() {
	static $fs;

	if (empty($fs)) {
		$fs = new Filesystem();
	}

	return $fs;
}
