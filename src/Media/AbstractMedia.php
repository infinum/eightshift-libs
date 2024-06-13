<?php

/**
 * File containing an abstract class for holding Media functionality.
 *
 * @package EightshiftLibs\Media
 */

declare(strict_types=1);

namespace EightshiftLibs\Media;

use EightshiftLibs\Services\ServiceInterface;
use EightshiftLibs\Helpers\Helpers;
use Exception;
use SimpleXMLElement;
use WP_Error;
use WP_Post;

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
	 * WebP allowed extensions.
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
		// WPML will not delete the file unless all files are removed from all languages.
		if (\has_filter('wpml_element_has_translations')) {
			$isTranslated = \apply_filters('wpml_element_has_translations', null, $attachmentId, 'attachment');

			if ($isTranslated) {
				return;
			}
		}

		$this->deleteWebPMediaOriginal($attachmentId);
		$this->deleteWebPMediaAllSizes($attachmentId);
	}

	/**
	 * Enable additional uploads in media.
	 *
	 * @param array<object|string> $mimes Load all mimes types.
	 * @return array<object|string>       Return original and updated.
	 */
	public function enableMimeTypes(array $mimes): array
	{
		$mimes['svg']  = 'image/svg+xml';
		$mimes['json'] = 'application/json';
		return $mimes;
	}

	/**
	 * Enable SVG preview in Media Library.
	 *
	 * @param array<mixed> $response   Array of prepared attachment data.
	 * @param int|object $attachment Attachment ID or object.
	 * @return array<object>|false Array of attachment details, or void if the parameter does not correspond to an attachment.
	 */
	public function enableSvgMediaLibraryPreview($response, $attachment)
	{
		if ($response['type'] === 'image' && $response['subtype'] === 'svg+xml' && \class_exists('SimpleXMLElement')) {
			try {
				$path = \get_attached_file($attachment instanceof WP_Post ? $attachment->ID : $attachment);

				if (\file_exists($path)) {
					$svgContent = \file($path);
					$svgContent = \implode(' ', $svgContent);

					if (!Helpers::isValidXml($svgContent)) {
						// Translators: %s represents the filename, eg. demo.json.
						new WP_Error(\sprintf(\esc_html__('Error: File invalid: %s', 'eightshift-libs'), $path));
						return false;
					}

					$svg    = new SimpleXMLElement($svgContent);
					$src    = $response['url'];
					$width  = (int) $svg['width'];
					$height = (int) $svg['height'];

					// media gallery.
					$response['image'] = \compact('src', 'width', 'height');
					$response['thumb'] = \compact('src', 'width', 'height');

					// media single.
					$response['sizes']['full'] = [
						'height'      => $height,
						'width'       => $width,
						'url'         => $src,
						'orientation' => $height > $width ? 'portrait' : 'landscape',
					];
				}
			} catch (Exception $e) {
				// Translators: %s represents the error text.
				new WP_Error(\sprintf(\esc_html__('Error: %s', 'eightshift-libs'), $e));
			}
		}

		return $response;
	}

	/**
	 * Check if SVG is valid on Add New Media Page.
	 *
	 * @param array<object|string> $response Response array.
	 * @return array<mixed>
	 */
	public function validateSvgOnUpload($response)
	{
		if ($response['type'] === 'image/svg+xml' && \class_exists('SimpleXMLElement')) {
			$path = $response['tmp_name'];

			$svgContent = \file($path);
			$svgContent = \implode(' ', $svgContent);

			if (\file_exists($path)) {
				if (!Helpers::isValidXml($svgContent)) {
					return [
						'size' => $response,
						'name' => $response['name'],
					];
				}
			}
		}
		return $response;
	}

	/**
	 * Enable SVG file upload.
	 *
	 * @param array<object>  $filetypeExtData Array fot output data.
	 * @param string $file              Full path to the file.
	 * @param string $filename          The name of the file (may differ from $file due to $file being in a tmp directory).
	 * @return array<object|string>
	 */
	public function enableSvgUpload($filetypeExtData, $file, $filename): array
	{
		if (\substr($filename, -4) === '.svg') {
			$filetypeExtData['ext']  = 'svg';
			$filetypeExtData['type'] = 'image/svg+xml';
		}
		return $filetypeExtData;
	}

	/**
	 * Enable JSON file upload.
	 *
	 * @param array<object>  $filetypeExtData Array fot output data.
	 * @param string $file              Full path to the file.
	 * @param string $filename          The name of the file (may differ from $file due to $file being in a tmp directory).
	 * @return array<object|string>
	 */
	public function enableJsonUpload($filetypeExtData, $file, $filename): array
	{
		if (\substr($filename, -5) === '.json') {
			$filetypeExtData['ext']  = 'json';
			$filetypeExtData['type'] = 'application/json';
		}
		return $filetypeExtData;
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
}
