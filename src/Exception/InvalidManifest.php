<?php

/**
 * File containing the failure exception class when assets aren't bundled or missing
 *
 * @package EightshiftLibs\Exception
 */

declare(strict_types=1);

namespace EightshiftLibs\Exception;

use InvalidArgumentException;

/**
 * Class InvalidManifest
 */
final class InvalidManifest extends InvalidArgumentException implements GeneralExceptionInterface
{
	/**
	 * Throws error if manifest key is missing
	 *
	 * @param string $key Missing manifest key.
	 * @param string $path Missing manifest path.
	 *
	 * @return static
	 */
	public static function missingManifestKeyException(string $key, string $path): InvalidManifest
	{
		return new InvalidManifest(
			\sprintf(
			/* translators: %s is replaced by the missing key in the manifest.json */
				\esc_html__(
					'%1$s key does not exist in manifest.json on this %2$s path. Please check if provided key is correct.',
					'eightshift-libs'
				),
				$key,
				$path
			)
		);
	}

	/**
	 * Throws error if manifest is empty or has errors.
	 *
	 * @param string $path Missing manifest path.
	 *
	 * @return static
	 */
	public static function emptyOrErrorManifestException(string $path): InvalidManifest
	{
		return new InvalidManifest(
			\sprintf(
			/* translators: %s is replaced by the missing key in the manifest.json */
				\esc_html__(
					'Manifest.json in this %s is empty or has errors. Please check your data.',
					'eightshift-libs'
				),
				$path
			)
		);
	}

	/**
	 * Throws error if manifest.json file has errors
	 *
	 * Errors like trailing commas or malformed json file.
	 *
	 * @param string $error Error message.
	 *
	 * @return static
	 */
	public static function manifestStructureException(string $error): InvalidManifest
	{
		return new InvalidManifest($error);
	}
}
