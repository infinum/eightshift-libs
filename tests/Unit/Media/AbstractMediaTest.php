<?php

/**
 * Tests for AbstractMedia class
 *
 * @package EightshiftLibs\Tests\Unit\Media
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Media;

use Brain\Monkey;
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
