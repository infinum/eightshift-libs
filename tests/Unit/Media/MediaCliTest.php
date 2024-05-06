<?php

namespace Tests\Unit\Media;

use EightshiftLibs\Helpers\Helpers;
use EightshiftLibs\Media\MediaCli;

use function Tests\getMockArgs;

beforeEach(function () {
	$this->mock = new MediaCli('boilerplate');
});

afterEach(function () {
	unset($this->mock);
});


test('Media CLI command will correctly copy the Media class with defaults', function () {
	$mock = $this->mock;
	$mock([], getMockArgs());

	// Check the output dir if the generated method is correctly generated.
	$generatedMedia = \file_get_contents(Helpers::getProjectPaths('srcDestination', 'Media/Media.php'));

	$this->assertStringContainsString('class Media extends AbstractMedia', $generatedMedia);
	$this->assertStringContainsString('after_setup_theme', $generatedMedia, 'Created class does not contain after_setup_theme hook');
	$this->assertStringContainsString('addThemeSupport', $generatedMedia, 'Created class does not contain addThemeSupport method');
});

test('Media CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});
