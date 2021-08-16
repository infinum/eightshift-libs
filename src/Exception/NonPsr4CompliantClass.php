<?php

/**
 * File containing the failure exception class when trying to autowire a class that's not PSR-4 compliant
 *
 * @package EightshiftLibs\Exception
 */

declare(strict_types=1);

namespace EightshiftLibs\Exception;

/**
 * Class NonPsr4CompliantClass
 */
final class NonPsr4CompliantClass extends \InvalidArgumentException implements GeneralExceptionInterface
{

	/**
	 * Throws exception if class has non psr-4 compliant namespace.
	 *
	 * @param string $className Class name we're looking for.
	 * @return static
	 */
	public static function throwInvalidNamespace(string $className): NonPsr4CompliantClass
	{
		return new NonPsr4CompliantClass(
			sprintf(
				/* translators: %s is replaced with the className. */
				'Unable to autowire %s. Please check if the namespace is PSR-4 compliant (i.e. it needs to match the folder structure).
				See: https://www.php-fig.org/psr/psr-4/#3-examples',
				$className
			)
		);
	}
}
