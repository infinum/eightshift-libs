<?php

/**
 * Enqueue class used to define all script and style enqueues for Gutenberg blocks.
 *
 * @package EightshiftLibs\Enqueue\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Enqueue\Blocks;

use EightshiftLibs\Enqueue\AbstractAssets;
use EightshiftLibs\Manifest\ManifestInterface;

/**
 * Enqueue_Blocks class.
 */
abstract class AbstractEnqueueBlocks extends AbstractAssets
{

	public const BLOCKS_EDITOR_SCRIPT_URI = 'applicationBlocksEditor.js';
	public const BLOCKS_EDITOR_STYLE_URI = 'applicationBlocksEditor.css';

	public const BLOCKS_STYLE_URI = 'applicationBlocks.css';
	public const BLOCKS_SCRIPT_URI = 'applicationBlocks.js';

	/**
	 * Instance variable of manifest data.
	 *
	 * @var ManifestInterface
	 */
	protected $manifest;

	/**
	 * Enqueue blocks script for editor only.
	 *
	 * @return void
	 */
	public function enqueueBlockEditorScript(): void
	{
		$handler = "{$this->getAssetsPrefix()}-block-editor-scripts";

		\wp_register_script(
			$handler,
			$this->manifest->getAssetsManifestItem(static::BLOCKS_EDITOR_SCRIPT_URI),
			$this->getAdminScriptDependencies(),
			$this->getAssetsVersion(),
			$this->scriptInFooter()
		);
		\wp_enqueue_script($handler);
	}

	/**
	 * Enqueue blocks style for editor only.
	 *
	 * @return void
	 */
	public function enqueueBlockEditorStyle(): void
	{
		$handler = "{$this->getAssetsPrefix()}-block-editor-style";

		\wp_register_style(
			$handler,
			$this->manifest->getAssetsManifestItem(static::BLOCKS_EDITOR_STYLE_URI),
			$this->getAdminStyleDependencies(),
			$this->getAssetsVersion(),
			$this->getMedia()
		);

		\wp_enqueue_style($handler);
	}

	/**
	 * Enqueue blocks style for editor and frontend.
	 *
	 * @return void
	 */
	public function enqueueBlockStyle(): void
	{
		$handler = "{$this->getAssetsPrefix()}-block-style";

		\wp_register_style(
			$handler,
			$this->manifest->getAssetsManifestItem(static::BLOCKS_STYLE_URI),
			$this->getFrontendStyleDependencies(),
			$this->getAssetsVersion(),
			$this->getMedia()
		);

		\wp_enqueue_style($handler);
	}

	/**
	 * Enqueue blocks script for frontend only.
	 *
	 * @return void
	 */
	public function enqueueBlockScript(): void
	{
		$handler = "{$this->getAssetsPrefix()}-block-scripts";

		\wp_register_script(
			$handler,
			$this->manifest->getAssetsManifestItem(static::BLOCKS_SCRIPT_URI),
			$this->getFrontendScriptDependencies(),
			$this->getAssetsVersion(),
			$this->scriptInFooter()
		);

		\wp_enqueue_script($handler);
	}

	/**
	 * Get style dependencies
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_style/
	 *
	 * @return array List of all the style dependencies.
	 */
	protected function getAdminStyleDependencies(): array
	{
		return ["{$this->getAssetsPrefix()}-block-style"];
	}

	/**
	 * List of admin script dependencies
	 *
	 * @return array List of all the admin dependencies.
	 */
	protected function getAdminScriptDependencies(): array
	{
		return [
			'jquery',
			'wp-components',
			'wp-blocks',
			'wp-element',
			'wp-editor',
			'wp-date',
			'wp-data',
			'wp-i18n',
			'wp-viewport',
			'wp-blob',
			'wp-url',
		];
	}
}
