<?php

/**
 * File containing an abstract class for holding Assets Manifest functionality.
 *
 * It is used to provide manifest.json file location used with Webpack to fetch correct file locations.
 *
 * @package EightshiftLibs\Manifest
 */

declare(strict_types=1);

namespace EightshiftLibs\Manifest;

use EightshiftLibs\Exception\InvalidManifest;
use EightshiftLibs\Services\ServiceInterface;

/**
 * Abstract class Manifest class.
 */
abstract class AbstractManifest implements ServiceInterface, ManifestInterface
{

	/**
	 * Full data of manifest items.
	 *
	 * @var array
	 */
	protected $manifest = [];

	/**
	 * Set the manifest data with site url prefix.
	 * You should never call this method directly instead you should call $this->manifest.
	 *
	 * @throws InvalidManifest Throws error if manifest.json file is missing.
	 *
	 * @return void Sets the manifest variable.
	 */
	public function setAssetsManifestRaw(): void
	{
		if (defined('WP_CLI') && !getenv('TEST')) {
			return;
		}

		$path = $this->getManifestFilePath();

		if (!file_exists($path)) {
			throw InvalidManifest::missingManifestException($path);
		}

		$data = json_decode(implode(' ', (array)file($path)), true);

		if (empty($data)) {
			return;
		}

		$this->manifest = array_map(
			function ($manifestItem) {
				return "{$this->getAssetsManifestOutputPrefix()}{$manifestItem}";
			},
			$data
		);
	}

	/**
	 * Return full path for specific asset from manifest.json.
	 *
	 * @param string $key File name key you want to get from manifest.
	 *
	 * @throws InvalidManifest Throws error if manifest key is missing.
	 *                         Returns data from manifest and not global variable.
	 *
	 * @return string Full path to asset.
	 */
	public function getAssetsManifestItem(string $key): string
	{
		if (defined('WP_CLI') && !getenv('TEST')) {
			return '';
		}

		$manifest = $this->manifest;

		if (!isset($manifest[$key])) {
			throw InvalidManifest::missingManifestItemException($key);
		}

		return $manifest[$key];
	}

	/**
	 * Manifest file path getter.
	 *
	 * @return string
	 */
	abstract protected function getManifestFilePath(): string;

	/**
	 * This method appends full site url to the relative manifest data item.
	 *
	 * @return string
	 */
	protected function getAssetsManifestOutputPrefix(): string
	{
		return \site_url();
	}
}
