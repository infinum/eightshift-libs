<?php

// Autoload things.
require_once dirname(__FILE__, 2) . '/vendor/autoload.php';

// Include core bootstrap for integration test suite.
if (isset($GLOBALS['argv']) && isset($GLOBALS['argv'][1]) && strpos($GLOBALS['argv'][1], 'integration') !== false) {
	// We need to set up core config details and test details

	require_once dirname(__FILE__, 2) . '/wp/tests/phpunit/includes/bootstrap.php';
}
