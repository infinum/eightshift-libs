<?php

/**
 * The Theme/Frontend Enqueue specific functionality.
 *
 * @package EightshiftLibs\Enqueue\Theme
 */

declare(strict_types=1);

namespace EightshiftLibs\Enqueue\Theme;

use EightshiftLibs\Enqueue\AbstractAssets;
use EightshiftLibs\Manifest\ManifestInterface;

/**
 * Class Enqueue
 */
abstract class AbstractEnqueueTheme extends AbstractAssets
{

	public const THEME_SCRIPT_URI = 'application.js';
	public const THEME_STYLE_URI  = 'application.css';

	/**
	 * Instance variable of manifest data.
	 *
	 * @var ManifestInterface
	 */
	protected $manifest;

	/**
	 * Register the Stylesheets for the front end of the theme.
	 *
	 * @return void
	 */
	public function enqueueStyles(): void
	{
		$handle = "{$this->getAssetsPrefix()}-theme-styles";

		\wp_register_style(
			$handle,
			$this->manifest->getAssetsManifestItem(static::THEME_STYLE_URI),
			$this->getFrontendStyleDependencies(),
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
		$handle = "{$this->getAssetsPrefix()}-scripts";

		\wp_register_script(
			$handle,
			$this->manifest->getAssetsManifestItem(static::THEME_SCRIPT_URI),
			$this->getFrontendScriptDependencies(),
			$this->getAssetsVersion(),
			$this->scriptInFooter()
		);

		\wp_enqueue_script($handle);

		foreach ($this->getLocalizations() as $objectName => $dataArray) {
			\wp_localize_script($handle, $objectName, $dataArray);
		}
	}
}
