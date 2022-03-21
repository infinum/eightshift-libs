<?php

/**
 * File containing invalid nouns exception
 *
 * @package EightshiftLibs\Exception
 */

declare(strict_types=1);

namespace EightshiftLibs\Exception;

use InvalidArgumentException;

/**
 * Class InvalidNouns
 */
final class InvalidNouns extends InvalidArgumentException implements GeneralExceptionInterface
{
	/**
	 * Create a new instance of the exception for an array of nouns that is
	 * missing a required key.
	 *
	 * @param string $key Asset handle that is not valid.
	 *
	 * @return static
	 */
	public static function fromKey(string $key): InvalidNouns
	{
		$message = \sprintf(
		/* translators: %s is replaced with name of the noun. */
			\esc_html__('The array of nouns passed into the Label_Generator is missing the %s noun.', 'eightshift-libs'),
			$key
		);

		return new InvalidNouns($message);
	}
}
