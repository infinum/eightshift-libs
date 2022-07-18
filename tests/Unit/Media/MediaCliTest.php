<?php

namespace Tests\Unit\Media;

use EightshiftLibs\Helpers\Components;
use EightshiftLibs\Media\MediaCli;

beforeEach(function () {
	$this->mediaCli = new MediaCli('boilerplate');
});


test('Media CLI command will correctly copy the Media class with defaults', function () {
	$mock = $this->mediaCli;
	$mock([], $mock->getDefaultArgs());

	// Check the output dir if the generated method is correctly generated.
	$generatedMedia = \file_get_contents(Components::getProjectPaths('srcDestination', 'Media/Media.php'));

	$this->assertStringContainsString('class Media extends AbstractMedia', $generatedMedia);
	$this->assertStringContainsString('after_setup_theme', $generatedMedia, 'Created class does not contain after_setup_theme hook');
	$this->assertStringContainsString('addThemeSupport', $generatedMedia, 'Created class does not contain addThemeSupport method');
});

test('Media CLI documentation is correct', function () {
	expect($this->mediaCli->getDoc())->toBeArray();
});
