<?php

/**
 * Tests for MediaTrait helper methods.
 *
 * @package EightshiftLibs\Tests\Unit\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Helpers;

use EightshiftLibs\Tests\BaseTestCase;
use EightshiftLibs\Helpers\MediaTrait;
use Brain\Monkey\Functions;
use Exception;

/**
 * Wrapper class to test MediaTrait methods without conflicts.
 */
class MediaTraitWrapper
{
	use MediaTrait;
}

/**
 * Test case for MediaTrait utility methods.
 *
 * @coversDefaultClass EightshiftLibs\Helpers\MediaTrait
 */
class MediaTraitTest extends BaseTestCase
{
	private MediaTraitWrapper $wrapper;

	protected function setUp(): void
	{
		parent::setUp();
		$this->wrapper = new MediaTraitWrapper();

		// Mock WordPress functions
		Functions\when('esc_html__')->returnArg(1);
		Functions\when('wp_get_upload_dir')->alias(function () {
			return [
				'basedir' => '/var/www/uploads',
				'baseurl' => 'https://example.com/uploads',
			];
		});
	}

	/**
	 * @covers ::convertMediaToWebPById
	 */
	public function testConvertMediaToWebPByIdWithOnlyOutput(): void
	{
		$attachmentId = 123;
		$filePath = '/var/www/uploads/2024/01/test-image.jpg';

		Functions\when('get_attached_file')->alias(function ($id) use ($filePath) {
			return $filePath;
		});

		$result = $this->wrapper::convertMediaToWebPById($attachmentId, 80, true);

		$this->assertIsArray($result);
		$this->assertArrayHasKey('attachmentId', $result);
		$this->assertEquals($attachmentId, $result['attachmentId']);
		$this->assertArrayHasKey('originalFullPath', $result);
		$this->assertArrayHasKey('newFullPath', $result);
		$this->assertArrayHasKey('originalExtension', $result);
		$this->assertArrayHasKey('newExtension', $result);
		$this->assertEquals('webp', $result['newExtension']);
	}

	/**
	 * @covers ::convertMediaToWebPById
	 */
	public function testConvertMediaToWebPByIdMergesAttachmentId(): void
	{
		$attachmentId = 456;
		$filePath = '/var/www/uploads/2024/02/photo.png';

		Functions\when('get_attached_file')->alias(function ($id) use ($attachmentId, $filePath) {
			if ($id === $attachmentId) {
				return $filePath;
			}
			return false;
		});

		$result = $this->wrapper::convertMediaToWebPById($attachmentId, 85, true);

		// Verify it includes both path data and attachment ID
		$this->assertEquals($attachmentId, $result['attachmentId']);
		$this->assertEquals($filePath, $result['originalFullPath']);
		$this->assertEquals('png', $result['originalExtension']);
	}

	/**
	 * @covers ::convertMediaToWebPById
	 */
	public function testConvertMediaToWebPByIdWithDifferentQuality(): void
	{
		$attachmentId = 789;
		$filePath = '/var/www/uploads/high-quality.jpg';

		Functions\when('get_attached_file')->justReturn($filePath);

		$result = $this->wrapper::convertMediaToWebPById($attachmentId, 95, true);

		$this->assertEquals($attachmentId, $result['attachmentId']);
		$this->assertArrayHasKey('newExtension', $result);
		$this->assertEquals('webp', $result['newExtension']);
	}

	/**
	 * @covers ::convertMediaToWebPById
	 */
	public function testConvertMediaToWebPByIdWithJpegFile(): void
	{
		$attachmentId = 111;
		$filePath = '/var/www/uploads/photo.jpeg';

		Functions\when('get_attached_file')->justReturn($filePath);

		$result = $this->wrapper::convertMediaToWebPById($attachmentId, 80, true);

		$this->assertEquals($attachmentId, $result['attachmentId']);
		$this->assertEquals('jpeg', $result['originalExtension']);
		$this->assertEquals('photo.webp', $result['newFileName']);
	}

