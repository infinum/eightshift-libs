<?php

/**
 * File holding InvalidPath class
 *
 * @package EightshiftLibs\Exception
 */

declare(strict_types=1);

namespace EightshiftLibs\Exception;

use InvalidArgumentException;

/**
 * InvalidPath class
 */
final class InvalidPath extends InvalidArgumentException implements GeneralExceptionInterface
{
	/**
	 * Throws error if the directory is missing.
	 *
	 * @param string $path Missing file path.
	 *
	 * @return static
	 */
	public static function missingDirectoryException(string $path): InvalidPath
	{
		return new InvalidPath(
			\sprintf(
				/* translators: %s is going to be replaced with the missing directory path. */
				\esc_html__('Failed to read directory on this %s path. Please check again.', 'eightshift-libs'),
				$path,
			)
		);
	}

	/**
	 * Throws error if the file is missing.
	 *
	 * @param string $path Missing file path.
	 *
	 * @return static
	 */
	public static function missingFileException(string $path): InvalidPath
	{
		return new InvalidPath(
			\sprintf(
				/* translators: %s is going to be replaced with the missing file path. */
				\esc_html__('Failed to open file on this %s path. Please check again.', 'eightshift-libs'),
				$path,
			)
		);
	}

	/**
	 * Throws error if the file is missing and provides an example.
	 *
	 * @param string $path Missing file path.
	 * @param string $example Expected file name.
	 *
	 * @return static
	 */
	public static function missingFileWithExampleException(string $path, string $example): InvalidPath
	{
		return new InvalidPath(
			\sprintf(
				/* translators: %1$s is going to be replaced with the missing file path. %2$s is going to be replaced with the expected file name. */
				\esc_html__('Failed to open file on this %1$s path. The file expecing should be called %2$s.', 'eightshift-libs'),
				$path,
				$example
			)
		);
	}
}
