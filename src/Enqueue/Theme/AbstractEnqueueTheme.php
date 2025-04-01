<?php

/**
 * The Theme/Frontend Enqueue specific functionality.
 *
 * @package EightshiftLibs\Enqueue\Theme
 */

declare(strict_types=1);

namespace EightshiftLibs\Enqueue\Theme;

use EightshiftLibs\Enqueue\AbstractAssets;

/**
 * Class Enqueue
 */
abstract class AbstractEnqueueTheme extends AbstractAssets
{
	/**
	 * Get the theme script handle.
	 *
	 * @return array<string, mixed>
	 */
	public const THEME_SCRIPT_URI = 'application.js';

	/**
	 * Get the theme style handle.
	 *
	 * @return array<string, mixed>
	 */
	public const THEME_STYLE_URI = 'application.css';

	/**
	 * Get theme Stylesheet handle.
	 *
	 * @return string
	 */
	public function getThemeStyleHandle(): string
	{
		return "{$this->getAssetsPrefix()}-theme-styles";
	}

	/**
	 * Get theme JavaScript handle.
	 *
	 * @return string
	 */
	public function getThemeScriptHandle(): string
	{
		return "{$this->getAssetsPrefix()}-theme-scripts";
	}

	/**
	 * Register the Stylesheets for the front end of the theme.
	 *
	 * @return void
	 */
	public function enqueueStyles(): void
	{
		$handle = $this->getThemeStyleHandle();

		\wp_register_style(
			$handle,
			$this->setAssetsItem(static::THEME_STYLE_URI),
			$this->getThemeStyleDependencies(),
			$this->getAssetsVersion(),
			$this->getMedia()
		);

		\wp_enqueue_style($handle);
	}

	/**
	 * Register the JavaScript for the front end of the theme.
	 *
	 * @return void
	 */
	public function enqueueScripts(): void
	{
		$handle = $this->getThemeScriptHandle();

		\wp_register_script(
			$handle,
			$this->setAssetsItem(static::THEME_SCRIPT_URI),
			$this->getThemeScriptDependencies(),
			$this->getAssetsVersion(),
			\is_wp_version_compatible('6.3') ? $this->scriptArgs() : $this->scriptInFooter()
		);

		\wp_enqueue_script($handle);

		foreach ($this->getLocalizations() as $objectName => $dataArray) {
			\wp_localize_script($handle, $objectName, $dataArray);
		}
	}

	/**
	 * Get script dependencies
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_script/#default-scripts-included-and-registered-by-wordpress
	 *
	 * @return array<int, string> List of all the script dependencies.
	 */
	protected function getThemeScriptDependencies(): array
	{
		return [];
	}

	/**
	 * Get style dependencies
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_style/
	 *
	 * @return array<int, string> List of all the style dependencies.
	 */
	protected function getThemeStyleDependencies(): array
	{
		return [];
	}
}
