<?php

namespace Tests\Unit\Media;

use Brain\Monkey\Functions;
use EightshiftLibs\Helpers\Components;
use EightshiftLibs\Media\AbstractMedia;


class AbstractMediaTest extends AbstractMedia {
	public function register(): void
	{

	}
};

beforeEach(function () {
	$this->mockMedia = attachemntMetaDataMock();
	$this->mockPath = Components::getProjectPaths('testsData', 'media');
	$this->mockFileName = 'test';

	Functions\when('get_attached_file')->justReturn("{$this->mockPath}/{$this->mockFileName}.png");
	Functions\when('wp_check_filetype')->justReturn([
		'ext' => 'png',
		'type' => 'image/png',
	]);
});

afterEach(function () {
	$mockSizes = [
		"{$this->mockPath}/{$this->mockFileName}.webp",
		"{$this->mockPath}/{$this->mockFileName}-150x150.webp",
	];

	foreach ($mockSizes as $size) {
		if (\file_exists($size)) {
			unlink($size);
		}
	}
});

test('Check if generateWebPMedia will return provided metadata.', function() {
	$mock = new AbstractMediaTest();
	$mockMedia = $this->mockMedia;

	$mock = $mock->generateWebPMedia($mockMedia, 10);

	expect($mock)
		->toBeArray()
		->toMatchArray($mockMedia);
});

test('Check if deleteWebPMedia will return void.', function() {
	$mock = new AbstractMediaTest();

	$mock = $mock->deleteWebPMedia(10);

	expect($mock)->toBeNull();
});

test('Check if generateWebPMediaOriginal will return empty string if file is missing.', function() {
	Functions\when('get_attached_file')->justReturn('');

	$mock = new AbstractMediaTest();

	$mock = $mock->generateWebPMediaOriginal(10, 80);

	expect($mock)
		->toBeString()
		->toEqual('');
});

test('Check if generateWebPMediaOriginal will return media path name with WebP extension - png.', function() {
	$path = $this->mockPath;
	$fileName = $this->mockFileName;

	$mock = new AbstractMediaTest();

	$mock = $mock->generateWebPMediaOriginal(10, 80);

	expect($mock)
		->toBeString()
		->toEqual("{$path}/{$fileName}.webp");
});

test('Check if generateWebPMediaOriginal will return media path name with WebP extension - jpg & jpeg.', function() {
	$path = $this->mockPath;
	$fileName = $this->mockFileName;

	Functions\when('get_attached_file')->justReturn("{$this->mockPath}/{$this->mockFileName}.jpg");
	Functions\when('wp_check_filetype')->justReturn([
		'ext' => 'jpg',
		'type' => 'image/jpg',
	]);

	$mock = new AbstractMediaTest();

	$mock = $mock->generateWebPMediaOriginal(10, 80);

	expect($mock)
		->toBeString()
		->toEqual("{$path}/{$fileName}.webp");
});

test('Check if generateWebPMediaOriginal will return media path name with WebP extension - bmp.', function() {
	$path = $this->mockPath;
	$fileName = $this->mockFileName;

	Functions\when('get_attached_file')->justReturn("{$this->mockPath}/{$this->mockFileName}.bmp");
	Functions\when('wp_check_filetype')->justReturn([
		'ext' => 'bmp',
		'type' => 'image/bmp',
	]);

	$mock = new AbstractMediaTest();

	$mock = $mock->generateWebPMediaOriginal(10, 80);

	expect($mock)
		->toBeString()
		->toEqual("{$path}/{$fileName}.webp");
});

test('Check if generateWebPMediaOriginal will return media path name with WebP extension - gif.', function() {
	$path = $this->mockPath;
	$fileName = $this->mockFileName;

	Functions\when('get_attached_file')->justReturn("{$this->mockPath}/{$this->mockFileName}.gif");
	Functions\when('wp_check_filetype')->justReturn([
		'ext' => 'gif',
		'type' => 'image/gif',
	]);

	$mock = new AbstractMediaTest();

	$mock = $mock->generateWebPMediaOriginal(10, 80);

	expect($mock)
		->toBeString()
		->toEqual("{$path}/{$fileName}.webp");
});

test('Check if generateWebPMediaOriginal will return empty string if file allready exists.', function() {
	Functions\when('get_attached_file')->justReturn("{$this->mockPath}/new.webp");

	$mock = new AbstractMediaTest();

	$mock = $mock->generateWebPMediaOriginal(10, 80, false);

	expect($mock)
		->toBeString()
		->toEqual('');
});

test('Check if generateWebPMediaOriginal will return empty string if file is not supported to convert.', function() {
	Functions\when('get_attached_file')->justReturn("{$this->mockPath}/test.md");
	Functions\when('wp_check_filetype')->justReturn([
		'ext' => 'md',
		'type' => 'text/plain',
	]);

	$mock = new AbstractMediaTest();

	$mock = $mock->generateWebPMediaOriginal(10, 80, true);

	expect($mock)
		->toBeString()
		->toEqual('');
});

