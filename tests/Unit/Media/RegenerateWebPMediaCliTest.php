<?php

namespace Tests\Unit\Media;

use EightshiftLibs\Media\RegenerateWebPMediaCli;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

/**
 * Mock before tests.
 */
beforeEach(function () {
	setBeforeEach();

	$this->regenerateWebPMediaCli = new RegenerateWebPMediaCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	setAfterEach();
});

test('Check if CLI command documentation is correct.', function () {
	expect($this->regenerateWebPMediaCli->getDoc())->toBeArray();
});
