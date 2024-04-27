<?php

/**
 * File containing an abstract class for holding Assets Manifest functionality.
 *
 * It is used to provide manifest.json file location used with Webpack to fetch correct file locations.
 *
 * @package EightshiftLibs\Manifest
 */

declare(strict_types=1);

namespace EightshiftFormsVendor\EightshiftLibs\Manifest;

use EightshiftLibs\Cache\AbstractManifestCache;
use EightshiftLibs\Cache\ManifestCacheInterface;
use EightshiftLibs\Exception\InvalidManifest;
use EightshiftLibs\Helpers\Components;
use EightshiftLibs\Services\ServiceInterface;

/**
 * Abstract class Manifest class.
 */
abstract class AbstractManifest implements ServiceInterface, ManifestInterface
{
	/**
	 * Instance variable for manifest cache.
	 */
	protected $manifestCache;

	/**
	 * Create a new instance.
	 *
	 * @param ManifestCacheInterface $manifestCache Inject manifest cache.
	 */
	public function __construct(ManifestCacheInterface $manifestCache) {
		$this->manifestCache = $manifestCache;
	}

	/**
	 * Set the manifest data with site url prefix.
	 * You should never call this method directly instead you should call $this->manifest.
	 *
	 * @throws InvalidManifest Throws error if manifest.json file is missing.
	 *
	 * @return void Sets the manifest variable to cache.
	 */
	public function setAssetsManifest(): void
	{
		$items = $this->manifestCache->getManifestCacheTopItem(AbstractManifestCache::ASSETS_KEY, AbstractManifestCache::TYPE_ASSETS);

		$path = $items['path'] ?? '';
		$data = $items['data'] ?? [];

		if (!$data) {
			throw InvalidManifest::emptyOrErrorManifestException($path);
		}

		$output = \array_map(
			function ($item) {
				$sep = \DIRECTORY_SEPARATOR;
				$path = rtrim($this->getAssetsManifestOutputPrefix(), $sep);
				$item = ltrim($item, $sep);

				return "{$path}{$sep}{$item}";
			},
			$data
		);

		Components::setStore();
		Components::setAssets($output);
	}

	/**
	 * Get the manifest data.
	 *
	 * @param string $key The key from the manifest.json file.
	 *
	 * @throws InvalidManifest Throws error if manifest.json file is missing.
	 *
	 * @return string The value from the manifest.json file.
	 */
	public function getAssetsManifestItem(string $key): string
	{
		$items = $this->manifestCache->getManifestCacheTopItem(AbstractManifestCache::ASSETS_KEY, AbstractManifestCache::TYPE_ASSETS);

		$path = $items['path'] ?? '';
		$data = $items['data'] ?? '';

		if (!$data) {
			throw InvalidManifest::emptyOrErrorManifestException($path);
		}

		if (!isset($data[$key])) {
			throw InvalidManifest::missingManifestKeyException($key, $path);
		}

		return $data[$key] ?? '';
	}

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
