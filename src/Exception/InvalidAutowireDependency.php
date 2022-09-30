<?php

/**
 * Autowire dependency exception class
 *
 * File containing the failure exception class when trying to inject interface based dependencies into a class (using autowiring)
 * but failing to provide the correct variable name by which we can find a class to inject.
 *
 * @package EightshiftLibs\Exception
 */

declare(strict_types=1);

namespace EightshiftLibs\Exception;

use InvalidArgumentException;

/**
 * Class InvalidAutowireDependency
 */
final class InvalidAutowireDependency extends InvalidArgumentException implements GeneralExceptionInterface
{
	/**
	 * Throws exception if we cant guess the class to inject.
	 *
	 * @param string $className Class name we're looking for.
	 * @param string $interfaceName Class we're looking for needs to implement this.
	 *
	 * @return static
	 */
	public static function throwUnableToFindClass(string $className, string $interfaceName): InvalidAutowireDependency
	{
		return new InvalidAutowireDependency(
			\sprintf(
				/* translators: 1: the className, 2: the interface name. */
				'Unable to find "%1$s" class that implements %2$s (looking in $filenameIndex).
				When injecting Interface dependencies, please make sure your variable name in __construct()
				matches the filename of a class implementing that interface (otherwise we don\'t know which class to inject).
				Alternatively you can define the dependency tree manually for this class using $main->getServiceClasses().
				See https://eightshift.com/docs/basics/autowiring#what-if-my-class-has-an-interface-parameter-inside-the-constructor-method',
				$className,
				$interfaceName
			)
		);
	}

	/**
	 * Throws an exception if we can't guess the class to inject because we found more than 1 with the same name that implements $interfaceName.
	 *
	 * @param string $className Class name we're looking for.
	 * @param string $interfaceName Class we're looking for needs to implement this.
	 *
	 * @return static
	 */
	public static function throwMoreThanOneClassFound(string $className, string $interfaceName): InvalidAutowireDependency
	{
		return new InvalidAutowireDependency(
			\sprintf(
				/* translators: 1: The class name, 2: The interface name, 3: The interface name */
				'Found more than 1 class called "%1$s" that implements %2$s interface.
				Please make sure you don\'t have more than 1 class with the same name implementing the same interface.
				Alternatively, you can manually define dependencies for the class that uses the %3$s interface as a dependency.
				See: https://eightshift.com/docs/basics/autowiring#what-if-my-class-has-an-interface-parameter-inside-the-constructor-method',
				$className,
				$interfaceName,
				$interfaceName
			)
		);
	}

	/**
	 * Throws an exception if we find a primitive dependency on a class that's not been manually built.
	 *
	 * @param string $className Class name we're looking for.
	 * @param string $param Parameter name that is causing the issue.
	 *
	 * @return static
	 */
	public static function throwPrimitiveDependencyFound(string $className, string $param): InvalidAutowireDependency
	{
		return new InvalidAutowireDependency(
			\sprintf(
				/* translators: %s is replaced with the className and interfaceName. */
				"Found a primitive dependency for %s with param %s. Autowire is unable to figure out what value needs to be injected here.
				Please define the dependency tree for this class manually using \$main->getServiceClasses().
				See: https://eightshift.com/docs/basics/autowiring#what-if-my-class-has-a-primitive-parameter-string-int-etc-inside-a-constructor-method",
				$className,
				$param
			)
		);
	}
}
