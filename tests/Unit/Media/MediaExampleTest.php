<?php

namespace Tests\Unit\Media;

use Brain\Monkey\Functions;
use EightshiftLibs\Media\MediaCli;
use Infinum\Media\Media;

use function Tests\getMockArgs;
use function Tests\reqOutputFiles;

beforeEach(function() {
	$mediaCliMock = new MediaCli('boilerplate');
	$mediaCliMock([], getMockArgs($mediaCliMock->getDefaultArgs()));

	reqOutputFiles(
		'Media/Media.php',
	);
});

test('Register method will call init hook', function () {
	(new Media())->register();

	$this->assertSame(20, has_action('after_setup_theme', 'Infinum\Media\Media->addThemeSupport()'));
	$this->assertSame(10, has_filter('wp_generate_attachment_metadata', 'Infinum\Media\Media->generateWebPMedia()'), 2);
	$this->assertSame(10, has_filter('wp_update_attachment_metadata', 'Infinum\Media\Media->generateWebPMedia()'), 2);
	$this->assertSame(10, has_action('delete_attachment', 'Infinum\Media\Media->deleteWebPMedia()'));
});

test('addThemeSupport method will call add_theme_support() function with different arguments', function () {
	Functions\when('add_theme_support')->alias(function($arg) {
		$envName = strtoupper($arg);
		$envName = \str_replace('-', '_', $envName);
		putenv("{$envName}=true");
	});

	(new Media())->addThemeSupport();

	$this->assertSame(\getenv('TITLE_TAG'), 'true', "Method addThemeSupport() didn't add theme support for title-tag");
	$this->assertSame(\getenv('HTML5'), 'true', "Method addThemeSupport() didn't add theme support for html5");
	$this->assertSame(\getenv('POST_THUMBNAILS'), 'true', "Method addThemeSupport() didn't add theme support for post-thumbnails");

});
