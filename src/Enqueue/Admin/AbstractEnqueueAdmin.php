<?php

/**
 * The Admin Enqueue specific functionality.
 *
 * @package EightshiftLibs\Enqueue\Admin
 */

declare(strict_types=1);

namespace EightshiftLibs\Enqueue\Admin;

use EightshiftLibs\Enqueue\AbstractAssets;

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
	 * Register the Stylesheets for the admin area.
	 *
	 * @return void
	 */
	public function enqueueAdminStyles(): void
	{
		if (!$this->getConditionUse()) {
			$handle = $this->getAdminStyleHandle();

			\wp_register_style(
				$handle,
				$this->setAssetsItem(static::ADMIN_STYLE_URI),
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
	 * Register the JavaScript for the admin area.
	 *
	 * @return void
	 */
	public function enqueueAdminScripts(): void
	{
		if (!$this->getConditionUse()) {
			$handle = $this->getAdminScriptHandle();

			\wp_register_script(
				$handle,
				$this->setAssetsItem(static::ADMIN_SCRIPT_URI),
				$this->getAdminScriptDependencies(),
				$this->getAssetsVersion(),
				\is_wp_version_compatible('6.3') ? $this->scriptArgs() : $this->scriptInFooter()
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

	/**
	 * Get script dependencies
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_script/#default-scripts-included-and-registered-by-wordpress
	 *
	 * @return array<int, string> List of all the script dependencies.
	 */
	protected function getAdminScriptDependencies(): array
	{
		return ['wp-element', 'wp-i18n', 'wp-api-fetch', 'wp-blocks'];
	}

	/**
	 * Get style dependencies
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_style/
	 *
	 * @return array<int, string> List of all the style dependencies.
	 */
	protected function getAdminStyleDependencies(): array
	{
		return [];
	}
}
