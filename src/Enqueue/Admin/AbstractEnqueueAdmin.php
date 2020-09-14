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
	public const ADMIN_STYLE_URI  = 'applicationAdmin.css';

	/**
	 * Instance variable of manifest data.
	 *
	 * @var ManifestInterface
	 */
	protected $manifest;

	/**
	 * Register all the hooks
	 *
	 * @return void
	 */
	public function register(): void
	{
		add_action('login_enqueue_scripts', [ $this, 'enqueueStyles' ]);
		add_action('admin_enqueue_scripts', [ $this, 'enqueueStyles' ], 50);
		add_action('admin_enqueue_scripts', [ $this, 'enqueueScripts' ]);
	}

	/**
	 * Register the Stylesheets for the admin area.
	 *
	 * @return void
	 */
	public function enqueueStyles(): void
	{
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

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @return void
	 */
	public function enqueueScripts(): void
	{
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
