<?php

/**
 * File containing invalid Gutenberg Block exceptions
 *
 * @package EightshiftLibs\Exception
 */

declare(strict_types=1);

namespace EightshiftLibs\Exception;

use InvalidArgumentException;

/**
 * Class InvalidBlock
 */
final class InvalidBlock extends InvalidArgumentException implements GeneralExceptionInterface
{
	/**
	 * Throws error if global manifest settings key is missing.
	 *
	 * @param string $name Block/component name.
	 * @param string $componentName Component name to check.
	 *
	 * @return static
	 */
	public static function wrongComponentNameException(string $name, string $componentName): InvalidBlock
	{
		return new InvalidBlock(
			\sprintf(
				/* translators: %1$s is going to be replaced with the component/block name, %2$s with component name. */
				\esc_html__('Component specified in %1$s manifest doesn\'t exist in your components list.
				Please check if you project has %2$s component.', 'eightshift-libs'),
				$name,
				$componentName
			)
		);
	}

	/**
	 * Throws error if missing item.
	 *
	 * @param string $name Block/component name.
	 * @param string $type Type of the item.
	 *
	 * @return static
	 */
	public static function missingItemException(string $name, string $type): InvalidBlock
	{
		return new InvalidBlock(
			\sprintf(
				/* translators: %1$s is going to be replaced with the component/block name, %2$s with type. */
				\esc_html__('Trying to get %1$s %2$s. Please check if it exists in the project.', 'eightshift-libs'),
				$name,
				$type
			)
		);
	}
}
