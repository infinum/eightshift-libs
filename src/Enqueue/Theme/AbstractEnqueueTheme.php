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
	 */
	public function getThemeStyleHandle(): string
	{
		return "{$this->getAssetsPrefix()}-theme-styles";
	}

	/**
	 * Get theme JavaScript handle.
	 */
	public function getThemeScriptHandle(): string
	{
		return "{$this->getAssetsPrefix()}-theme-scripts";
	}

	/**
	 * Register the Stylesheets for the front end of the theme.
	 */
	public function enqueueThemeStyles(): void
	{
		$handle = $this->getThemeStyleHandle();

		\wp_register_style(
			$handle,
			$this->setAssetsItem(static::THEME_STYLE_URI),
			$this->getThemeStyleDependencies(),
			$this->getAssetsVersion(),
			$this->getMedia()
		);

		$this->enqueueSharedFonts();

		\wp_enqueue_style($handle);
	}

	/**
	 * Register the JavaScript for the front end of the theme.
	 */
	public function enqueueThemeScripts(): void
	{
		$handle = $this->getThemeScriptHandle();

		\wp_register_script(
			$handle,
			$this->setAssetsItem(static::THEME_SCRIPT_URI),
			$this->mergeAssetDependencies(static::THEME_SCRIPT_URI, $this->getThemeScriptDependencies()),
			$this->getAssetsVersion(),
			\is_wp_version_compatible('6.3') ? $this->scriptArgs() : $this->scriptInFooter()
		);

		\wp_enqueue_script($handle);
		\add_action('wp_print_footer_scripts', $this->diagnoseMissingThemeScriptDependencies(...), \PHP_INT_MAX);

		foreach ($this->getLocalizations() as $objectName => $dataArray) {
			\wp_localize_script($handle, $objectName, $dataArray);
		}
	}

	/**
	 * Report unresolved dependencies after scripts have had a chance to print.
	 */
	public function diagnoseMissingThemeScriptDependencies(): void
	{
		$scripts = \wp_scripts();
		$handle = $this->getThemeScriptHandle();
		if (!\in_array($handle, $scripts->queue, true) || \in_array($handle, $scripts->done, true)) {
			return;
		}

		$pending = [$handle];
		$visited = [];
		$missing = [];
		while ($pending !== []) {
			$dependency = \array_pop($pending);
			$dependency = \explode('?', $dependency, 2)[0];
			if (isset($visited[$dependency])) {
				continue;
			}
			$visited[$dependency] = true;
			if (!isset($scripts->registered[$dependency])) {
				$missing[] = $dependency;
				continue;
			}
			foreach ($scripts->registered[$dependency]->deps as $child) {
				$pending[] = $child;
			}
		}

		if ($missing !== []) {
			\_doing_it_wrong(__METHOD__, 'Theme script ' . \esc_html($handle) . ' has unregistered dependencies: ' . \esc_html(\implode(', ', $missing)), '');
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
