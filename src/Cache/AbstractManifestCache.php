<?php

/**
 * File containing an abstract class for holding Manifest Cache functionality.
 *
 * It is used to provide manifest.json file location stored in the transient cache.
 *
 * @package EightshiftFormsVendor\EightshiftLibs\Cache
 */

declare(strict_types=1);

namespace EightshiftLibs\Cache;

use EightshiftLibs\Exception\InvalidManifest;
use EightshiftLibs\Helpers\Helpers;

/**
 * Abstract class AbstractManifestCache class.
 */
abstract class AbstractManifestCache implements ManifestCacheInterface
{
	/**
	 * Transient name prefix.
	 *
	 * @var string
	 */
	private const TRANSIENT_NAME = 'eightshift_manifest_cache_';

	/**
	 * Cache key - blocks.
	 *
	 * @var string
	 */
	public const BLOCKS_KEY = 'blocks';

	/**
	 * Cache key - components.
	 *
	 * @var string
	 */
	public const COMPONENTS_KEY = 'components';

	/**
	 * Cache key - variations.
	 *
	 * @var string
	 */
	public const VARIATIONS_KEY = 'variations';

	/**
	 * Cache key - wrapper.
	 *
	 * @var string
	 */
	public const WRAPPER_KEY = 'wrapper';

	/**
	 * Cache key - settings.
	 *
	 * @var string
	 */
	public const SETTINGS_KEY = 'settings';

	/**
	 * Cache key - assets.
	 *
	 * @var string
	 */
	public const ASSETS_KEY = 'assets';

	/**
	 * Cache key - config.
	 *
	 * @var string
	 */
	public const CONFIG_KEY = 'config';

	/**
	 * Cache key - styles.
	 *
	 * @var string
	 */
	public const STYLES_KEY = 'styles';

	/**
	 * Cache key - countries.
	 *
	 * @var string
	 */
	public const COUNTRIES_KEY = 'countries';

	/**
	 * Cache key for blocks.
	 *
	 * @var string
	 */
	public const TYPE_BLOCKS = 'blocks';

	/**
	 * Cache key for assets.
	 *
	 * @var string
	 */
	public const TYPE_ASSETS = 'assets';

	/**
	 * Cache key for geolocation.
	 *
	 * @var string
	 */
	public const TYPE_GEOLOCATION = 'geolocation';

	/**
	 * Namespace for blocks.
	 *
	 * @var string
	 */
	private $blocksNamespace = '';

	/**
	 * Get cache name.
	 *
	 * @return string Cache name.
	 */
	abstract public function getCacheName(): string;

	/**
	 * Get cache duration.
	 * Default is 0 = infinite.
	 *
	 * @return int Cache duration.
	 */
	public function getDuration(): int
	{
		return 0;
	}

	/**
	 * Get manifest cache top item.
	 *
	 * @param string $key Key of the cache.
	 * @param string $cacheType Type of the cache.
	 *
	 * @throws InvalidManifest If cache item is missing.
	 *
	 * @return array<string, mixed> Array of cache item.
	 */
	public function getManifestCacheTopItem(string $key, string $cacheType = self::TYPE_BLOCKS): array
	{
		$output = [];

		if ((\defined('WP_ENVIRONMENT_TYPE') && \WP_ENVIRONMENT_TYPE !== 'development') && !\defined('WP_CLI')) {
			$output = $this->getCache($cacheType)[$key] ?? [];
		}

		if (!$output) {
			$output = $this->getAllManifests($cacheType)[$key] ?? [];
		}

		if (!$output && !\defined('WP_CLI')) {
			throw InvalidManifest::missingCacheTopItemException($key, $this->getFullPath($key, $cacheType));
		}

		return $output;
	}

	/**
	 * Get manifest cache subitem.
	 *
	 * @param string $key Key of the cache.
	 * @param string $name Name of the subitem.
	 * @param string $cacheType Type of the cache.
	 *
	 * @throws InvalidManifest If cache subitem is missing.
	 *
	 * @return array<string, mixed> Array of cache item.
	 */
	public function getManifestCacheSubItem(string $key, string $name, string $cacheType = self::TYPE_BLOCKS): array
	{
		$output = $this->getManifestCacheTopItem($key, $cacheType)[$name] ?? [];

		if (!$output) {
			throw InvalidManifest::missingCacheSubItemException($key, $name, $this->getFullPath($key, $cacheType, $name));
		}

		return $output;
	}

