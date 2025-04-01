<?php

/**
 * Enqueue class used to define all script and style enqueues for Gutenberg blocks.
 *
 * @package EightshiftLibs\Enqueue\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Enqueue\Blocks;

use EightshiftLibs\Enqueue\AbstractAssets;

/**
 * Enqueue_Blocks class.
 */
abstract class AbstractEnqueueBlocks extends AbstractAssets
{
	/* --------------------------------------------------------------------------- */
	/* ONLY EDITOR  */
	/* --------------------------------------------------------------------------- */

	/**
	 * Block editor script handle.
	 *
	 * @return string
	 */
	public const BLOCKS_EDITOR_SCRIPT_URI = 'applicationBlocksEditor.js';

	/**
	 * Block editor style handle.
	 *
	 * @return string
	 */
	public const BLOCKS_EDITOR_STYLE_URI = 'applicationBlocksEditor.css';

	/**
	 * Get block editor JavaScript handle.
	 *
	 * @return string
	 */
	public function getBlockEditorScriptsHandle(): string
	{
		return "{$this->getAssetsPrefix()}-block-editor-scripts";
	}

	/**
	 * Enqueue blocks script for editor only.
	 *
	 * @return void
	 */
	public function enqueueBlockEditorScript(): void
	{
		$handle = $this->getBlockEditorScriptsHandle();

		\wp_register_script(
			$handle,
			$this->setAssetsItem(static::BLOCKS_EDITOR_SCRIPT_URI),
			$this->getBlockEditorScriptDependencies(),
			$this->getAssetsVersion(),
			\is_wp_version_compatible('6.3') ? $this->scriptArgs() : $this->scriptInFooter()
		);

		\wp_enqueue_script($handle);

		foreach ($this->getLocalizations() as $objectName => $dataArray) {
			\wp_localize_script($handle, $objectName, $dataArray);
		}
	}

	/**
	 * List block editor script dependencies.
	 *
	 * @return string[] List of all the admin dependencies.
	 */
	protected function getBlockEditorScriptDependencies(): array
	{
		return [
			'react',
			'react-dom',
			'wp-components',
			'wp-blocks',
			'wp-element',
			'wp-editor',
			'wp-date',
			'wp-data',
			'wp-i18n',
			'wp-api-fetch',
			'wp-viewport',
			'wp-blob',
			'wp-url',
			'lodash',
		];
	}

	/**
	 * Get block editor stylesheet handle.
	 *
	 * @return string
	 */
	public function getBlockEditorStyleHandle(): string
	{
		return "{$this->getAssetsPrefix()}-block-editor-style";
	}

	/**
	 * Enqueue blocks style for editor only.
	 *
	 * @return void
	 */
	public function enqueueBlockEditorStyle(): void
	{
		$handle = $this->getBlockEditorStyleHandle();

		\wp_register_style(
			$handle,
			$this->setAssetsItem(static::BLOCKS_EDITOR_STYLE_URI),
			$this->getBlockEditorStyleDependencies(),
			$this->getAssetsVersion(),
			$this->getMedia()
		);

		\wp_enqueue_style($handle);
	}

	/**
	 * Get style dependencies
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_style/
	 *
	 * @return string[] List of all the style dependencies.
	 */
	protected function getBlockEditorStyleDependencies(): array
	{
		return ["{$this->getAssetsPrefix()}-block-style"];
	}

	/* --------------------------------------------------------------------------- */
	/* ONLY FRONTEND  */
	/* --------------------------------------------------------------------------- */

	/**
	 * Block frontend style handle.
	 *
	 * @return string
	 */
	public const BLOCKS_FRONTEND_STYLE_URI = 'applicationBlocksFrontend.css';

	/**
	 * Block frontend script handle.
	 *
	 * @return string
	 */
	public const BLOCKS_FRONTEND_SCRIPT_URI = 'applicationBlocksFrontend.js';

		/**
	 * Get block frontend JavaScript handle.
	 *
	 * @return string
	 */
	public function getBlockFrontendScriptHandle(): string
	{
		return "{$this->getAssetsPrefix()}-block-frontend-scripts";
	}

