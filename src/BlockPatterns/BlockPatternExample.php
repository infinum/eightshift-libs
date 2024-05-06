<?php

/**
 * File that holds base class for block pattern.
 *
 * @package EightshiftLibs\BlockPatterns
 */

declare(strict_types=1);

namespace %namespace%\BlockPatterns;

use %useLibs%\BlockPatterns\AbstractBlockPattern;

/**
 * BlockPatternExample class.
 */
class BlockPatternExample extends AbstractBlockPattern
{
	/**
	 * Get the pattern categories.
	 *
	 * @return array<string> Array of categories.
	 */
	protected function getCategories(): array
	{
		return [];
	}

	/**
	 * Get the pattern keywords.
	 *
	 * @return array<string> Array of keywords.
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
	protected function getName(): string
	{
		return '%name%';
	}

	/**
	 * Get the pattern title that is shown in the pattern selector.
	 *
	 * @return string Pattern title.
	 */
	protected function getTitle(): string
	{
		return '%title%';
	}

	/**
	 * Get the pattern description.
	 *
	 * @return string Pattern description.
	 */
	protected function getDescription(): string
	{
		return '%description%';
	}

	/**
	 * Get the pattern content.
	 * NOTE: While returning the pattern content set it inside double quotes ("")
	 * to avoid formatting issues.
	 *
	 * @return string Pattern content.
	 */
	protected function getContent(): string
	{
		return '%content%';
	}
}
