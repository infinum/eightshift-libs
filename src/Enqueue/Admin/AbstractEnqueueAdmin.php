<?php

/**
 * The Admin Enqueue specific functionality.
 *
 * @package EightshiftLibs\Enqueue\Admin
 */

declare(strict_types=1);

namespace EightshiftLibs\Enqueue\Admin;

use EightshiftLibs\Enqueue\AbstractAssets;
use EightshiftLibs\Manifest\ManifestInterface;

/**
 * Class EnqueueAdmin
 *
 * This class handles enqueue scripts and styles.
 */
abstract class AbstractEnqueueAdmin extends AbstractAssets
{

	public const ADMIN_SCRIPT_URI = 'applicationAdmin.js';
	public const ADMIN_STYLE_URI = 'applicationAdmin.css';

	/**
	 * Instance variable of manifest data.
	 *
	 * @var ManifestInterface
	 */
	protected $manifest;

	/**
	 * Register the Stylesheets for the admin area.
	 *
	 * @return void
	 */
	public function enqueueStyles(): void
	{
		if (!$this->getConditionUse()) {
			$handle = "{$this->getAssetsPrefix()}-styles";

			\wp_register_style(
				$handle,
				$this->manifest->getAssetsManifestItem(static::ADMIN_STYLE_URI),
				$this->getAdminStyleDependencies(),
				$this->getAssetsVersion(),
				$this->getMedia()
			);

			\wp_enqueue_style($handle);
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @return void
	 */
	public function enqueueScripts(): void
	{
		if (!$this->getConditionUse()) {
			$handle = "{$this->getAssetsPrefix()}-scripts";

			\wp_register_script(
				$handle,
				$this->manifest->getAssetsManifestItem(static::ADMIN_SCRIPT_URI),
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

		if (is_object($screen) && $screen->is_block_editor) { // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
			return true;
		}

		return false;
	}
}
