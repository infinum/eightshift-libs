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

	public const BLOCKS_FRONTEND_STYLE_URI = 'applicationBlocksFrontend.css';
	public const BLOCKS_FRONTEND_SCRIPT_URI = 'applicationBlocksFrontend.js';

	/**
	 * Instance variable of manifest data.
	 *
	 * @var ManifestInterface
	 */
	protected ManifestInterface $manifest;

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
	 * Method that returns assets hook used to determine hook usage.
	 *
	 * @param string $hook Hook name.
	 *
	 * @return boolean
	 */
	public function isEnqueueBlockEditorScriptUsed(string $hook): bool
	{
		return true;
	}

	/**
	 * Enqueue blocks script for editor only.
	 *
	 * @param string $hook Hook name.
	 *
	 * @return void
	 */
	public function enqueueBlockEditorScript(string $hook): void
	{
		if (!$this->isEnqueueBlockEditorScriptUsed($hook)) {
			return;
		}

		$handle = $this->getBlockEditorScriptsHandle();

		\wp_register_script(
			$handle,
			$this->manifest->getAssetsManifestItem(static::BLOCKS_EDITOR_SCRIPT_URI),
			$this->getAdminScriptDependencies(),
			$this->getAssetsVersion(),
			$this->scriptInFooter()
		);

		\wp_enqueue_script($handle);

		foreach ($this->getLocalizations() as $objectName => $dataArray) {
			\wp_localize_script($handle, $objectName, $dataArray);
		}
	}

	/**
	 * Get block editor Stylesheet handle.
	 *
	 * @return string
	 */
	public function getBlockEditorStyleHandle(): string
	{
		return "{$this->getAssetsPrefix()}-block-editor-style";
	}

	/**
	 * Method that returns assets hook used to determine hook usage.
	 *
	 * @param string $hook Hook name.
	 *
	 * @return boolean
	 */
	public function isEnqueueBlockEditorStyleUsed(string $hook): bool
	{
		return true;
	}

	/**
	 * Enqueue blocks style for editor only.
	 *
	 * @param string $hook Hook name.
	 *
	 * @return void
	 */
	public function enqueueBlockEditorStyle(string $hook): void
	{
		if (!$this->isEnqueueBlockEditorStyleUsed($hook)) {
			return;
		}

		$handle = $this->getBlockEditorStyleHandle();

		\wp_register_style(
			$handle,
			$this->manifest->getAssetsManifestItem(static::BLOCKS_EDITOR_STYLE_URI),
			$this->getAdminStyleDependencies(),
			$this->getAssetsVersion(),
			$this->getMedia()
		);

		\wp_enqueue_style($handle);
	}

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
	 * Method that returns assets hook used to determine hook usage.
	 *
	 * @param string $hook Hook name.
	 *
	 * @return boolean
	 */
	public function isEnqueueBlockStyleUsed(string $hook): bool
	{
		return true;
	}

	/**
	 * Enqueue blocks style for editor and frontend.
	 *
	 * @param string $hook Hook name.
	 *
	 * @return void
	 */
	public function enqueueBlockStyle(string $hook): void
	{
		if (!$this->isEnqueueBlockStyleUsed($hook)) {
			return;
		}

		$handle = $this->getBlockStyleHandle();

		\wp_register_style(
			$handle,
			$this->manifest->getAssetsManifestItem(static::BLOCKS_STYLE_URI),
			$this->getFrontendStyleDependencies(),
			$this->getAssetsVersion(),
			$this->getMedia()
		);

		\wp_enqueue_style($handle);
	}

	/**
	 * Get block frontend JavaScript handle.
	 *
	 * @return string
	 */
	public function getBlockFrontentScriptHandle(): string
	{
		return "{$this->getAssetsPrefix()}-block-frontend-scripts";
	}

	/**
	 * Method that returns assets hook used to determine hook usage.
	 *
	 * @param string $hook Hook name.
	 *
	 * @return boolean
	 */
	public function isEnqueueBlockFrontendScriptUsed(string $hook): bool
	{
		return true;
	}

	/**
	 * Enqueue blocks script for frontend only.
	 *
	 * @param string $hook Hook name.
	 *
	 * @return void
	 */
	public function enqueueBlockFrontendScript(string $hook): void
	{
		if (!$this->isEnqueueBlockFrontendScriptUsed($hook)) {
			return;
		}

		$handle = $this->getBlockFrontentScriptHandle();

		\wp_register_script(
			$handle,
			$this->manifest->getAssetsManifestItem(static::BLOCKS_FRONTEND_SCRIPT_URI),
			$this->getFrontendScriptDependencies(),
			$this->getAssetsVersion(),
			$this->scriptInFooter()
		);

		\wp_enqueue_script($handle);


		foreach ($this->getLocalizations() as $objectName => $dataArray) {
			\wp_localize_script($this->getBlockFrontentScriptHandle(), $objectName, $dataArray);
		}
	}

	/**
	 * Get block frontend Stylesheet handle.
	 *
	 * @return string
	 */
	public function getBlockFrontentStyleHandle(): string
	{
		return "{$this->getAssetsPrefix()}-block-frontend-style";
	}

	/**
	 * Method that returns assets hook used to determine hook usage.
	 *
	 * @param string $hook Hook name.
	 *
	 * @return boolean
	 */
	public function isEnqueueBlockFrontendStyleUsed(string $hook): bool
	{
		return true;
	}

	/**
	 * Enqueue blocks style for frontend only.
	 *
	 * @param string $hook Hook name.
	 *
	 * @return void
	 */
	public function enqueueBlockFrontendStyle(string $hook): void
	{
		if (!$this->isEnqueueBlockFrontendStyleUsed($hook)) {
			return;
		}

		$handle = $this->getBlockFrontentStyleHandle();

		\wp_register_style(
			$handle,
			$this->manifest->getAssetsManifestItem(static::BLOCKS_FRONTEND_STYLE_URI),
			$this->getFrontendStyleDependencies(),
			$this->getAssetsVersion(),
			$this->getMedia()
		);

		\wp_enqueue_style($handle);
	}

	/**
	 * List of admin script dependencies
	 *
	 * @return string[] List of all the admin dependencies.
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
			'lodash',
		];
	}
}
