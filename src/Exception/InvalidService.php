<?php

/**
 * File containing the invalid service exception class
 *
 * @package EightshiftLibs\Exception
 */

declare(strict_types=1);

namespace EightshiftLibs\Exception;

use InvalidArgumentException;

/**
 * Class InvalidService
 */
final class InvalidService extends InvalidArgumentException implements GeneralExceptionInterface
{
	/**
	 * Create a new instance of the exception for a service class name that is
	 * not recognized.
	 *
	 * @param string $service Class name of the service that was not recognized.
	 *
	 * @return static
	 */
	public static function fromService(string $service): InvalidService
	{
		$message = \sprintf(
		/* translators: %s is replaced with name of the service. */
			\esc_html__('The service %s is not recognized and cannot be registered.', 'eightshift-libs'),
			$service
		);

		return new InvalidService($message);
	}
}
