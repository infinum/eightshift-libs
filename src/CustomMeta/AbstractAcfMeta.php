<?php

/**
 * File that holds base abstract class for custom meta registration.
 *
 * @package EightshiftLibs\CustomMeta
 */

declare(strict_types=1);

namespace EightshiftLibs\CustomMeta;

use EightshiftLibs\Services\ServiceInterface;

/**
 * Abstract class AbstractAcfMeta class.
 */
abstract class AbstractAcfMeta implements ServiceInterface
{

	/**
	 * Register custom acf meta.
	 *
	 * @return void
	 */
	public function register(): void
	{
		\add_action('acf/init', [$this, 'fields']);
	}

	/**
	 * Render acf fields.
	 *
	 * @return void
	 */
	abstract public function fields(): void;
}
