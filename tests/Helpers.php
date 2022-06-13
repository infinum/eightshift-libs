<?php

namespace Tests;

use Brain\Monkey\Functions;
use Mockery;
use Brain\Monkey;
use Mockery\MockInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * Helper function that will setup some repeating mocks in every tests.
 *
 * This is a way to circumvent the issue I was having described here:
 * https://github.com/pestphp/pest/issues/259
 */
function setupMocks() {
	// Mock WP functions
	Functions\stubTranslationFunctions();
	Functions\stubEscapeFunctions();

	// Mock the template dir location.
	Functions\when('get_template_directory')->justReturn(\dirname(__FILE__) . '/data');

	// Mock the template dir location.
	Functions\when('get_stylesheet_directory')->justReturn(\dirname(__FILE__) . '/');

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

	// Mock wp_get_attachment_metadata function.
	Functions\when('wp_get_attachment_metadata')->justReturn(attachemntMetaDataMock());

	// Mock get_post_meta function.
	Functions\when('get_post_meta')->justReturn('');

	// Mock wp_delete_file function.
	Functions\when('wp_delete_file')->justReturn('');

	// Mock ACF add options page function
	Functions\when('acf_add_options_page')->justReturn(true);

	// Mock ACF add options subpage function
	Functions\when('acf_add_options_sub_page')->justReturn(true);

	// Mock ACF add local field group function
	Functions\when('acf_add_local_field_group')->justReturn(true);

	// Mock current_user_can function.
	Functions\when('current_user_can')->returnArg();

	// Mock get_field function.
	Functions\when('get_field')->returnArg();

	$wpCliMock = mock('alias:WP_CLI');

	$wpCliMock
		->shouldReceive('success')
		->andReturnUsing(function ($message) {
			putenv("ES_CLI_SUCCESS_HAPPENED={$message}");
		});

	$wpCliMock
		->shouldReceive('error')
		->andReturnUsing(function ($message) {
			putenv("ES_CLI_ERROR_HAPPENED={$message}");
		});

	$wpCliMock
		->shouldReceive('log')
		->andReturnUsing(function ($message) {
			putenv("ES_CLI_LOG_HAPPENED={$message}");
		});

	$wpCliMock
		->shouldReceive('runcommand')
		->andReturnUsing(function ($message) {
			putenv("ES_CLI_RUN_COMMAND_HAPPENED={$message}");
		});

	$wpCliMock
		->shouldReceive('add_command')
		->andReturnUsing(function ($message) {
			putenv("ES_CLI_ADD_COMMAND_HAPPENED={$message}");
		});

	// Mock attachment function.
	Functions\when('get_attached_file')->justReturn('test.jpg');

	// Mock attachment function.
	Functions\when('wp_check_filetype')->justReturn([
		'ext' => 'jpg',
		'type' => 'image/jpeg',
	]);

	if (!defined('DAY_IN_SECONDS')) {
		define('DAY_IN_SECONDS', 3600);
	}

	Functions\when('is_admin')->justReturn(false);

	Functions\when('setcookie')->alias(function($name, $value) {
		putenv("ES_SIDEAFFECT={$name}");
		putenv("ES_SIDEAFFECT_ADDITIONAL={$value}");
	});
}

/**
 * Set everything before every test.
 *
 * @return void
 */
function setBeforeEach() {
	Monkey\setUp();
	setupMocks();
}

/**
 * Clear everything after each test.
 *
 * @return void
 */
function setAfterEach($delete = true) {
	Monkey\tearDown();

	if ($delete) {
		deleteCliOutput();
	}

	putenv('ES_SIDEAFFECT');
	putenv('ES_SIDEAFFECT_ADDITIONAL');
	putenv('ES_CLI_SUCCESS_HAPPENED');
	putenv('ES_CLI_ERROR_HAPPENED');
	putenv('ES_CLI_LOG_HAPPENED');
	putenv('ES_CLI_RUNCOMMAND_HAPPENED');
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
 * Get path to data mocks.
 *
 * @param string $path Path to attach.
 *
 * @return string
 */
function getDataPath(string $path = ''): string
{
	return __DIR__ . "/data/{$path}";
}

/**
 * Mock Mockery interface.
 *
 * @param string $class Class to mock.
 *
 * @return MockInterface
 */
function mock(string $class): MockInterface
{
	return Mockery::mock($class);
}
