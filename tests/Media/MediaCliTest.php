<?php

namespace Tests\Unit\Media;

use EightshiftLibs\Media\MediaCli;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mediaCli = new MediaCli('boilerplate');
});

afterEach(function () {
	setAfterEach();
});


test('Media CLI command will correctly copy the Media class with defaults', function () {
	$mock = $this->mediaCli;
	$mock([], $mock->getDefaultArgs());

	// Check the output dir if the generated method is correctly generated.
	$generatedMedia = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/Media/Media.php');

	$this->assertStringContainsString('class Media extends AbstractMedia', $generatedMedia);
	$this->assertStringContainsString('after_setup_theme', $generatedMedia, 'Created class does not contain after_setup_theme hook');
	$this->assertStringContainsString('addThemeSupport', $generatedMedia, 'Created class does not contain addThemeSupport method');
});

test('Media CLI documentation is correct', function () {
	expect($this->mediaCli->getDoc())->toBeArray();
});