	/**
	 * @covers ::convertMediaToWebPById
	 */
	public function testConvertMediaToWebPByIdWithGifFile(): void
	{
		$attachmentId = 222;
		$filePath = '/var/www/uploads/animation.gif';

		Functions\when('get_attached_file')->justReturn($filePath);

		$result = $this->wrapper::convertMediaToWebPById($attachmentId, 80, true);

		$this->assertEquals($attachmentId, $result['attachmentId']);
		$this->assertEquals('gif', $result['originalExtension']);
		$this->assertEquals('animation.webp', $result['newFileName']);
	}

	/**
	 * @covers ::convertMediaToWebPById
	 */
	public function testConvertMediaToWebPByIdReturnsAllFields(): void
	{
		$attachmentId = 333;
		$filePath = '/var/www/uploads/complete-test.png';

		Functions\when('get_attached_file')->justReturn($filePath);

		$result = $this->wrapper::convertMediaToWebPById($attachmentId, 80, true);

		// Should have all path conversion fields plus attachment ID
		$this->assertArrayHasKey('attachmentId', $result);
		$this->assertArrayHasKey('newFullPath', $result);
		$this->assertArrayHasKey('originalFullPath', $result);
		$this->assertArrayHasKey('newUrl', $result);
		$this->assertArrayHasKey('originalUrl', $result);
		$this->assertArrayHasKey('newExtension', $result);
		$this->assertArrayHasKey('originalExtension', $result);
		$this->assertArrayHasKey('newType', $result);
		$this->assertArrayHasKey('originalType', $result);
	}

	/**
	 * @covers ::convertMediaToWebPByPath
	 */
	public function testConvertMediaToWebPByPathWithOnlyOutput(): void
	{
		$filePath = '/var/www/uploads/2024/01/test-image.jpg';

		$result = $this->wrapper::convertMediaToWebPByPath($filePath, 80, true);

		$this->assertIsArray($result);
		$this->assertArrayHasKey('newFullPath', $result);
		$this->assertArrayHasKey('newUrl', $result);
		$this->assertArrayHasKey('newExtension', $result);
		$this->assertArrayHasKey('newType', $result);
		$this->assertArrayHasKey('newFileName', $result);
		$this->assertArrayHasKey('originalFullPath', $result);
		$this->assertArrayHasKey('originalUrl', $result);
		$this->assertArrayHasKey('originalExtension', $result);
		$this->assertArrayHasKey('originalFileName', $result);
		$this->assertArrayHasKey('originalType', $result);

		$this->assertEquals($filePath, $result['originalFullPath']);
		$this->assertEquals('jpg', $result['originalExtension']);
		$this->assertEquals('webp', $result['newExtension']);
		$this->assertEquals('test-image', $result['originalFileName']);
		$this->assertEquals('test-image.webp', $result['newFileName']);
	}

	/**
	 * @covers ::convertMediaToWebPByPath
	 */
	public function testConvertMediaToWebPByPathWithPngFile(): void
	{
		$filePath = '/var/www/uploads/image.png';

		$result = $this->wrapper::convertMediaToWebPByPath($filePath, 90, true);

		$this->assertEquals('png', $result['originalExtension']);
		$this->assertEquals('image/png', $result['originalType']);
		$this->assertEquals('webp', $result['newExtension']);
		$this->assertEquals('image/webp', $result['newType']);
	}

	/**
	 * @covers ::convertMediaToWebPByPath
	 */
	public function testConvertMediaToWebPByPathWithGifFile(): void
	{
		$filePath = '/var/www/uploads/animation.gif';

		$result = $this->wrapper::convertMediaToWebPByPath($filePath, 80, true);

		$this->assertEquals('gif', $result['originalExtension']);
		$this->assertEquals('animation.webp', $result['newFileName']);
	}

	/**
	 * @covers ::convertMediaToWebPByPath
	 */
	public function testConvertMediaToWebPByPathWithBmpFile(): void
	{
		$filePath = '/var/www/uploads/photo.bmp';

		$result = $this->wrapper::convertMediaToWebPByPath($filePath, 80, true);

		$this->assertEquals('bmp', $result['originalExtension']);
		$this->assertEquals('photo.webp', $result['newFileName']);
	}

