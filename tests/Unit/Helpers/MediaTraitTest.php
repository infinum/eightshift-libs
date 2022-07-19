<?php

namespace Tests\Unit\Helpers;

use Brain\Monkey\Functions;
use EightshiftLibs\Helpers\Components;

test('Check if getWebPMedia will return webp image format.', function() {
	$mock = Components::getWebPMedia('image.jpg');

	expect($mock)
		->toMatchArray([
			'src' => 'image.webp',
			'type' => 'image/webp',
		]);
});

test('Check if getWebPMedia will return empty array if provided extension is not supported.', function() {

	Functions\when('wp_check_filetype')->justReturn([
		'ext' => 'pdf',
		'type' => 'application/pdf',
	]);

	$mock = Components::getWebPMedia('document.pdf');

	expect($mock)
		->toBeArray();
});

test('Check if existsWebPMedia will return false if attachment ID is non existing.', function() {
	$mock = Components::existsWebPMedia(1);

	expect($mock)
		->toBeFalse();
});
