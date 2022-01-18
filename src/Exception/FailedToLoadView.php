<?php

/**
 * File containing failed to load view class
 *
 * @package EightshiftLibs\Exception
 */

declare(strict_types=1);

namespace EightshiftLibs\Exception;

/**
 * Class FailedToLoadView
 */
final class FailedToLoadView extends \RuntimeException implements GeneralExceptionInterface
{
	/**
	 * Create a new instance of the exception if the view file itself created
	 * an exception.
	 *
	 * @param string     $uri URI of the file that is not accessible or
	 *                              not readable.
	 * @param \Exception $exception Exception that was thrown by the view file.
	 *
	 * @return static
	 */
	public static function viewException(string $uri, \Exception $exception): FailedToLoadView
	{
		$message = sprintf(
		/* translators: %1$s will be replaced with view URI, and %2$s with error. */
			\esc_html__('Could not load the View URI: %1$s. Reason: %2$s.', 'eightshift-libs'),
			$uri,
			$exception->getMessage()
		);

		return new FailedToLoadView($message, $exception->getCode(), $exception);
	}
}
