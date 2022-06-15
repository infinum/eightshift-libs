<?php

/**
 * File containing file missing exception class
 *
 * @package EightshiftLibs\Exception
 */

declare(strict_types=1);

namespace EightshiftLibs\Exception;

use Exception;
use RuntimeException;

/**
 * Class FileMissing
 */
final class FileMissing extends RuntimeException implements GeneralExceptionInterface
{
	/**
	 * Create a new instance of the exception if the file is missing
	 *
	 * @param string    $path Path of the file that is not accessible or
	 *                        not readable.
	 *
	 * @return static
	 */
	public static function missingFileOnPath(string $path): FileMissing
	{
		$message = \sprintf(
		/* translators: %1$s will be replaced with file path. */
			\esc_html__('File missing on the path: %1$s.', 'eightshift-libs'),
			$path,
		);

		return new FileMissing($message);
	}
}