	/**
	 * @covers ::convertMediaToWebPByPath
	 */
	public function testConvertMediaToWebPByPathWithEmptyPath(): void
	{
		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Media origin does not exist');

		$this->wrapper::convertMediaToWebPByPath('', 80, false);
	}

	/**
	 * @covers ::convertMediaToWebPByPath
	 */
	public function testConvertMediaToWebPByPathGeneratesCorrectUrls(): void
	{
		$filePath = '/var/www/uploads/2024/01/my-image.jpg';

		$result = $this->wrapper::convertMediaToWebPByPath($filePath, 80, true);

		$this->assertEquals('https://example.com/uploads/2024/01/my-image.jpg', $result['originalUrl']);
		$this->assertEquals('https://example.com/uploads/2024/01/my-image.webp', $result['newUrl']);
	}

	/**
	 * @covers ::convertMediaToWebPByPath
	 */
	public function testConvertMediaToWebPByPathWithQualityParameter(): void
	{
		$filePath = '/var/www/uploads/test.jpg';

		// Test with different quality values
		$result1 = $this->wrapper::convertMediaToWebPByPath($filePath, 100, true);
		$result2 = $this->wrapper::convertMediaToWebPByPath($filePath, 50, true);

		// Both should have same structure
		$this->assertEquals($result1['originalFullPath'], $result2['originalFullPath']);
		$this->assertEquals($result1['newExtension'], $result2['newExtension']);
	}

	/**
	 * @covers ::convertMediaToWebPByPath
	 */
	public function testConvertMediaToWebPByPathWithJpegExtension(): void
	{
		$filePath = '/var/www/uploads/photo.jpeg';

		$result = $this->wrapper::convertMediaToWebPByPath($filePath, 80, true);

		$this->assertEquals('jpeg', $result['originalExtension']);
		$this->assertEquals('photo.webp', $result['newFileName']);
	}

	/**
	 * @covers ::convertMediaToWebPByPath
	 */
	public function testConvertMediaToWebPByPathReturnsAllRequiredFields(): void
	{
		$filePath = '/var/www/uploads/test.png';

		$result = $this->wrapper::convertMediaToWebPByPath($filePath, 80, true);

		$requiredFields = [
			'newFullPath',
			'newUrl',
			'newExtension',
			'newType',
			'newFileName',
			'originalFullPath',
			'originalUrl',
			'originalExtension',
			'originalFileName',
			'originalType',
			'dirnameRelative',
			'dirname',
			'dirnameUpload'
		];

		foreach ($requiredFields as $field) {
			$this->assertArrayHasKey($field, $result, "Missing required field: $field");
		}
	}

	/**
	 * @covers ::convertMediaToWebPByPath
	 */
	public function testConvertMediaToWebPByPathThrowsWhenWebpAlreadyExists(): void
	{
		$filePath = '/var/www/uploads/test.jpg';

		// Mock file_exists: the new .webp path already exists.
		Functions\when('file_exists')->alias(function ($path) {
			// The new webp path would be /var/www/uploads/test.webp.
			return \str_ends_with($path, '.webp');
		});

		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Media already exists');

		$this->wrapper::convertMediaToWebPByPath($filePath, 80, false);
	}

	/**
	 * @covers ::convertMediaToWebPByPath
	 */
	public function testConvertMediaToWebPByPathThrowsWhenOriginDoesNotExist(): void
	{
		$filePath = '/var/www/uploads/nonexistent.jpg';

		// Mock file_exists: webp doesn't exist, but neither does original.
		Functions\when('file_exists')->justReturn(false);

		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Media origin does not exist');

		$this->wrapper::convertMediaToWebPByPath($filePath, 80, false);
	}

