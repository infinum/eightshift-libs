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
	 *
	 * @return static
	 */
	public static function missingManifestItemException(string $key): InvalidManifest
	{
		return new InvalidManifest(
			\sprintf(
			/* translators: %s is replaced by the missing key in the manifest.json */
				\esc_html__(
					'%s key does not exist in manifest.json. Please check if provided key is correct.',
					'eightshift-libs'
				),
				$key
			)
		);
	}

	/**
	 * Throws error if manifest.json file is missing
	 *
	 * @param string $path Missing manifest.json path.
	 *
	 * @return static
	 */
	public static function missingManifestException(string $path): InvalidManifest
	{
		return new InvalidManifest(
			\sprintf(
			/* translators: %s is replaced by the path where the manifest.json should be */
				\esc_html__(
					'manifest.json is missing at this path: %s. Bundle the theme before using it. Or your bundling process is returning an error.',
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
