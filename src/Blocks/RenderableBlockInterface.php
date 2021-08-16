<?php

/**
 * File that holds the renderable Block interface.
 *
 * @package EightshiftLibs\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Blocks;

/**
 * Interface Renderable Block.
 *
 * An object that can be rendered.
 */
interface RenderableBlockInterface
{

	/**
	 * Provides block registration render callback method.
	 *
	 * @param array<string, mixed> $attributes Array of attributes as defined in block's manifest.json.
	 * @param string $innerBlockContent Block's content if using inner blocks.
	 *
	 * @return string
	 * @throws \Exception On missing attributes OR missing template.
	 */
	public function render(array $attributes, string $innerBlockContent): string;
}