	/**
	 * @covers ::convertMediaToWebPByPath
	 */
	public function testConvertMediaToWebPByPathThrowsForUnsupportedExtension(): void
	{
		$filePath = '/var/www/uploads/document.tiff';

		// Mock file_exists: webp doesn't exist, original exists.
		Functions\when('file_exists')->alias(function ($path) {
			return !\str_ends_with($path, '.webp');
		});

		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Unsupported media extension');

		$this->wrapper::convertMediaToWebPByPath($filePath, 80, false);
	}

	/**
	 * @covers ::convertMediaToWebPById
	 */
	public function testConvertMediaToWebPByIdThrowsWhenEmptyFilePath(): void
	{
		Functions\when('get_attached_file')->justReturn('');

		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Media origin does not exist');

		$this->wrapper::convertMediaToWebPById(999, 80, false);
	}

	/**
	 * @covers ::convertMediaToWebPByPath
	 */
	public function testConvertJpegToWebPSuccessfully(): void
	{
		$filePath = '/var/www/uploads/photo.jpg';
		$fakeImage = \imagecreatetruecolor(1, 1);

		Functions\when('file_exists')->alias(function ($path) use ($filePath) {
			return $path === $filePath;
		});

		Functions\when('imagecreatefromjpeg')->justReturn($fakeImage);
		Functions\when('imagewebp')->justReturn(true);

		$result = $this->wrapper::convertMediaToWebPByPath($filePath, 80, false);

		$this->assertSame('webp', $result['newExtension']);
		$this->assertSame($filePath, $result['originalFullPath']);
	}

	/**
	 * @covers ::convertMediaToWebPByPath
	 */
	public function testConvertJpegExtensionToWebPSuccessfully(): void
	{
		$filePath = '/var/www/uploads/photo.jpeg';
		$fakeImage = \imagecreatetruecolor(1, 1);

		Functions\when('file_exists')->alias(function ($path) use ($filePath) {
			return $path === $filePath;
		});

		Functions\when('imagecreatefromjpeg')->justReturn($fakeImage);
		Functions\when('imagewebp')->justReturn(true);

		$result = $this->wrapper::convertMediaToWebPByPath($filePath, 90, false);

		$this->assertSame('jpeg', $result['originalExtension']);
		$this->assertSame('webp', $result['newExtension']);
	}

	/**
	 * @covers ::convertMediaToWebPByPath
	 */
	public function testConvertPngToWebPSuccessfully(): void
	{
		$filePath = '/var/www/uploads/image.png';
		$fakeImage = \imagecreatetruecolor(1, 1);

		Functions\when('file_exists')->alias(function ($path) use ($filePath) {
			return $path === $filePath;
		});

		Functions\when('imagecreatefrompng')->justReturn($fakeImage);
		Functions\when('imagepalettetotruecolor')->justReturn(true);
		Functions\when('imagealphablending')->justReturn(true);
		Functions\when('imagesavealpha')->justReturn(true);
		Functions\when('imagewebp')->justReturn(true);

		$result = $this->wrapper::convertMediaToWebPByPath($filePath, 80, false);

		$this->assertSame('png', $result['originalExtension']);
		$this->assertSame('webp', $result['newExtension']);
	}

	/**
	 * @covers ::convertMediaToWebPByPath
	 */
	public function testConvertGifToWebPSuccessfully(): void
	{
		$filePath = '/var/www/uploads/animation.gif';
		$fakeImage = \imagecreatetruecolor(1, 1);

		Functions\when('file_exists')->alias(function ($path) use ($filePath) {
			return $path === $filePath;
		});

		Functions\when('imagecreatefromgif')->justReturn($fakeImage);
		Functions\when('imagepalettetotruecolor')->justReturn(true);
		Functions\when('imagealphablending')->justReturn(true);
		Functions\when('imagesavealpha')->justReturn(true);
		Functions\when('imagewebp')->justReturn(true);

		$result = $this->wrapper::convertMediaToWebPByPath($filePath, 80, false);

		$this->assertSame('gif', $result['originalExtension']);
		$this->assertSame('webp', $result['newExtension']);
	}

