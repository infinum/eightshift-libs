<?php

/**
 * File that holds base abstract class for block pattern.
 *
 * @package EightshiftLibs\BlockPatterns
 */

declare(strict_types=1);

namespace EightshiftLibs\BlockPatterns;

use EightshiftLibs\Services\ServiceInterface;

/**
 * Abstract class AbstractBlockPattern class.
 */
abstract class AbstractBlockPattern implements ServiceInterface
{

	/**
	 * Register block pattern.
	 *
	 * @return void
	 */
	public function register(): void
	{
		\add_action('init', [$this, 'registerBlockPattern']);
	}

	/**
	 * Method that registers new block pattern that is used inside init hook.
	 *
	 * @return void
	 */
	public function registerBlockPattern(): void
	{
		\register_block_pattern(
			$this->getName(),
			[
				'title' => $this->getTitle(),
				'description' => $this->getDescription(),
				'content' => $this->getContent(),
				'categories' => $this->getCategories(),
				'keywords' => $this->getKeywords(),
			]
		);
	}

	/**
	 * Get the pattern categories.
	 *
	 * @return array<string, mixed> Array of categories.
	 */
	protected function getCategories(): array
	{
		return [];
	}

	/**
	 * Get the pattern keywords.
	 *
	 * @return string[] Array of keywords.
	 */
	protected function getKeywords(): array
	{
		return [];
	}

	/**
	 * Get the pattern name with namespace.
	 * Example: eightshift/pattern-name
	 *
	 * @return string Pattern name.
	 */
	abstract protected function getName(): string;

	/**
	 * Get the pattern title that is shown in the pattern selector.
	 *
	 * @return string Pattern title.
	 */
	abstract protected function getTitle(): string;

	/**
	 * Get the pattern description.
	 *
	 * @return string Pattern description.
	 */
	abstract protected function getDescription(): string;

	/**
	 * Get the pattern content.
	 * NOTE: While returning the pattern content set it inside double quotes ("")
	 * to avoid formatting issues.
	 *
	 * @return string Pattern content.
	 */
	abstract protected function getContent(): string;
}
