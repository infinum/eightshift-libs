<?php

/**
 * File that holds base abstract class for custom taxonomy registration.
 *
 * @package EightshiftLibs\CustomTaxonomy
 */

declare(strict_types=1);

namespace EightshiftLibs\CustomTaxonomy;

use EightshiftLibs\Services\ServiceInterface;

/**
 * Abstract class AbstractBaseTaxonomy class.
 */
abstract class AbstractTaxonomy implements ServiceInterface
{
	/**
	 * Register custom taxonomy.
	 *
	 * @return void
	 */
	public function register(): void
	{
		\add_action('init', [$this, 'taxonomyRegisterCallback']);
	}

	/**
	 * Method that registers taxonomy that is used inside init hook.
	 *
	 * @return void
	 */
	public function taxonomyRegisterCallback(): void
	{
		\register_taxonomy(
			$this->getTaxonomySlug(),
			$this->getPostTypeSlug(),
			$this->getTaxonomyArguments()
		);
	}

	/**
	 * Get the slug of the custom taxonomy
	 *
	 * @return string Custom taxonomy slug.
	 */
	abstract protected function getTaxonomySlug(): string;

	/**
	 * Get the post type slug(s) that use the taxonomy.
	 *
	 * @return string|string[] Custom post type slug or an array of slugs.
	 */
	abstract protected function getPostTypeSlug();

	/**
	 * Get the arguments that configure the custom taxonomy.
	 *
	 * @return array<string, mixed> Array of arguments.
	 */
	abstract protected function getTaxonomyArguments(): array;
}
