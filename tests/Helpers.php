<?php

namespace Tests;

use Brain\Monkey\Functions;
use Mockery;
use Mockery\MockInterface;

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
	Functions\when('get_template_directory')->justReturn(dirname(__FILE__) . '/data');

	// Mock the template dir location.
	Functions\when('get_stylesheet_directory')->justReturn(dirname(__FILE__) . '/');

	// Mock escaping function.
	Functions\when('wp_kses_post')->returnArg();

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
function deleteCliOutput(string $dir) : void
{
	if (!is_dir($dir)) {
		return;
	}

	$iterator = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS);
	$files = new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::CHILD_FIRST);

	foreach ($files as $file) {
		if ($file->isDir()) {
			rmdir($file->getRealPath());
		} else {
			unlink($file->getRealPath());
		}
	}

	rmdir($dir);
}

function mock(string $class): MockInterface
{
    return Mockery::mock($class);
}
