<?php

namespace Tests\Unit\Media;

use EightshiftLibs\Media\MediaCli;

use function Tests\deleteCliOutput;
use function Tests\mock;

/**
 * Mock before tests.
 */
beforeEach(function () {
	$wpCliMock = mock('alias:WP_CLI');

	$wpCliMock
		->shouldReceive('success')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('error')
		->andReturnArg(0);

	$this->media = new MediaCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	deleteCliOutput();
});


test('Media CLI command will correctly copy the Media class with defaults', function () {
	$media = $this->media;
	$media([], $media->getDevelopArgs([]));

	// Check the output dir if the generated method is correctly generated.
	$generatedMedia = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/Media/Media.php');

	$this->assertStringContainsString('class Media extends AbstractMedia', $generatedMedia);
	$this->assertStringContainsString('after_setup_theme', $generatedMedia, 'Created class does not contain after_setup_theme hook');
	$this->assertStringContainsString('addThemeSupport', $generatedMedia, 'Created class does not contain addThemeSupport method');
});

test('Media CLI documentation is correct', function () {
	$media = $this->media;

	$documentation = $media->getDoc();

	$key = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($key, $documentation);
	$this->assertArrayNotHasKey('synopsis', $documentation);
	$this->assertSame('Generates media class.', $documentation[$key]);

});
