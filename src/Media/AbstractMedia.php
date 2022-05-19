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
	 * Media WebP Trait.
	 */
	use MediaWebPTrait;

	/**
	 * WebP Quality compression range 0-100.
	 *
	 * @var array<string>
	 */
	public const WEBP_ALLOWED_EXT = ['gif', 'jpg', 'jpeg', 'png', 'bmp'];

	/**
	 * Generate WebP media images.
	 *
	 * @param array<string, mixed> $metadata An array of attachment meta data.
	 * @param integer $attachmentId Current attachment ID.
	 *
	 * @return array<string, mixed>
	 */
	public function generateWebPMedia(array $metadata, int $attachmentId): array
	{
		$this->generateWebPMediaOriginal($attachmentId, $this->getMediaWebPQuality());
		$this->generateWebPMediaAllSizes($attachmentId, $this->getMediaWebPQuality());

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
		$this->deleteWebPMediaOriginal($attachmentId);
		$this->deleteWebPMediaAllSizes($attachmentId);
	}

	/**
	 * WebP Quality compression range 0-100.
	 *
	 * @return integer
	 */
	protected function getMediaWebPQuality(): int
	{
		return 80;
	}
}
