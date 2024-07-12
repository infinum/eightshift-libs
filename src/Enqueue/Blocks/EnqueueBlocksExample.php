<?php

/**
 * Enqueue class used to define all script and style enqueues for Gutenberg blocks.
 *
 * @package %g_namespace%\Enqueue\Blocks
 */

declare(strict_types=1);

namespace %g_namespace%\Enqueue\Blocks;

use %g_namespace%\Config\Config;
use %g_use_libs%\Enqueue\Blocks\AbstractEnqueueBlocks;

/**
 * Enqueue_Blocks class.
 */
class EnqueueBlocksExample extends AbstractEnqueueBlocks
{
	/**
	 * Register all the hooks
	 */
	public function register(): void
	{
		// Editor only script.
		\add_action('enqueue_block_editor_assets', [$this, 'enqueueBlockEditorScript']);

		// Editor only style.
		\add_action('enqueue_block_editor_assets', [$this, 'enqueueBlockEditorStyle'], 50);

		// Editor and frontend style.
		\add_action('enqueue_block_assets', [$this, 'enqueueBlockStyle'], 50);

		// Frontend only script.
		\add_action('wp_enqueue_scripts', [$this, 'enqueueBlockFrontendScript']);

		// Frontend only style.
		\add_action('wp_enqueue_scripts', [$this, 'enqueueBlockFrontendStyle'], 50);

		// Unregister default style overrides.
		\add_action('enqueue_block_editor_assets', [$this, 'unregisterDefaultStyleOverrides'], 102);
	}

	/**
	 * Method that returns assets name used to prefix asset handlers.
	 *
	 * @return string
	 */
	public function getAssetsPrefix(): string
	{
		return Config::getProjectName();
	}

	/**
	 * Method that returns assets version for versioning asset handlers.
	 *
	 * @return string
	 */
	public function getAssetsVersion(): string
	{
		return Config::getProjectVersion();
	}
}
