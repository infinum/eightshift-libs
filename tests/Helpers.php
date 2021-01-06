<?php

namespace Tests;

use Brain\Monkey\Functions;

// Mock WP functions
Functions\stubTranslationFunctions();
Functions\stubEscapeFunctions();

// Mock the template dir location.
Functions\when('get_template_directory')->justReturn(dirname(__FILE__) . '/data');

// Mock escaping function.
Functions\when('wp_kses_post')->returnArg();

// Mock json success and error handlers.
Functions\when('wp_send_json_success')->alias(function(...$args) {
    echo json_encode($args);
});
Functions\when('wp_send_json_error')->alias(function(...$args) {
    echo json_encode($args);
});

// Mock rest response handler.
Functions\when('rest_ensure_response')->returnArg();

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
