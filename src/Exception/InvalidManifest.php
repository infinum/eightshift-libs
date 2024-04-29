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
	 * Throws error if manifest is missing.
	 *
	 * @param string $path Missing manifest path.
	 *
	 * @return static
	 */
	public static function missingManifestException(string $path): InvalidManifest
	{
		return new InvalidManifest(
			\sprintf(
			/* translators: %s is replaced by the missing key in the manifest.json */
				\esc_html__(
					'Manifest.json in missing in this %s path. Please check your data.',
					'eightshift-libs'
				),
				$path
			)
		);
	}

	/**
	 * Throws error if manifest try to access on none allowed path.
	 *
	 * @param string $path Missing manifest path.
	 *
	 * @return static
	 */
	public static function notAllowedManifestPathException(string $path): InvalidManifest
	{
		return new InvalidManifest(
			\sprintf(
				/* translators: %s is replaced by the path privided */
				\esc_html__(
					'You are trying to get manifest.json outside of the Blocks folder. Please review your implementation. Path you are providing is: $s',
					'eightshift-libs'
				),
				$path
			)
		);
	}

	/**
	 * Throws error if manifest try to access on none allowed path item.
	 *
	 * @param string $path Missing manifest path.
	 *
	 * @return static
	 */
	public static function notAllowedManifestPathItemException(string $path): InvalidManifest
	{
		return new InvalidManifest(
			\sprintf(
				/* translators: %s is replaced by the path privided */
				\esc_html__(
					'You are tryng to get manifest.json from outside of the Blocks items folder. Allowed folders are: %1$s. Path you are providing is: %2$s',
					'eightshift-libs'
				),
				\implode(', ', [
					'custom',
					'components',
					'variations',
					'wrapper'
				]),
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
