<?php

/**
 * File containing the invalid callback exception class
 *
 * @package EightshiftLibs\Exception
 */

declare(strict_types=1);

namespace EightshiftLibs\Exception;

/**
 * Class Invalid_Callback.
 */
final class InvalidCallback extends \InvalidArgumentException implements GeneralExceptionInterface
{

	/**
	 * Create a new instance of the exception for a callback class name that is
	 * not recognized.
	 *
	 * @param string $callback Class name of the callback that was not recognized.
	 *
	 * @return static
	 */
	public static function fromCallback(string $callback)
	{
		$message = sprintf(
		/* translators: %s is replaced with callback name. */
			esc_html__('The callback %s is not recognized and cannot be registered.', 'eightshift-libs'),
			$callback
		);

		return new static($message);
	}
}