	/**
	 * Enqueue blocks script for frontend only.
	 *
	 * @return void
	 */
	public function enqueueBlockFrontendScript(): void
	{
		$handle = $this->getBlockFrontendScriptHandle();

		\wp_register_script(
			$handle,
			$this->setAssetsItem(static::BLOCKS_FRONTEND_SCRIPT_URI),
			$this->getBlockFrontendScriptDependencies(),
			$this->getAssetsVersion(),
			\is_wp_version_compatible('6.3') ? $this->scriptArgs() : $this->scriptInFooter()
		);

		\wp_enqueue_script($handle);


		foreach ($this->getLocalizations() as $objectName => $dataArray) {
			\wp_localize_script($this->getBlockFrontendScriptHandle(), $objectName, $dataArray);
		}
	}

	/**
	 * Get block frontend script dependencies.
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_script/#default-scripts-included-and-registered-by-wordpress
	 *
	 * @return array<int, string> List of all the script dependencies.
	 */
	protected function getBlockFrontendScriptDependencies(): array
	{
		return [];
	}

	/**
	 * Get block frontend Stylesheet handle.
	 *
	 * @return string
	 */
	public function getBlockFrontendStyleHandle(): string
	{
		return "{$this->getAssetsPrefix()}-block-frontend-style";
	}

	/**
	 * Enqueue blocks style for frontend only.
	 *
	 * @return void
	 */
	public function enqueueBlockFrontendStyle(): void
	{
		$handle = $this->getBlockFrontendStyleHandle();

		\wp_register_style(
			$handle,
			$this->setAssetsItem(static::BLOCKS_FRONTEND_STYLE_URI),
			$this->getBlockFrontendStyleDependencies(),
			$this->getAssetsVersion(),
			$this->getMedia()
		);

		\wp_enqueue_style($handle);
	}

	/**
	 * Get front end style dependencies
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_style/
	 *
	 * @return array<int, string> List of all the style dependencies.
	 */
	protected function getBlockFrontendStyleDependencies(): array
	{
		return [];
	}

	/* --------------------------------------------------------------------------- */
	/* BOTH EDITOR AND FRONTEND  */
	/* --------------------------------------------------------------------------- */

		/**
	 * Block style handle.
	 *
	 * @return string
	 */
	public const BLOCKS_STYLE_URI = 'applicationBlocks.css';

	/**
	 * Get block Stylesheet handle.
	 *
	 * @return string
	 */
	public function getBlockStyleHandle(): string
	{
		return "{$this->getAssetsPrefix()}-block-style";
	}

	/**
	 * Enqueue blocks style for editor and frontend.
	 *
	 * @return void
	 */
	public function enqueueBlockStyle(): void
	{
		$handle = $this->getBlockStyleHandle();

		\wp_register_style(
			$handle,
			$this->setAssetsItem(static::BLOCKS_STYLE_URI),
			$this->getBlockStyleDependencies(),
			$this->getAssetsVersion(),
			$this->getMedia()
		);

		\wp_enqueue_style($handle);
	}

	/**
	 * Get front end style dependencies
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_style/
	 *
	 * @return array<int, string> List of all the style dependencies.
	 */
	protected function getBlockStyleDependencies(): array
	{
		return [];
	}

	/* --------------------------------------------------------------------------- */
	/* GENERAL  */
	/* --------------------------------------------------------------------------- */

	/**
	 * Un-registers some default styles that add unnecessary overrides.
	 * The styles are re-registered with a fake URL to prevent breaking style dependencies.
	 *
	 * This is a workaround until Gutenberg provides a better way to disable these styles.
	 *
	 * Verify that everything looks good in the Block editor after adding!
	 *
	 * @return void
	 */
	public function unregisterDefaultStyleOverrides(): void
	{
		// Unregister unneeded default styles.
		\wp_deregister_style('forms');
		\wp_deregister_style('reset');

		// Re-register the styles with a fake URL just so it doesn't break style dependencies.
		// phpcs:disable WordPress.WP.EnqueuedResourceParameters.MissingVersion
		\wp_enqueue_style('forms', \get_admin_url(null, 'css'), []);
		\wp_enqueue_style('reset', \get_admin_url(null, 'css'), []);
		// phpcs:enable WordPress.WP.EnqueuedResourceParameters.MissingVersion
	}
}
