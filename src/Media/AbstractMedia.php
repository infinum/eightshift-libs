<?php

/**
 * File containing an abstract class for holding Media functionality.
 *
 * @package EightshiftLibs\Media
 */

declare(strict_types=1);

namespace EightshiftLibs\Media;

use EightshiftLibs\Services\ServiceInterface;

/**
 * Abstract class Media class.
 */
abstract class AbstractMedia implements ServiceInterface
{
	/**
	 * WebP Quality compression range 0-100.
	 *
	 * @var array<string>
	 */
	public const WEBP_ALLOWED_EXT = ['gif', 'jpg', 'jpeg', 'png', 'bmp'];

	/**
	 * WebP Quality compression range 0-100.
	 *
	 * @var int
	 */
	public const WEBP_QUALITY = 80;

	/**
	 * Generate webP media images.
	 *
	 * @param array<string, mixed> $metadata An array of attachment meta data.
	 * @param integer $attachmentId Current attachment ID.
	 *
	 * @return array<string, mixed>
	 */
	public function generateWebPMedia(array $metadata, int $attachmentId): array
	{
		// Get attached file to get path.
		$filePath = \get_attached_file($attachmentId);

		if (!$filePath) {
			return $metadata;
		}

		// Convert the original media.
		$mainImage = $this->convertMediaToWebPByPath($filePath);

		if (!$mainImage) {
			return $metadata;
		}

		// Find all sizes.
		$sizes = $metadata['sizes'] ?? [];

		if (!$sizes) {
			return $metadata;
		}

		// Get subdirectory path based on the provided file.
		$fileUploadSubDir = $this->getFileUploadSubDirPath($filePath);

		// Convert all file sizes.
		foreach ($sizes as $size) {
			$sizeFileOriginal = $size['file'] ?? '';

			if (!$sizeFileOriginal) {
				continue;
			}

			// Convert one size.
			$this->convertMediaToWebPByPath($fileUploadSubDir . $sizeFileOriginal);
		}

		return $metadata;
	}

	/**
	 * Delete all created WebP media after original media is deleted.
	 *
	 * @param integer $attachmentId Current attachment ID.
	 * @return void
	 */
	public function deleteWebPMedia(int $attachmentId): void
	{
		// Get attached file to get path.
		$filePath = \get_attached_file($attachmentId);

		if (!$filePath) {
			return;
		}

		// Delete the original media.
		$this->deleteWebPMediaByPath($filePath);

		// Get media meta data from post meta.
		$metadata = \get_post_meta($attachmentId, '_wp_attachment_metadata', true);

		// Delete all current active media and sizes.
		if ($metadata) {
			$this->deleteWebPMediaSizes($metadata, $filePath);
		}

		// Get all media backup media and sizes.
		$metadataBackup = \get_post_meta($attachmentId, '_wp_attachment_backup_sizes', true);

		// Delete all backup media and sizes.
		// Due to different output from post meta adjust the metadata.
		if ($metadataBackup) {
			$this->deleteWebPMediaSizes(
				[
					'sizes' => $metadataBackup,
				],
				$filePath
			);
		}
	}

	/**
	 * WebP Quality compression range 0-100.
	 *
	 * @return integer
	 */
	protected function getMediaWebPQuality(): int
	{
		return self::WEBP_QUALITY;
	}

	/**
	 * Convert media to WebP using file path.
	 *
	 * @param string $filePath Disk full file path.
	 *
	 * @return boolean
	 */
	private function convertMediaToWebPByPath(string $filePath): bool
	{
		// Detect type of media.
		$typeData = \wp_check_filetype($filePath);
		$ext = $typeData['ext'];

		// Replace the image name extension with the WebP.
		$filePathNew = \str_replace(".{$ext}", '.webp', $filePath);

		// Bailout if media exists.
		if (\file_exists($filePathNew)) {
			return false;
		}

		// Convert using different methods for differnet extensions.
		switch ($ext) {
			case 'gif':
				$createdImage = \imagecreatefromgif($filePath);
				break;
			case 'jpg':
			case 'jpeg':
				$createdImage = \imagecreatefromjpeg($filePath);
				break;
			case 'png':
				$createdImage = \imagecreatefrompng($filePath);
				\imagepalettetotruecolor($createdImage);
				\imagealphablending($createdImage, true);
				\imagesavealpha($createdImage, true);
				break;
			case 'bmp':
				$createdImage = \imagecreatefrombmp($filePath);
				break;
			default:
				return false;
		}

		if (!$createdImage) {
			return false;
		}

		// Create new WebP image and store it to the same location.
		\imagewebp($createdImage, $filePathNew, $this->getMediaWebPQuality());

		// Free up memory.
		\imagedestroy($createdImage);

		return true;
	}

	/**
	 * Delete WebP media by path.
	 *
	 * @param string $filePath Disk full file path.
	 *
	 * @return void
	 */
	private function deleteWebPMediaByPath(string $filePath): void
	{
		// Detect type of media.
		$typeData = \wp_check_filetype($filePath);
		$ext = $typeData['ext'];

		// Replace the image name extension with the WebP.
		$filePathNew = \str_replace(".{$ext}", '.webp', $filePath);

		// Delete file from disk.
		\wp_delete_file($filePathNew);
	}

	/**
	 * Delete WebP media sizes.
	 *
	 * @param array<string, mixed> $metadata An array of attachment meta data.
	 * @param string $filePath Disk full file path.
	 *
	 * @return void
	 */
	private function deleteWebPMediaSizes(array $metadata, string $filePath): void
	{
		// Find media sizes.
		$sizes = $metadata['sizes'] ?? [];

		if (!$sizes) {
			return;
		}

		// Get subdirectory path based on the provided file.
		$fileUploadSubDir = $this->getFileUploadSubDirPath($filePath);

		// Delete all file sizes.
		foreach ($sizes as $size) {
			$sizeFileOriginal = $size['file'] ?? '';

			if (!$sizeFileOriginal) {
				continue;
			}

			// Delete one size.
			$this->deleteWebPMediaByPath($fileUploadSubDir . $sizeFileOriginal);
		}
	}

	/**
	 * Get uploaded file subdir path.
	 *
	 * @param string $filePath Full file path.
	 *
	 * @return string
	 */
	private function getFileUploadSubDirPath(string $filePath): string
	{
		// Detect final media directory from provided file.
		$file = \explode(\DIRECTORY_SEPARATOR, $filePath);
		$file = \end($file);

		return \str_replace($file, '', $filePath);
	}
}