	/**
	 * @covers ::convertMediaToWebPByPath
	 */
	public function testConvertBmpToWebPSuccessfully(): void
	{
		$filePath = '/var/www/uploads/photo.bmp';
		$fakeImage = \imagecreatetruecolor(1, 1);

		Functions\when('file_exists')->alias(function ($path) use ($filePath) {
			return $path === $filePath;
		});

		Functions\when('imagecreatefrombmp')->justReturn($fakeImage);
		Functions\when('imagewebp')->justReturn(true);

		$result = $this->wrapper::convertMediaToWebPByPath($filePath, 80, false);

		$this->assertSame('bmp', $result['originalExtension']);
		$this->assertSame('webp', $result['newExtension']);
	}

	/**
	 * @covers ::convertMediaToWebPByPath
	 */
	public function testConvertJpegThrowsWhenImageCreateFails(): void
	{
		$filePath = '/var/www/uploads/corrupt.jpg';

		Functions\when('file_exists')->alias(function ($path) use ($filePath) {
			return $path === $filePath;
		});

		Functions\when('imagecreatefromjpeg')->alias(function () {
			throw new \Exception('GD error');
		});

		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Failed to create image from JPEG');

		$this->wrapper::convertMediaToWebPByPath($filePath, 80, false);
	}

	/**
	 * @covers ::convertMediaToWebPByPath
	 */
	public function testConvertPngThrowsWhenImageCreateFails(): void
	{
		$filePath = '/var/www/uploads/corrupt.png';

		Functions\when('file_exists')->alias(function ($path) use ($filePath) {
			return $path === $filePath;
		});

		Functions\when('imagecreatefrompng')->alias(function () {
			throw new \Exception('GD error');
		});

		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Failed to create image from PNG');

		$this->wrapper::convertMediaToWebPByPath($filePath, 80, false);
	}

	/**
	 * @covers ::convertMediaToWebPByPath
	 */
	public function testConvertGifThrowsWhenImageCreateFails(): void
	{
		$filePath = '/var/www/uploads/corrupt.gif';

		Functions\when('file_exists')->alias(function ($path) use ($filePath) {
			return $path === $filePath;
		});

		Functions\when('imagecreatefromgif')->alias(function () {
			throw new \Exception('GD error');
		});

		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Failed to create image from GIF');

		$this->wrapper::convertMediaToWebPByPath($filePath, 80, false);
	}

	/**
	 * @covers ::convertMediaToWebPByPath
	 */
	public function testConvertBmpThrowsWhenImageCreateFails(): void
	{
		$filePath = '/var/www/uploads/corrupt.bmp';

		Functions\when('file_exists')->alias(function ($path) use ($filePath) {
			return $path === $filePath;
		});

		Functions\when('imagecreatefrombmp')->alias(function () {
			throw new \Exception('GD error');
		});

		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Failed to create image from BMP');

		$this->wrapper::convertMediaToWebPByPath($filePath, 80, false);
	}

	/**
	 * @covers ::convertMediaToWebPByPath
	 */
	public function testConvertThrowsWhenImageCreateReturnsNull(): void
	{
		$filePath = '/var/www/uploads/bad.jpg';

		Functions\when('file_exists')->alias(function ($path) use ($filePath) {
			return $path === $filePath;
		});

		Functions\when('imagecreatefromjpeg')->justReturn(null);

		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Failed to create image');

		$this->wrapper::convertMediaToWebPByPath($filePath, 80, false);
	}

	/**
	 * @covers ::convertMediaToWebPByPath
	 */
	public function testConvertThrowsWhenImageWebpFails(): void
	{
		$filePath = '/var/www/uploads/photo.jpg';
		$fakeImage = \imagecreatetruecolor(1, 1);

		Functions\when('file_exists')->alias(function ($path) use ($filePath) {
			return $path === $filePath;
		});

		Functions\when('imagecreatefromjpeg')->justReturn($fakeImage);
		Functions\when('imagewebp')->justReturn(false);

		$this->expectException(Exception::class);
		$this->expectExceptionMessage('Failed to create image due to unknown error');

		$this->wrapper::convertMediaToWebPByPath($filePath, 80, false);
	}
}
