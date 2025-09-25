<?php

/**
 * Helpers for media.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

use Exception;
use Throwable;

/**
 * Class MediaTrait Helper
 */
trait MediaTrait
{
	/**
	 * Generate WebP media images - original size.
	 *
	 * @param integer $attachmentId Current attachment ID.
	 * @param integer $quality Conversion quality.
	 * @param boolean $onlyOutput Only return output array.
	 *
	 * @throws Exception Media not found.
	 * @throws Throwable Failed to create image.
	 *
	 * @return array<string, string>
	 */
	public static function convertMediaToWebPById(int $attachmentId, int $quality = 80, bool $onlyOutput = false): array
	{
		// Get attached file to get path.
		$filePath = \get_attached_file($attachmentId);

		return \array_merge(
			self::convertMediaToWebPByPath($filePath, $quality, $onlyOutput),
			[
				'attachmentId' => $attachmentId,
			]
		);
	}

	/**
	 * Convert media to WebP using file path.
	 *
	 * @param string $filePath Disk full file path.
	 * @param integer $quality Conversion quality.
	 * @param boolean $onlyOutput Only return output array.
	 *
	 * @throws Exception Media already exists.
	 * @throws Exception Media origin does not exist.
	 * @throws Exception Failed to create image from GIF.
	 * @throws Exception Failed to create image from JPEG.
	 * @throws Exception Failed to create image from PNG.
	 * @throws Exception Failed to create image from BMP.
	 * @throws Exception Unsupported media extension.
	 *
	 * @return array<string, string>
	 */
	public static function convertMediaToWebPByPath(string $filePath, int $quality = 80, bool $onlyOutput = false): array
	{
		// Detect type of media.
		$originalExtension = \pathinfo($filePath, \PATHINFO_EXTENSION);
		$originalFileName = \pathinfo($filePath, \PATHINFO_FILENAME);
		$originalDirname = \pathinfo($filePath, \PATHINFO_DIRNAME);

		// Replace the image name extension with the WebP.
		$filePathNew =  $originalDirname . '/' . $originalFileName . '.webp';

		$uploadDir = \wp_get_upload_dir();

		$dirnameRelative = \ltrim(\str_replace($uploadDir['basedir'], '', $originalDirname), '/');

		$output = [
			'newFullPath' => $filePathNew,
			'newUrl' => $uploadDir['baseurl'] . '/' . $dirnameRelative . "/{$originalFileName}.webp",
			'newExtension' => 'webp',
			'newType' => 'image/webp',
			'newFileName' => "{$originalFileName}.webp",
			'originalFullPath' => $filePath,
			'originalUrl' => $uploadDir['baseurl'] . '/' . $dirnameRelative . "/{$originalFileName}.{$originalExtension}",
			'originalExtension' => $originalExtension,
			'originalFileName' => $originalFileName,
			'originalType' => "image/{$originalExtension}",
			'dirnameRelative' => $dirnameRelative,
			'dirname' => $originalDirname,
			'dirnameUpload' => $uploadDir['basedir'],
		];

		// Bailout if only output is requested used for WP-CLI.
		if ($onlyOutput) {
			return $output;
		}


		// Bailout if media exists.
		if (\file_exists($filePathNew)) {
			throw new Exception(\esc_html__('Media already exists', 'eightshift-libs'));
		}

		// Bailout if media origin doesn't exist.
		if (!\file_exists($filePath)) {
			throw new Exception(\esc_html__('Media origin does not exist', 'eightshift-libs'));
		}

		// Convert using different methods for different extensions.
		switch ($originalExtension) {
			case 'gif':
				try {
					$createdImage = \imagecreatefromgif($filePath);
				} catch (Throwable $e) {
					throw new Exception(\esc_html__('Failed to create image from GIF', 'eightshift-libs'));
				}

				if ($createdImage) {
					\imagepalettetotruecolor($createdImage);
					\imagealphablending($createdImage, true);
					\imagesavealpha($createdImage, true);
				}
				break;
			case 'jpg':
			case 'jpeg':
				try {
					$createdImage = \imagecreatefromjpeg($filePath);
				} catch (Throwable $e) {
					throw new Exception(\esc_html__('Failed to create image from JPEG', 'eightshift-libs'));
				}
				break;
			case 'png':
				try {
					$createdImage = \imagecreatefrompng($filePath);
				} catch (Throwable $e) {
					throw new Exception(\esc_html__('Failed to create image from PNG', 'eightshift-libs'));
				}

				if ($createdImage) {
					\imagepalettetotruecolor($createdImage);
					\imagealphablending($createdImage, true);
					\imagesavealpha($createdImage, true);
				}
				break;
			case 'bmp':
				try {
					$createdImage = \imagecreatefrombmp($filePath);
				} catch (Throwable $e) {
					throw new Exception(\esc_html__('Failed to create image from BMP', 'eightshift-libs'));
				}
				break;
			default:
				throw new Exception(\esc_html__('Unsupported media extension', 'eightshift-libs'));
		}

		if (!$createdImage) {
			throw new Exception(\esc_html__('Failed to create image', 'eightshift-libs'));
		}

		// Create new WebP image and store it to the same location.
		$newImage = \imagewebp($createdImage, $filePathNew, $quality);

		// Free up memory.
		unset($createdImage);

		if (!$newImage) {
			throw new Exception(\esc_html__('Failed to create image due to unknown error', 'eightshift-libs'));
		}

		return $output;
	}
}
