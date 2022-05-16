<?php

namespace Tests\Unit\Helpers;

use EightshiftLibs\Helpers\Components;

use Brain\Monkey\Functions;
use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function() {
	setBeforeEach();
});

afterEach(function() {
	setAfterEach();
});

test('Check if getWebPMedia will return webp image format.', function() {
	$mock = Components::getWebPMedia('image.jpg');

	expect($mock)
		->toBeString('image.webp');
});

test('Check if getWebPMedia will return empty string if provided extension is not supported.', function() {

	Functions\when('wp_check_filetype')->justReturn([
		'ext' => 'pdf',
		'type' => 'application/pdf',
	]);

	$mock = Components::getWebPMedia('document.pdf');

	expect($mock)
		->toBeString('');
});

test('Check if existsWebPMedia will return false if attachment ID is non existing.', function() {
	$mock = Components::existsWebPMedia(1);

	expect($mock)
		->toBeFalse();
});