	/**
	 * Set all cache.
	 *
	 * @param array<string> $ignoreCache Array of cache to ignore.
	 *
	 * @return void
	 */
	public function setAllCache($ignoreCache = []): void
	{
		$ignoreCache = \array_flip($ignoreCache);

		if (!isset($ignoreCache[self::TYPE_BLOCKS])) {
			$this->setCache(self::TYPE_BLOCKS);
		}

		if (!isset($ignoreCache[self::TYPE_ASSETS])) {
			$this->setCache(self::TYPE_ASSETS);
			$this->setAssetsStaticCache();
		}

		if (!isset($ignoreCache[self::TYPE_GEOLOCATION])) {
			$this->setCache(self::TYPE_GEOLOCATION);
		}
	}

	/**
	 * Unset all manifest cache.
	 *
	 * @return void
	 */
	public function deleteAllCache(): void
	{
		$data = \array_keys($this->getCacheBuilder());

		foreach ($data as $cache) {
			$this->deleteCache($cache);
		}
	}

	/**
	 * Unset cache item by type.
	 *
	 * @param string $cacheType Type of the cache.
	 *
	 * @return void
	 */
	public function deleteCache(string $cacheType = self::TYPE_BLOCKS): void
	{
		\delete_transient(self::TRANSIENT_NAME . $this->getCacheName() . "_{$cacheType}");
	}

	/**
	 * Set assets static cache to the store helpers used in view files.
	 *
	 * @return void
	 */
	protected function setAssetsStaticCache(): void
	{
		Helpers::setStore();
		Helpers::setAssets($this->getManifestCacheTopItem(self::ASSETS_KEY, self::TYPE_ASSETS));
	}

	/**
	 * Set cache.
	 *
	 * @param string $cacheType Type of the cache.
	 *
	 * @return void
	 */
	protected function setCache(string $cacheType = self::TYPE_BLOCKS): void
	{
		$name = self::TRANSIENT_NAME . $this->getCacheName() . "_{$cacheType}";

		$cache = \get_transient($name);

		if (!$cache) {
			\set_transient($name, \wp_json_encode($this->getAllManifests($cacheType)), $this->getDuration());
		}
	}

	/**
	 * Get cache.
	 *
	 * @param string $cacheType Type of the cache.
	 *
	 * @return array<string, array<mixed>> Array of cache.
	 */
	protected function getCache(string $cacheType = self::TYPE_BLOCKS): array
	{
		$cache = \get_transient(self::TRANSIENT_NAME . $this->getCacheName() . "_{$cacheType}") ?: ''; // phpcs:ignore WordPress.PHP.DisallowShortTernary.Found

		if (!$cache) {
			$this->setCache();
		}

		return \json_decode($cache, true) ?? [];
	}

	/**
	 * Get cache builder.
	 *
	 * @return array<string, array<mixed>> Array of cache builder.
	 */
	protected function getCacheBuilder(): array
	{
		$sep = \DIRECTORY_SEPARATOR;

		return [
			self::TYPE_BLOCKS => [
				self::SETTINGS_KEY => [
					'path' => 'blocksDestination',
					'multiple' => false,
					'validation' => [
						'$schema',
						'namespace',
						'background',
						'foreground',
					],
				],
				self::BLOCKS_KEY => [
					'path' => 'blocksDestinationCustom',
					'id' => 'blockName',
					'multiple' => true,
					'autoset' => [
						'classes' => 'array',
						'attributes' => 'array',
						'hasInnerBlocks' => 'boolean',
					],
					'validation' => [
						'$schema',
						'title',
						'description',
						'namespace',
						'blockName',
						'blockFullName',
						'keywords',
						'icon',
						'category',
					],
				],
				self::COMPONENTS_KEY => [
					'path' => 'blocksDestinationComponents',
					'multiple' => true,
					'id' => 'componentName',
					'validation' => [
						'$schema',
						'title',
						'componentName',
					],
				],
				self::VARIATIONS_KEY => [
					'path' => 'blocksDestinationVariations',
					'id' => 'name',
					'multiple' => true,
					'validation' => [
						'$schema',
						'title',
						'description',
						'icon',
						'name',
						'parentName',
					],
				],
				self::WRAPPER_KEY => [
					'path' => 'blocksDestinationWrapper',
					'validation' => [
						'$schema',
						'title',
					],
				],
			],
			self::TYPE_ASSETS => [
				self::ASSETS_KEY => [
					'path' => 'themeRoot',
					'fileName' => "public{$sep}manifest.json",
				],
			],
			self::TYPE_GEOLOCATION => [
				self::COUNTRIES_KEY => [
					'path' => 'libs',
					'pathAlternative' => 'libsPrefixed',
					'fileName' => "src{$sep}Geolocation{$sep}manifest.json",
				],
			],
		];
	}

