<?php

/**
 * Tests for AbstractMedia class
 *
 * @package EightshiftLibs\Tests\Unit\Media
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Media;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftLibs\Media\AbstractMedia;
use EightshiftLibs\Services\ServiceInterface;
use EightshiftLibs\Tests\BaseTestCase;

/**
 * AbstractMediaTest class
 */
class AbstractMediaTest extends BaseTestCase
{
	/**
	 * Set up before each test
	 *
	 * @return void
	 */
	protected function setUp(): void
	{
		parent::setUp();
		Monkey\setUp();
	}

	/**
	 * Tear down after each test
	 *
	 * @return void
	 */
	protected function tearDown(): void
	{
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Test that AbstractMedia implements ServiceInterface
	 *
	 * @return void
	 */
	public function testImplementsServiceInterface(): void
	{
		$media = new ConcreteMedia();

		$this->assertInstanceOf(ServiceInterface::class, $media);
	}

	/**
	 * Test that enableMimeTypes method exists
	 *
	 * @return void
	 */
	public function testEnableMimeTypesMethodExists(): void
	{
		$media = new ConcreteMedia();

		$this->assertTrue(\method_exists($media, 'enableMimeTypes'));
		$this->assertTrue(\is_callable([$media, 'enableMimeTypes']));
	}

	/**
	 * Test that enableMimeTypes adds SVG and JSON
	 *
	 * @return void
	 */
	public function testEnableMimeTypesAddsSvgAndJson(): void
	{
		$media = new ConcreteMedia();
		$mimes = ['jpg' => 'image/jpeg'];

		$result = $media->enableMimeTypes($mimes);

		$this->assertArrayHasKey('svg', $result);
		$this->assertArrayHasKey('json', $result);
		$this->assertEquals('image/svg+xml', $result['svg']);
		$this->assertEquals('application/json', $result['json']);
	}

	/**
	 * Test that enableSvgMediaLibraryPreview method exists
	 *
	 * @return void
	 */
	public function testEnableSvgMediaLibraryPreviewMethodExists(): void
	{
		$media = new ConcreteMedia();

		$this->assertTrue(\method_exists($media, 'enableSvgMediaLibraryPreview'));
		$this->assertTrue(\is_callable([$media, 'enableSvgMediaLibraryPreview']));
	}

	/**
	 * Test that validateSvgOnUpload method exists
	 *
	 * @return void
	 */
	public function testValidateSvgOnUploadMethodExists(): void
	{
		$media = new ConcreteMedia();

		$this->assertTrue(\method_exists($media, 'validateSvgOnUpload'));
		$this->assertTrue(\is_callable([$media, 'validateSvgOnUpload']));
	}

	/**
	 * Test that enableSvgUpload method exists
	 *
	 * @return void
	 */
	public function testEnableSvgUploadMethodExists(): void
	{
		$media = new ConcreteMedia();

		$this->assertTrue(\method_exists($media, 'enableSvgUpload'));
		$this->assertTrue(\is_callable([$media, 'enableSvgUpload']));
	}

	/**
	 * Test that enableSvgUpload sets correct type for SVG files
	 *
	 * @return void
	 */
	public function testEnableSvgUploadSetsCorrectTypeForSvg(): void
	{
		$media = new ConcreteMedia();
		$data = ['ext' => '', 'type' => ''];

		$result = $media->enableSvgUpload($data, '/tmp/file.svg', 'image.svg');

		$this->assertEquals('svg', $result['ext']);
		$this->assertEquals('image/svg+xml', $result['type']);
	}

	/**
	 * Test that enableJsonUpload method exists
	 *
	 * @return void
	 */
	public function testEnableJsonUploadMethodExists(): void
	{
		$media = new ConcreteMedia();

		$this->assertTrue(\method_exists($media, 'enableJsonUpload'));
		$this->assertTrue(\is_callable([$media, 'enableJsonUpload']));
	}

	/**
	 * Test that enableJsonUpload sets correct type for JSON files
	 *
	 * @return void
	 */
	public function testEnableJsonUploadSetsCorrectTypeForJson(): void
	{
		$media = new ConcreteMedia();
		$data = ['ext' => '', 'type' => ''];

		$result = $media->enableJsonUpload($data, '/tmp/file.json', 'data.json');

		$this->assertEquals('json', $result['ext']);
		$this->assertEquals('application/json', $result['type']);
	}

	/**
	 * Test that enableSvgUpload does not modify data for non-SVG files
	 *
	 * @return void
	 */
	public function testEnableSvgUploadDoesNotModifyNonSvgFiles(): void
	{
		$media = new ConcreteMedia();
		$data = ['ext' => 'png', 'type' => 'image/png'];

		$result = $media->enableSvgUpload($data, '/tmp/file.png', 'image.png');

		$this->assertEquals('png', $result['ext']);
		$this->assertEquals('image/png', $result['type']);
	}

	/**
	 * Test that enableJsonUpload does not modify data for non-JSON files
	 *
	 * @return void
	 */
	public function testEnableJsonUploadDoesNotModifyNonJsonFiles(): void
	{
		$media = new ConcreteMedia();
		$data = ['ext' => 'txt', 'type' => 'text/plain'];

		$result = $media->enableJsonUpload($data, '/tmp/file.txt', 'data.txt');

		$this->assertEquals('txt', $result['ext']);
		$this->assertEquals('text/plain', $result['type']);
	}

	/**
	 * Test that enableMimeTypes preserves existing mimes
	 *
	 * @return void
	 */
	public function testEnableMimeTypesPreservesExistingMimes(): void
	{
		$media = new ConcreteMedia();
		$mimes = ['jpg' => 'image/jpeg', 'png' => 'image/png'];

		$result = $media->enableMimeTypes($mimes);

		$this->assertArrayHasKey('jpg', $result);
		$this->assertArrayHasKey('png', $result);
		$this->assertEquals('image/jpeg', $result['jpg']);
		$this->assertEquals('image/png', $result['png']);
	}

	/**
	 * Test that enableSvgMediaLibraryPreview returns unchanged response for non-SVG
	 *
	 * @return void
	 */
	public function testEnableSvgMediaLibraryPreviewReturnsUnchangedForNonSvg(): void
	{
		$media = new ConcreteMedia();
		$response = [
			'type' => 'image',
			'subtype' => 'jpeg',
			'url' => 'http://example.com/image.jpg',
		];

		$result = $media->enableSvgMediaLibraryPreview($response, 1);

		$this->assertEquals($response, $result);
	}

	/**
	 * Test that validateSvgOnUpload returns unchanged response for non-SVG
	 *
	 * @return void
	 */
	public function testValidateSvgOnUploadReturnsUnchangedForNonSvg(): void
	{
		$media = new ConcreteMedia();
		$response = [
			'type' => 'image/jpeg',
			'tmp_name' => '/tmp/phpXxx',
			'name' => 'image.jpg',
		];

		$result = $media->validateSvgOnUpload($response);

		$this->assertEquals($response, $result);
	}

	/**
	 * Test that getMediaWebPQuality returns default value
	 *
	 * @return void
	 */
	public function testGetMediaWebPQualityReturnsDefault(): void
	{
		$media = new ConcreteMedia();

		$reflection = new \ReflectionMethod($media, 'getMediaWebPQuality');

		$this->assertEquals(80, $reflection->invoke($media));
	}

	/**
	 * Test that getWebPAllowedExt returns expected extensions
	 *
	 * @return void
	 */
	public function testGetWebPAllowedExtReturnsExpectedExtensions(): void
	{
		$media = new ConcreteMedia();

		$reflection = new \ReflectionMethod($media, 'getWebPAllowedExt');
		$result = $reflection->invoke($media);

		$this->assertEquals(['jpg', 'jpeg', 'png', 'bmp'], $result);
	}

	/**
	 * Test convertMediaToWebP returns upload unchanged for non-allowed extension
	 *
	 * @return void
	 */
	public function testConvertMediaToWebPReturnsUnchangedForNonAllowedExt(): void
	{
		$media = new ConcreteMedia();

		$upload = [
			'file' => '/tmp/uploads/document.pdf',
			'url' => 'https://example.com/document.pdf',
			'type' => 'application/pdf',
		];

		$result = $media->convertMediaToWebP($upload);

		$this->assertSame($upload, $result);
	}

	/**
	 * Test convertMediaToWebP returns upload unchanged for GIF_extension
	 *
	 * @return void
	 */
	public function testConvertMediaToWebPReturnsUnchangedForGif(): void
	{
		$media = new ConcreteMedia();

		$upload = [
			'file' => '/tmp/uploads/animation.gif',
			'url' => 'https://example.com/animation.gif',
			'type' => 'image/gif',
		];

		$result = $media->convertMediaToWebP($upload);

		$this->assertSame($upload, $result);
	}

	/**
	 * Test convertMediaToWebP returns upload unchanged on exception
	 *
	 * @return void
	 */
	public function testConvertMediaToWebPReturnsUnchangedOnException(): void
	{
		Functions\when('esc_html__')->returnArg();
		Functions\when('wp_get_upload_dir')->justReturn([
			'basedir' => '/tmp/uploads',
			'baseurl' => 'https://example.com/uploads',
		]);
		Functions\when('wp_unique_filename')->alias(function ($dir, $name) {
			return $name;
		});

		// file_exists returns false → convertMediaToWebPByPath throws,
		// convertMediaToWebP catches and returns original upload.
		Functions\when('file_exists')->justReturn(false);

		$media = new ConcreteMedia();

		$upload = [
			'file' => '/tmp/uploads/photo.jpg',
			'url' => 'https://example.com/photo.jpg',
			'type' => 'image/jpeg',
		];

		$result = $media->convertMediaToWebP($upload);

		$this->assertSame($upload, $result);
	}

	/**
	 * Test enableSvgMediaLibraryPreview sets image data for valid SVG
	 *
	 * @return void
	 */
	public function testEnableSvgMediaLibraryPreviewSetsImageDataForSvg(): void
	{
		$svgPath = \dirname(__DIR__, 2) . '/fixtures/media/test.svg';

		Functions\when('get_attached_file')->justReturn($svgPath);
		Functions\when('file_exists')->alias('file_exists');

		$media = new ConcreteMedia();

		$response = [
			'type' => 'image',
			'subtype' => 'svg+xml',
			'url' => 'https://example.com/test.svg',
		];

		$result = $media->enableSvgMediaLibraryPreview($response, 123);

		$this->assertIsArray($result);
		$this->assertArrayHasKey('image', $result);
		$this->assertArrayHasKey('thumb', $result);
		$this->assertArrayHasKey('sizes', $result);
		$this->assertEquals('https://example.com/test.svg', $result['image']['src']);
		$this->assertEquals(100, $result['image']['width']);
		$this->assertEquals(50, $result['image']['height']);
		$this->assertArrayHasKey('full', $result['sizes']);
		$this->assertEquals('landscape', $result['sizes']['full']['orientation']);
	}

	/**
	 * Test enableSvgMediaLibraryPreview returns false for invalid SVG XML
	 *
	 * @return void
	 */
	public function testEnableSvgMediaLibraryPreviewReturnsFalseForInvalidSvg(): void
	{
		// Create a temporary invalid SVG file.
		$tmpFile = \tempnam(\sys_get_temp_dir(), 'svg_test');
		\file_put_contents($tmpFile, 'not valid xml at all');

		Functions\when('get_attached_file')->justReturn($tmpFile);
		Functions\when('file_exists')->alias('file_exists');
		Functions\when('esc_html__')->returnArg();

		// Define WP_Error stub if it doesn't exist.
		if (!\class_exists('WP_Error')) {
			eval('class WP_Error { public function __construct($code = \'\', $message = \'\', $data = \'\') {} }');
		}

		$media = new ConcreteMedia();

		$response = [
			'type' => 'image',
			'subtype' => 'svg+xml',
			'url' => 'https://example.com/bad.svg',
		];

		$result = $media->enableSvgMediaLibraryPreview($response, 123);

		$this->assertFalse($result);

		\unlink($tmpFile);
	}

	/**
	 * Test enableSvgMediaLibraryPreview accepts WP_Post as attachment
	 *
	 * @return void
	 */
	public function testEnableSvgMediaLibraryPreviewAcceptsWpPostAttachment(): void
	{
		$svgPath = \dirname(__DIR__, 2) . '/fixtures/media/test.svg';

		Functions\when('get_attached_file')->justReturn($svgPath);
		Functions\when('file_exists')->alias('file_exists');

		$media = new ConcreteMedia();

		// Create a mock WP_Post.
		$post = \Mockery::mock('WP_Post');
		$post->ID = 42;

		$response = [
			'type' => 'image',
			'subtype' => 'svg+xml',
			'url' => 'https://example.com/test.svg',
		];

		$result = $media->enableSvgMediaLibraryPreview($response, $post);

		$this->assertIsArray($result);
		$this->assertArrayHasKey('image', $result);
	}

	/**
	 * Test validateSvgOnUpload returns unchanged for valid SVG
	 *
	 * @return void
	 */
	public function testValidateSvgOnUploadReturnsUnchangedForValidSvg(): void
	{
		$svgPath = \dirname(__DIR__, 2) . '/fixtures/media/test.svg';

		Functions\when('file_exists')->alias('file_exists');

		$media = new ConcreteMedia();

		$response = [
			'type' => 'image/svg+xml',
			'tmp_name' => $svgPath,
			'name' => 'test.svg',
		];

		$result = $media->validateSvgOnUpload($response);

		$this->assertSame($response, $result);
	}

	/**
	 * Test validateSvgOnUpload returns error-like structure for invalid SVG
	 *
	 * @return void
	 */
	public function testValidateSvgOnUploadReturnsErrorForInvalidSvg(): void
	{
		$tmpFile = \tempnam(\sys_get_temp_dir(), 'svg_val');
		\file_put_contents($tmpFile, 'not xml content');

		Functions\when('file_exists')->alias('file_exists');

		$media = new ConcreteMedia();

		$response = [
			'type' => 'image/svg+xml',
			'tmp_name' => $tmpFile,
			'name' => 'bad.svg',
		];

		$result = $media->validateSvgOnUpload($response);

		// Invalid SVG should return error-like structure with 'size' and 'name' keys.
		$this->assertArrayHasKey('size', $result);
		$this->assertArrayHasKey('name', $result);
		$this->assertSame('bad.svg', $result['name']);

		\unlink($tmpFile);
	}

	/**
	 * Test enableSvgMediaLibraryPreview portrait orientation
	 */
	public function testEnableSvgMediaLibraryPreviewPortraitOrientation(): void
	{
		$svgPath = \dirname(__DIR__, 2) . '/fixtures/media/portrait.svg';

		Functions\when('get_attached_file')->justReturn($svgPath);
		Functions\when('file_exists')->alias('file_exists');

		$media = new ConcreteMedia();

		$response = [
			'type' => 'image',
			'subtype' => 'svg+xml',
			'url' => 'https://example.com/portrait.svg',
		];

		$result = $media->enableSvgMediaLibraryPreview($response, 123);

		$this->assertIsArray($result);
		$this->assertArrayHasKey('sizes', $result);
		$this->assertEquals('portrait', $result['sizes']['full']['orientation']);
		$this->assertEquals(50, $result['sizes']['full']['width']);
		$this->assertEquals(100, $result['sizes']['full']['height']);
	}

	/**
	 * Test convertMediaToWebP success path
	 */
	public function testConvertMediaToWebPSuccessPath(): void
	{
		$media = new ConcreteMedia();

		$upload = [
			'file' => '/tmp/uploads/photo.jpg',
			'url' => 'https://example.com/uploads/photo.jpg',
			'type' => 'image/jpeg',
		];

		// Mock Helpers::convertMediaToWebPByPath via Patchwork.
		\Patchwork\redefine(
			'EightshiftLibs\Helpers\Helpers::convertMediaToWebPByPath',
			function ($filePath, $quality) {
				return [
					'newFullPath' => '/tmp/uploads/photo.webp',
					'newUrl' => 'https://example.com/uploads/photo.webp',
					'newType' => 'image/webp',
				];
			}
		);

		$deleteCalled = false;
		Functions\when('wp_delete_file')->alias(function ($file) use (&$deleteCalled) {
			$deleteCalled = true;
		});

		$result = $media->convertMediaToWebP($upload);

		$this->assertEquals('/tmp/uploads/photo.webp', $result['file']);
		$this->assertEquals('https://example.com/uploads/photo.webp', $result['url']);
		$this->assertEquals('image/webp', $result['type']);
		$this->assertTrue($deleteCalled, 'wp_delete_file should be called with original file');
	}
}

/**
 * Concrete implementation of AbstractMedia for testing
 */
class ConcreteMedia extends AbstractMedia
{
	/**
	 * Register the service
	 *
	 * @return void
	 */
	public function register(): void
	{
		\add_filter('upload_mimes', [$this, 'enableMimeTypes']);
		\add_filter('wp_check_filetype_and_ext', [$this, 'enableSvgUpload'], 10, 3);
		\add_filter('wp_check_filetype_and_ext', [$this, 'enableJsonUpload'], 10, 3);
	}
}