test('Check if generateWebPMediaOriginal will return empty string if file can\'t be converted.', function() {
	Functions\when('get_attached_file')->justReturn("{$this->mockPath}/brokenExt.bmp");
	Functions\when('wp_check_filetype')->justReturn([
		'ext' => 'bmp',
		'type' => 'image/jpeg',
	]);

	$mock = new AbstractMediaTest();

	expect($mock->generateWebPMediaOriginal(10, 80, true))
		->toBeString()
		->toEqual('');

	Functions\when('wp_check_filetype')->justReturn([
		'ext' => 'png',
		'type' => 'image/jpeg',
	]);

	expect($mock->generateWebPMediaOriginal(10, 80, true))
		->toBeString()
		->toEqual('');

	Functions\when('wp_check_filetype')->justReturn([
		'ext' => 'gif',
		'type' => 'image/jpeg',
	]);

	expect($mock->generateWebPMediaOriginal(10, 80, true))
		->toBeString()
		->toEqual('');

	Functions\when('wp_check_filetype')->justReturn([
		'ext' => 'bmp',
		'type' => 'image/jpeg',
	]);

	expect($mock->generateWebPMediaOriginal(10, 80, true))
		->toBeString()
		->toEqual('');

	Functions\when('wp_check_filetype')->justReturn([
		'ext' => 'jpg',
		'type' => 'image/png',
	]);

	expect($mock->generateWebPMediaOriginal(10, 80, true))
		->toBeString()
		->toEqual('');
});

test('Check if generateWebPMediaAllSizes will return empty array if file is missing.', function() {
	Functions\when('get_attached_file')->justReturn('');

	$mock = new AbstractMediaTest();

	$mock = $mock->generateWebPMediaAllSizes(10, 80);

	expect($mock)
		->toBeArray()
		->toMatchArray([]);
});

test('Check if generateWebPMediaAllSizes will skip all sizes with no file key.', function() {
	$mockMetaData = attachemntMetaDataBrokenMock();
	Functions\when('wp_get_attachment_metadata')->justReturn($mockMetaData);

	$mock = new AbstractMediaTest();

	$mock = $mock->generateWebPMediaAllSizes(10, 80);

	expect($mock)
		->toBeArray()
		->toMatchArray([]);
});

test('Check if generateWebPMediaAllSizes will return empty array if file sizes are missing.', function() {
	$mockMetaData = attachemntMetaDataMock();
	unset($mockMetaData['sizes']);
	Functions\when('wp_get_attachment_metadata')->justReturn($mockMetaData);

	$mock = new AbstractMediaTest();

	$mock = $mock->generateWebPMediaAllSizes(10, 80);

	expect($mock)
		->toBeArray()
		->toMatchArray([]);
});

test('Check if deleteWebPMediaOriginal will return empty string if file is missing.', function() {
	Functions\when('get_attached_file')->justReturn('');

	$mock = new AbstractMediaTest();

	$mock = $mock->deleteWebPMediaOriginal(10);

	expect($mock)
		->toBeString()
		->toEqual('');
});

test('Check if deleteWebPMediaOriginal will return media path name with WebP extension if file deleted.', function() {
	$path = $this->mockPath;
	$fileName = $this->mockFileName;

	$mock = new AbstractMediaTest();

	$mock = $mock->deleteWebPMediaOriginal(10);

	expect($mock)
		->toBeString()
		->toEqual("{$path}/{$fileName}.webp");
});

test('Check if deleteWebPMediaAllSizes will return empty array if file is missing.', function() {
	Functions\when('get_attached_file')->justReturn('');

	$mock = new AbstractMediaTest();

	$mock = $mock->deleteWebPMediaAllSizes(10);

	expect($mock)
		->toBeArray()
		->toMatchArray([]);
});

test('Check if deleteWebPMediaAllSizes will skip all sizes with no file key.', function() {
	$mockMetaData = attachemntMetaDataBrokenMock();
	Functions\when('wp_get_attachment_metadata')->justReturn($mockMetaData);

	$mock = new AbstractMediaTest();

	$mock = $mock->deleteWebPMediaAllSizes(10);

	expect($mock)
		->toBeArray()
		->toMatchArray([]);
});

test('Check if deleteWebPMediaAllSizes returns array with sizes.', function() {
	Functions\when('get_attached_file')->justReturn("{$this->mockPath}/{$this->mockFileName}.png");
	Functions\when('get_post_meta')->justReturn(attachemntMetaDataMock());

	$mock = new AbstractMediaTest();

	$mock = $mock->deleteWebPMediaAllSizes(10);

	expect($mock)
		->toBeArray()
		->toHaveKey('thumbnail');
});
