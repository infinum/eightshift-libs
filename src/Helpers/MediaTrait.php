<?php

/**
 * Helpers for media.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

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
	 *
	 * @return string
	 */
	public static function convertMediaToWebPById(int $attachmentId, int $quality = 80): string
	{
		// Get attached file to get path.
		$filePath = \get_attached_file($attachmentId);

		if (!$filePath) {
			return '';
		}

		return self::convertMediaToWebPByPath($filePath, $quality);
	}

	/**
	 * Convert media to WebP using file path.
	 *
	 * @param string $filePath Disk full file path.
	 * @param integer $quality Conversion quality.
	 *
	 * @return string
	 */
	public static function convertMediaToWebPByPath(string $filePath, int $quality = 80): string
	{
		// Bailout if media origin doesn't exist.
		if (!\file_exists($filePath)) {
			return $filePath;
		}

		// Detect type of media.
		$typeData = \wp_check_filetype($filePath);
		$ext = $typeData['ext'];

		// Replace the image name extension with the WebP.
		$filePathNew = \str_replace(".{$ext}", '.webp', $filePath);

		// Bailout if media exists.
		if (\file_exists($filePathNew)) {
			return $filePathNew;
		}

		// Convert using different methods for different extensions.
		switch ($ext) {
			case 'gif':
				try {
					$createdImage = \imagecreatefromgif($filePath);
				} catch (Throwable $e) {
					return $filePath;
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
					return $filePath;
				}
				break;
			case 'png':
				try {
					$createdImage = \imagecreatefrompng($filePath);
				} catch (Throwable $e) {
					return $filePath;
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
					return $filePath;
				}
				break;
			default:
				return $filePath;
		}

		if (!$createdImage) {
			return $filePathNew;
		}

		// Create new WebP image and store it to the same location.
		$newImage = \imagewebp($createdImage, $filePathNew, $quality);

		// Free up memory.
		\imagedestroy($createdImage);

		if ($newImage) {
			return $filePathNew;
		}

		return $filePath;
	}
}
