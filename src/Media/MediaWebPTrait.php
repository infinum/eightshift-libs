<?php

/**
 * Trait containing an  Media WebP functionality.
 *
 * @package EightshiftLibs\Media
 */

declare(strict_types=1);

namespace EightshiftLibs\Media;

use Exception;

/**
 * Trait MediaWebPTrait.
 */
trait MediaWebPTrait
{
	/**
	 * Generate WebP media images - original size.
	 *
	 * @param integer $attachmentId Current attachment ID.
	 * @param integer $quality Conversion quality.
	 * @param boolean $force Force conversion no matter if the file exists.
	 *
	 * @return string
	 */
	public function generateWebPMediaOriginal(int $attachmentId, int $quality, bool $force = false): string
	{
		// Get attached file to get path.
		$filePath = \get_attached_file($attachmentId);

		if (!$filePath) {
			return '';
		}

		return $this->convertMediaToWebPByPath($filePath, $quality, $force);
	}
	/**
	 * Generate WebP media images - sizes.
	 *
	 * @param integer $attachmentId Current attachment ID.
	 * @param integer $quality Conversion quality.
	 * @param boolean $force Force conversion no matter if the file exists.
	 *
	 * @return array<string>
	 */
	public function generateWebPMediaAllSizes(int $attachmentId, int $quality, bool $force = false): array
	{
		$output = [];

		// Get attached file to get path.
		$filePath = \get_attached_file($attachmentId);

		if (!$filePath) {
			return $output;
		}

		// Find all sizes.
		$sizes = \wp_get_attachment_metadata($attachmentId)['sizes'] ?? [];

		if (!$sizes) {
			return $output;
		}

		// Get subdirectory path based on the provided file.
		$fileUploadSubDir = $this->getFileUploadSubDirPath($filePath);

		// Convert all file sizes.
		foreach ($sizes as $size => $sizeValue) {
			$sizeFileOriginal = $sizeValue['file'] ?? '';

			if (!$sizeFileOriginal) {
				continue;
			}

			// Convert one size.
			$convertedMedia = $this->convertMediaToWebPByPath($fileUploadSubDir . $sizeFileOriginal, $quality, $force);

			if ($convertedMedia) {
				$output[$size] = $convertedMedia;
			}
		}

		return $output;
	}

	/**
	 * Delete all created WebP media after original media is deleted - original.
	 *
	 * @param integer $attachmentId Current attachment ID.
	 *
	 * @return string
	 */
	public function deleteWebPMediaOriginal(int $attachmentId): string
	{
		// Get attached file to get path.
		$filePath = \get_attached_file($attachmentId);

		if (!$filePath) {
			return '';
		}

		// Delete the original media.
		return $this->deleteWebPMediaByPath($filePath);
	}

	/**
	 * Delete all created WebP media after original media is deleted - size.
	 *
	 * @param integer $attachmentId Current attachment ID.
	 *
	 * @return array<string>
	 */
	public function deleteWebPMediaAllSizes(int $attachmentId): array
	{
		$output = [];

		// Get attached file to get path.
		$filePath = \get_attached_file($attachmentId);

		if (!$filePath) {
			return $output;
		}

		// Get media meta data from post meta.
		$metadata = \get_post_meta($attachmentId, '_wp_attachment_metadata', true);

		// Delete all current active media and sizes.
		if ($metadata) {
			$output = \array_merge(
				$output,
				$this->deleteWebPMediaSizes($metadata, $filePath)
			);
		}

		// Get all media backup media and sizes.
		$metadataBackup = \get_post_meta($attachmentId, '_wp_attachment_backup_sizes', true);

		// Delete all backup media and sizes.
		// Due to different output from post meta adjust the metadata.
		if ($metadataBackup) {
			$output = \array_merge(
				$output,
				$this->deleteWebPMediaSizes(
					[
						'sizes' => $metadataBackup,
					],
					$filePath
				)
			);
		}

		return $output;
	}

	/**
	 * Convert media to WebP using file path.
	 *
	 * @param string $filePath Disk full file path.
	 * @param integer $quality Conversion quality.
	 * @param boolean $force Force conversion no matter if the file exists.
	 *
	 * @return string
	 */
	private function convertMediaToWebPByPath(string $filePath, int $quality, bool $force = false): string
	{
		// Bailout if media origin doesn't exist.
		if (!\getenv('ES_TEST')) {
			if (!\file_exists($filePath)) {
				return '';
			}
		}

		// Detect type of media.
		$typeData = \wp_check_filetype($filePath);
		$ext = $typeData['ext'];

		// Replace the image name extension with the WebP.
		$filePathNew = \str_replace(".{$ext}", '.webp', $filePath);

		// Bailout if media exists.
		if (\file_exists($filePathNew) && !$force) {
			return '';
		}

		// Convert using different methods for differnet extensions.
		switch ($ext) {
			case 'gif':
				try {
					$createdImage = \imagecreatefromgif($filePath);
				} catch (Exception $e) {
					return '';
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
				} catch (Exception $e) {
					return '';
				}
				break;
			case 'png':
				try {
					$createdImage = \imagecreatefrompng($filePath);
				} catch (Exception $e) {
					return '';
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
				} catch (Exception $e) {
					return '';
				}
				break;
			default:
				return '';
		}

		if (!$createdImage) {
			return '';
		}

		// Create new WebP image and store it to the same location.
		$newImage = \imagewebp($createdImage, $filePathNew, $quality);

		// Free up memory.
		\imagedestroy($createdImage);

		if ($newImage) {
			return $filePathNew;
		}

		return '';
	}

	/**
	 * Delete WebP media by path.
	 *
	 * @param string $filePath Disk full file path.
	 *
	 * @return string
	 */
	private function deleteWebPMediaByPath(string $filePath): string
	{
		// Detect type of media.
		$typeData = \wp_check_filetype($filePath);
		$ext = $typeData['ext'];

		// Replace the image name extension with the WebP.
		$filePathNew = \str_replace(".{$ext}", '.webp', $filePath);

		if (!\getenv('ES_TEST')) {
			if (!\file_exists($filePathNew)) {
				return '';
			}
		}

		// Delete file from disk.
		\wp_delete_file($filePathNew);

		return $filePathNew;
	}

	/**
	 * Delete WebP media sizes.
	 *
	 * @param array<string, mixed> $metadata An array of attachment meta data.
	 * @param string $filePath Disk full file path.
	 *
	 * @return array<string>
	 */
	private function deleteWebPMediaSizes(array $metadata, string $filePath): array
	{
		$output = [];

		// Find media sizes.
		$sizes = $metadata['sizes'] ?? [];

		if (!$sizes) {
			return $output;
		}

		// Get subdirectory path based on the provided file.
		$fileUploadSubDir = $this->getFileUploadSubDirPath($filePath);

		// Delete all file sizes.
		foreach ($sizes as $size => $sizeValue) {
			$sizeFileOriginal = $sizeValue['file'] ?? '';

			if (!$sizeFileOriginal) {
				continue;
			}

			$newSizePth = $fileUploadSubDir . $sizeFileOriginal;

			// Delete one size.
			$deletedMedia = $this->deleteWebPMediaByPath($newSizePth);

			if ($deletedMedia) {
				$output[$size] = $deletedMedia;
			}
		}

		return $output;
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
