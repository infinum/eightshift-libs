<?php

/**
 * Helpers for media.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

use EightshiftLibs\Media\AbstractMedia;
use EightshiftLibs\Media\UseWebPMediaCli;

/**
 * Class MediaTrait Helper
 */
trait MediaTrait
{
	/**
	 * Cache for flipped allowed extensions for O(1) lookup.
	 *
	 * @var array<string, int>|null
	 */
	private static ?array $allowedExtensionsFlipped = null;

	/**
	 * Cache for WebP media results to avoid repeated processing.
	 *
	 * @var array<string, array<string>>
	 */
	private static array $webpMediaCache = [];

	/**
	 * Cache for file existence checks to reduce filesystem operations.
	 *
	 * @var array<int, bool>
	 */
	private static array $webpExistsCache = [];

	/**
	 * Initialize media-related static caches.
	 *
	 * @param array<int, string> $allowed Allowed file types.
	 *
	 * @return void
	 */
	private static function initializeMediaCaches(array $allowed = AbstractMedia::WEBP_ALLOWED_EXT): void
	{
		if (self::$allowedExtensionsFlipped === null) {
			self::$allowedExtensionsFlipped = \array_flip($allowed);
		}
	}

	/**
	 * Fast file extension extraction optimized for performance.
	 *
	 * @param string $path File path.
	 *
	 * @return string|null
	 */
	private static function getFileExtension(string $path): ?string
	{
		// Fast path: find last dot and extract extension.
		$lastDot = \strrpos($path, '.');
		if ($lastDot === false) {
			return null;
		}

		$ext = \substr($path, $lastDot + 1);

		// Quick validation - extensions should be 3-4 chars typically.
		$extLength = \strlen($ext);
		if ($extLength < 2 || $extLength > 5) {
			return null;
		}

		return \strtolower($ext);
	}

	/**
	 * Optimized path replacement for WebP conversion.
	 *
	 * @param string $path Original path.
	 * @param string $ext Original extension.
	 *
	 * @return string
	 */
	private static function replaceExtensionToWebP(string $path, string $ext): string
	{
		// Find the last occurrence of the extension and replace it.
		$extLen = \strlen($ext);
		$lastPos = \strrpos($path, ".{$ext}");

		if ($lastPos !== false) {
			return \substr($path, 0, $lastPos) . '.webp';
		}

		// Fallback to simple replacement if position not found.
		return \str_replace(".{$ext}", '.webp', $path);
	}

	/**
	 * Return WebP format from the original path.
	 * Optimized for maximum performance with static caching and fast operations.
	 *
	 * @param string $path Path to original media file.
	 * @param array<int, string> $allowed Allowed file types.
	 *
	 * @return array<string>
	 */
	public static function getWebPMedia(string $path, array $allowed = AbstractMedia::WEBP_ALLOWED_EXT): array
	{
		// Early return for empty path.
		if ($path === '') {
			return [];
		}

		// Check cache first for repeated calls.
		$cacheKey = $path;
		if (isset(self::$webpMediaCache[$cacheKey])) {
			return self::$webpMediaCache[$cacheKey];
		}

		// Initialize caches.
		self::initializeMediaCaches($allowed);

		// Fast extension extraction instead of wp_check_filetype.
		$ext = self::getFileExtension($path);

		if ($ext === null) {
			self::$webpMediaCache[$cacheKey] = [];
			return [];
		}

		// Fast path for WebP files.
		if ($ext === 'webp') {
			$result = [
				'src' => $path,
				'type' => 'image/webp',
			];
			self::$webpMediaCache[$cacheKey] = $result;
			return $result;
		}

		// Fast O(1) lookup instead of in_array.
		if (!isset(self::$allowedExtensionsFlipped[$ext])) {
			self::$webpMediaCache[$cacheKey] = [];
			return [];
		}

		// Optimized path replacement.
		$newPath = self::replaceExtensionToWebP($path, $ext);

		$result = [
			'src' => $newPath,
			'type' => 'image/webp',
		];

		// Cache the result.
		self::$webpMediaCache[$cacheKey] = $result;
		return $result;
	}

	/**
	 * Check if WebP Media is used based on the options setting.
	 * Already optimized with static caching.
	 *
	 * @return boolean
	 */
	public static function isWebPMediaUsed(): bool
	{
		static $isWebPMediaUsed = null;

		if ($isWebPMediaUsed === null) {
			$isWebPMediaUsed = (bool) \get_option(UseWebPMediaCli::USE_WEBP_MEDIA_OPTION_NAME, false);
		}

		return $isWebPMediaUsed;
	}

	/**
	 * Check if WebP media exist by testing the original media.
	 * Optimized with caching and reduced filesystem operations.
	 *
	 * @param integer $attachmentId Id of the media.
	 *
	 * @return boolean
	 */
	public static function existsWebPMedia(int $attachmentId): bool
	{
		// Check cache first to avoid repeated filesystem operations.
		if (isset(self::$webpExistsCache[$attachmentId])) {
			return self::$webpExistsCache[$attachmentId];
		}

		// Early return for invalid attachment ID.
		if ($attachmentId <= 0) {
			self::$webpExistsCache[$attachmentId] = false;
			return false;
		}

		$filePath = \get_attached_file($attachmentId);

		// Early return if no file path.
		if (!$filePath) {
			self::$webpExistsCache[$attachmentId] = false;
			return false;
		}

		$image = self::getWebPMedia($filePath);

		// Early return if WebP conversion not possible.
		if (empty($image) || !isset($image['src'])) {
			self::$webpExistsCache[$attachmentId] = false;
			return false;
		}

		// Check file existence.
		$exists = \file_exists($image['src']);

		// Cache the result.
		self::$webpExistsCache[$attachmentId] = $exists;

		return $exists;
	}
}
