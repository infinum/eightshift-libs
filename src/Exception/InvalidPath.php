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
}
