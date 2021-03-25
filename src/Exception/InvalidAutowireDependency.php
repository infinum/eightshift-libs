<?php

/**
 * File containing the failure exception class when trying to inject interface based dependencies into a class (using autowiring) but
 * failing to provide the correct variable name by which we can find a class to inject.
 *
 * @package EightshiftLibs\Exception
 */

declare(strict_types=1);

namespace EightshiftLibs\Exception;

/**
 * Class Component_Exception.
 */
final class InvalidAutowireDependency extends \InvalidArgumentException implements GeneralExceptionInterface
{

	/**
	 * Throws exception if we cant guess the class to inject.
	 *
	 * @param string $className Class name we're looking for.
	 * @param string $interfaceName Class we're looking for needs to implement this.
	 * @return static
	 */
	public static function throwUnableToFindClass(string $className, string $interfaceName)
	{
		return new static(
			sprintf(
				/* translators: %s is replaced with the className and interfaceName. */
				"Unable to find \"%s\" class that implements %s (looking in \$filenameIndex). When injecting Interface dependencies, please make sure your variable name in __construct() matches the filename of a class implementing that interface (otherwise we dont know which class to inject). Alternatively you can define the dependency tree manually for this class using \$main->getServiceClasses(). See https://infinum.github.io/eightshift-docs/docs/basics/autowiring#what-if-my-class-has-an-interface-parameter-inside-the-constructor-method",
				$className,
				$interfaceName
			)
		);
	}

	/**
	 * Throws exception if we cant guess the class to inject because we found more than 1 with same name that implement $interfaceName.
	 *
	 * @param string $className Class name we're looking for.
	 * @param string $interfaceName Class we're looking for needs to implement this.
	 * @return static
	 */
	public static function throwMoreThanOneClassFound(string $className, string $interfaceName)
	{
		return new static(
			sprintf(
				/* translators: %s is replaced with the className and interfaceName. */
				"Found more than 1 class called \"%s\" that implements %s interface. Please make sure you dont have more than 1 class with the same name implementing the same interface. Alternatively you can manually defined dependencies for the class that uses %s interface as a dependency. See: https://infinum.github.io/eightshift-docs/docs/basics/autowiring#what-if-my-class-has-an-interface-parameter-inside-the-constructor-method",
				$className,
				$interfaceName,
				$interfaceName
			)
		);
	}

	/**
	 * Throws exception if we find a primitive dependency on a class that's not been manually built.
	 *
	 * @param string $className Class name we're looking for.
	 * @return static
	 */
	public static function throwPrimitiveDependencyFound(string $className)
	{
		return new static(
			sprintf(
				/* translators: %s is replaced with the className and interfaceName. */
				"Found a primitive dependency for %s. Autowire is unable to figure out what value needs to be injected here. Please define the dependency tree for this class manually using \$main->getServiceClasses(). See: https://infinum.github.io/eightshift-docs/docs/basics/autowiring#what-if-my-class-has-a-primitive-parameter-string-int-etc-inside-a-constructor-method",
				$className
			)
		);
	}
}