	/**
	 * Get all manifests from the paths.
	 *
	 * @param string $cacheType Type of the cache.
	 *
	 * @return array<string, array<mixed>> Array of manifests.
	 */
	private function getAllManifests(string $cacheType = self::TYPE_BLOCKS): array
	{
		$output = [];

		foreach ($this->getCacheBuilder()[$cacheType] ?? [] as $parent => $data) {
			$multiple = $data['multiple'] ?? false;

			if ($multiple) {
				$output[$parent] = $this->geItems($this->getFullPath($parent, $cacheType, '*'), $data, $parent);
			} else {
				$output[$parent] = $this->getItem($this->getFullPath($parent, $cacheType), $data, $parent);
			}
		}

		return $output;
	}

	/**
	 * Get single item from the path.
	 *
	 * @param string $path Path to the item.
	 * @param array<mixed> $data Data array.
	 * @param string $parent Parent key.
	 *
	 * @throws InvalidManifest If manifest key is missing.
	 *
	 * @return array<string, mixed> Item.
	 */
	private function getItem(string $path, array $data, string $parent): array
	{
		if (!\file_exists($path)) {
			return [];
		}

		$file = \file_get_contents($path); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		if (!$file) {
			return [];
		}

		$fileDecoded = \json_decode($file, true);

		if (!$fileDecoded) {
			return [];
		}

		$autoset = $data['autoset'] ?? [];

		if ($autoset) {
			foreach ($autoset as $key => $type) {
				if (isset($fileDecoded[$key])) {
					continue;
				}

				switch ($type) {
					case 'array':
						$fileDecoded[$key] = [];
						break;
					case 'boolean':
						$fileDecoded[$key] = false;
						break;
					default:
						$fileDecoded[$key] = '';
						break;
				}
			}
		}

		switch ($parent) {
			case self::BLOCKS_KEY:
				if ($this->blocksNamespace) {
					$fileDecoded['namespace'] = $this->blocksNamespace;
					$fileDecoded['blockFullName'] = "{$this->blocksNamespace}/{$fileDecoded['blockName']}";
				}
				break;
			case self::SETTINGS_KEY:
				$this->blocksNamespace = $fileDecoded['namespace'] ?? '';
				break;
		}

		$validation = $data['validation'] ?? [];

		if ($validation) {
			foreach ($validation as $key) {
				if (!isset($fileDecoded[$key])) {
					throw InvalidManifest::missingManifestKeyException($key, $path);
				}
			}
		}

		return $fileDecoded;
	}

	/**
	 * Get multiple items from the path.
	 *
	 * @param string $path Path to the items.
	 * @param array<mixed> $data Data array.
	 * @param string $parent Parent key.
	 *
	 * @return array<string, array<mixed>> Array of items.
	 */
	private function geItems(string $path, array $data, string $parent): array
	{
		$output = [];

		$id = $data['id'] ?? '';

		foreach ((array)\glob($path) as $itemPath) {
			$item = $this->getItem($itemPath, $data, $parent);

			$idName = $item[$id] ?? '';

			if (!$idName) {
				continue;
			}

			$output[$idName] = $item;
		}

		return $output;
	}

	/**
	 * Get full path.
	 *
	 * @param string $type Type of the item.
	 * @param string $cacheType Type of the cache.
	 * @param string $name Name of the item.
	 *
	 * @return string Full path.
	 */
	private function getFullPath($type, string $cacheType = self::TYPE_BLOCKS, $name = ''): string
	{
		$data = $this->getCacheBuilder()[$cacheType][$type] ?? [];

		if (!$data) {
			return '';
		}

		$path = $data['path'] ?? '';
		$pathAlternative = $data['pathAlternative'] ?? '';
		$pathCustom = $data['pathCustom'] ?? '';
		$fileName = $data['fileName'] ?? 'manifest.json';

		$realPath = Helpers::getProjectPaths($path);

		if (!\is_dir($realPath) && $pathAlternative) {
			$realPath = Helpers::getProjectPaths($pathAlternative);
		}

		if ($pathCustom) {
			$realPath = $pathCustom;
		}

		if (!$name) {
			return Helpers::joinPaths([$realPath, $fileName]);
		}

		return Helpers::joinPaths([$realPath, $name, $fileName]);
	}
}
