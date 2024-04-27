<?php

/**
 * The Admin Enqueue specific functionality.
 *
 * @package EightshiftLibs\Enqueue\Admin
 */

declare(strict_types=1);

namespace EightshiftLibs\Enqueue\Admin;

use EightshiftLibs\Enqueue\AbstractAssets;
use EightshiftLibs\Helpers\Components;

/**
 * Class EnqueueAdmin
 *
 * This class handles enqueue scripts and styles.
 */
abstract class AbstractEnqueueAdmin extends AbstractAssets
{
	/**
	 * Admin script handle.
	 *
	 * @return string
	 */
	public const ADMIN_SCRIPT_URI = 'applicationAdmin.js';

	/**
	 * Admin style handle.
	 *
	 * @return string
	 */
	public const ADMIN_STYLE_URI = 'applicationAdmin.css';

	/**
	 * Get admin Stylesheet handle.
	 *
	 * @return string
	 */
	public function getAdminStyleHandle(): string
	{
		return "{$this->getAssetsPrefix()}-styles";
	}

	/**
	 * Method that returns assets hook used to determine hook usage.
	 *
	 * @param string $hook Hook name.
	 *
	 * @return boolean
	 */
	public function isEnqueueStylesUsed(string $hook): bool
	{
		return true;
	}

	/**
	 * Register the Stylesheets for the admin area.
	 *
	 * @param string $hook Hook name.
	 *
	 * @return void
	 */
	public function enqueueStyles(string $hook): void
	{
		if (!$this->isEnqueueStylesUsed($hook)) {
			return;
		}

		if (!$this->getConditionUse()) {
			$handle = $this->getAdminStyleHandle();

			\wp_register_style(
				$handle,
				Components::getAsset(static::ADMIN_STYLE_URI),
				$this->getAdminStyleDependencies(),
				$this->getAssetsVersion(),
				$this->getMedia()
			);

			\wp_enqueue_style($handle);
		}
	}

	/**
	 * Get admin JavaScript handle.
	 *
	 * @return string
	 */
	public function getAdminScriptHandle(): string
	{
		return "{$this->getAssetsPrefix()}-scripts";
	}

	/**
	 * Method that returns assets hook used to determine hook usage.
	 *
	 * @param string $hook Hook name.
	 *
	 * @return boolean
	 */
	public function isEnqueueScriptsUsed(string $hook): bool
	{
		return true;
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @param string $hook Hook name.
	 *
	 * @return void
	 */
	public function enqueueScripts(string $hook): void
	{
		if (!$this->isEnqueueScriptsUsed($hook)) {
			return;
		}

		if (!$this->getConditionUse()) {
			$handle = $this->getAdminScriptHandle();

			\wp_register_script(
				$handle,
				Components::getAsset(static::ADMIN_SCRIPT_URI),
				$this->getAdminScriptDependencies(),
				$this->getAssetsVersion(),
				$this->scriptInFooter()
			);

			\wp_enqueue_script($handle);

			foreach ($this->getLocalizations() as $objectName => $dataArray) {
				\wp_localize_script($handle, $objectName, $dataArray);
			}
		}
	}

	/**
	 * Condition script usage.
	 *
	 * @return boolean
	 */
	public function getConditionUse(): bool
	{
		if (!\is_admin()) {
			return false;
		}

		$screen = \get_current_screen();

		if (\is_object($screen) && $screen->is_block_editor) { // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
			return true;
		}

		return false;
	}
}
