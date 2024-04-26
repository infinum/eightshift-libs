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
	 * Create a new instance of the exception for a file that is not accessible
	 * or not readable.
	 *
	 * @param string $uri URI of the file that is not accessible or not
	 *                    readable.
	 *
	 * @return static
	 */
	public static function fromUri(string $uri): InvalidPath
	{
		$message = \sprintf(
			/* translators: %s will be replaced by path. */
			\esc_html__('The URI "%s" is not accessible or readable.', 'eightshift-libs'),
			$uri
		);

		return new InvalidPath($message);
	}

	/**
	 * Throws error if the file is missing.
	 *
	 * @param string $path Missing file path.
	 *
	 * @return static
	 */
	public static function missingFileException(string $path): InvalidBlock
	{
		return new InvalidBlock(
			\sprintf(
				/* translators: %s is going to be replaced with the missing file path. */
				\esc_html__('Failed to open file on this %s path. Please check again.', 'eightshift-libs'),
				$path,
			)
		);
	}

	/**
	 * Throws error if the block file is missing and provides an example.
	 *
	 * @param string $sourcePath Missing file path.
	 *
	 * @return static
	 */
	public static function missingFileWithExampleException(string $sourcePath): InvalidBlock
	{
		return new InvalidBlock(
			\sprintf(
				/* translators: %1$s is going to be replaced with the missing file path. %2$s is going to be replaced with the expected file name. */
				\esc_html__('Failed to open file on this %1$s path. The file expecing should be called %2$s.', 'eightshift-libs'),
				$sourcePath,
			)
		);
	}
}
