<?php

/**
 * File that holds base abstract class for custom post type registration.
 *
 * @package EightshiftLibs\CustomPostType
 */

declare(strict_types=1);

namespace EightshiftLibs\CustomPostType;

use EightshiftLibs\Services\ServiceInterface;

/**
 * Abstract class AbstractPostType class.
 */
abstract class AbstractPostType implements ServiceInterface
{
	/**
	 * Register custom post type.
	 *
	 * @return void
	 */
	public function register(): void
	{
		\add_action('init', [$this, 'postTypeRegisterCallback']);
	}

	/**
	 * Method that registers post_type that is used inside init hook.
	 *
	 * @return void
	 */
	public function postTypeRegisterCallback(): void
	{
		\register_post_type(
			$this->getPostTypeSlug(),
			$this->getPostTypeArguments()
		);
	}

	/**
	 * Get the slug to use for the custom post type.
	 *
	 * @return string Custom post type slug.
	 */
	abstract protected function getPostTypeSlug(): string;

	/**
	 * Get the arguments that configure the custom post type.
	 *
	 * @return array<string, mixed> Array of arguments.
	 */
	abstract protected function getPostTypeArguments(): array;
}
