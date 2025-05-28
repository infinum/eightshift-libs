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
	 * Return WebP format from the original path.
	 *
	 * @param string $path Path to original media file.
	 * @param array<int, string> $allowed Allowed file types.
	 *
	 * @return array<string>
	 */
	public static function getWebPMedia(string $path, array $allowed = AbstractMedia::WEBP_ALLOWED_EXT): array
	{
		$typeData = \wp_check_filetype($path);
		$ext = $typeData['ext'];

		if ($ext === 'webp') {
			return [
				'src' => $path,
				'type' => 'image/webp',
			];
		}

		$allowed = \array_flip($allowed);

		if (!isset($allowed[$ext])) {
			return [];
		}

		$newPath = \str_replace(".{$ext}", '.webp', $path);

		return [
			'src' => $newPath,
			'type' => 'image/webp',
		];
	}

	/**
	 * Check if WebP Media is used based on the options setting.
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
	 *
	 * @param integer $attachmentId Id of the media.
	 *
	 * @return boolean
	 */
	public static function existsWebPMedia(int $attachmentId): bool
	{
		$filePath = \get_attached_file($attachmentId);

		$image = self::getWebPMedia($filePath);

		if (!$image) {
			return false;
		}

		return \file_exists($image['src']);
	}
}
