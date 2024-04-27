<?php

/**
 * File containing an abstract class for holding Manifest Cache functionality.
 *
 * It is used to provide manifest.json file location stored in the transient cache.
 *
 * @package EightshiftLibs\Cache
 */

declare(strict_types=1);

namespace EightshiftLibs\Cache;

use EightshiftLibs\Helpers\Components;

/**
 * Abstract class AbstractManifestCache class.
 */
abstract class AbstractManifestCache implements ManifestCacheInterface
{
	private const TRANSIENT_NAME = 'eightshift_manifest_cache_';

	public const BLOCKS_KEY = 'blocks';
	public const COMPONENTS_KEY = 'components';
	public const VARIATIONS_KEY = 'variations';
	public const WRAPPER_KEY = 'wrapper';
	public const SETTINGS_KEY = 'settings';
	public const ASSETS_KEY = 'assets';
	public const CONFIG_KEY = 'config';
	public const STYLES_KEY = 'styles';
	public const PATHS_KEY = 'paths';

	public const TYPE_BLOCKS = 'blocks';
	public const TYPE_ASSETS = 'assets';

	private $blocksNamespace = '';

	/**
	 * Get cache name.
	 *
	 * @return string Cache name.
	 */
	public abstract function getCacheName(): string;

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
	 * @return array<string, mixed> Array of cache item.
	 */
	public function getManifestCacheTopItem(string $key, string $cacheType = self::TYPE_BLOCKS): array
	{
		$output = [
			'data' => [],
			'path' => $this->getFullPath($key),
			'key' => $key,
		];

		$data = [];

		if (\defined('WP_ENVIRONMENT_TYPE') && \WP_ENVIRONMENT_TYPE !== 'development') {
			$data = $this->getCache($cacheType)[$key] ?? [];
		}

		if (!$data) {
			$data = $this->getAllManifests()[$key] ?? [];
		}

		if (!$data) {
			return $output;
		}

		$output['data'] = $data;

		return $output;
	}

	/**
	 * Get manifest cache subitem.
	 *
	 * @param string $key Key of the cache.
	 * @param string $name Name of the subitem.
	 * @param string $cacheType Type of the cache.
	 *
	 * @return array<string, mixed> Array of cache item.
	 */
	public function getManifestCacheSubItem(string $key, string $name, string $cacheType = self::TYPE_BLOCKS): array
	{
		$output = [
			'data' => [],
			'path' => $this->getFullPath($key, $name),
			'key' => $key,
		];

		$data = $this->getManifestCacheTopItem($key, $cacheType)[$name] ?? [];

		if (!$data) {
			return $output;
		}

		$output['data'] = $data;

		return $output;
	}

	/**
	 * Set cache.
	 *
	 * @param string $type Type of the cache.
	 *
	 * @return void
	 */
	public function setAllCache(): void
	{
		$this->setCache(self::TYPE_BLOCKS);
		$this->setCache(self::TYPE_ASSETS);
	}

	/**
	 * Set cache.
	 *
	 * @param string $type Type of the cache.
	 *
	 * @return void
	 */
	protected function setCache(string $type = self::TYPE_BLOCKS): void
	{
		$name = self::TRANSIENT_NAME . $this->getCacheName() . "_{$type}";

		$cache = \get_transient($name);

		if (!$cache) {
			\set_transient($name, \wp_json_encode($this->getAllManifests()), $this->getDuration());
		}
	}

	/**
	 * Get cache.
	 *
	 * @param string $type Type of the cache.
	 *
	 * @return array<string, array> Array of cache.
	 */
	protected function getCache(string $type = self::TYPE_BLOCKS): array
	{
		$cache = \get_transient(self::TRANSIENT_NAME . $this->getCacheName() . "_{$type}");

		if (!$cache) {
			$this->setCache();
		}

		return \json_decode($cache, true) ?? [];
	}

	/**
	 * Unset cache.
	 *
	 * @return array<string, array> Array of cache.
	 *
	 * @return void
	 */
	protected function deleteCache(string $type = self::TYPE_BLOCKS): void
	{
		\delete_transient(self::TRANSIENT_NAME . $this->getCacheName() . "_{$type}");
	}

	/**
	 * Get cache builder.
	 *
	 * @return array<string, array> Array of cache builder.
	 */
	protected function getCacheBuilder(): array
	{
		$sep = \DIRECTORY_SEPARATOR;

		return [
			self::SETTINGS_KEY => [
				'path' => 'blocksDestination',
				'fileName' => 'manifest.json',
				'multiple' => false,
			],
			self::BLOCKS_KEY => [
				'path' => 'blocksDestinationCustom',
				'fileName' => 'manifest.json',
				'id' => 'blockName',
				'multiple' => true,
				'autoset' => [
					'classes' => 'array',
					'attributes' => 'array',
					'hasInnerBlocks' => 'boolean',
				],
			],
			self::COMPONENTS_KEY => [
				'path' => 'blocksDestinationComponents',
				'fileName' => 'manifest.json',
				'id' => 'componentName',
				'multiple' => true,
			],
			self::VARIATIONS_KEY => [
				'path' => 'blocksDestinationVariations',
				'id' => 'name',
				'fileName' => 'manifest.json',
				'multiple' => true,
			],
			self::WRAPPER_KEY => [
				'path' => 'blocksDestinationWrapper',
				'fileName' => 'manifest.json',
				'multiple' => false,
			],
			self::ASSETS_KEY => [
				'path' => 'themeRoot',
				'fileName' => "public{$sep}manifest.json",
				'multiple' => false,
			],
		];
	}

	/**
	 * Get all manifests from the paths.
	 *
	 * @return array<string, array> Array of manifests.
	 */
	private function getAllManifests(): array
	{
		$output = [];

		foreach ($this->getCacheBuilder() as $parent => $data) {
			$multiple = $data['multiple'] ?? false;

			if ($multiple) {
				$output[$parent] = $this->geItems($this->getFullPath($parent, '*'), $data, $parent);
			} else {
				$output[$parent] = $this->getItem($this->getFullPath($parent), $data, $parent);
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
	 * @return array<string, mixed> Item.
	 */
	private function getItem(string $path, array $data, string $parent): array
	{
		if (!\file_exists($path)) {
			return [];
		}

		$file = \file_get_contents($path);

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
				$namespace = $this->blocksNamespace ?? '';

				if ($namespace) {
					$fileDecoded['namespace'] = $namespace;
					$fileDecoded['blockFullName'] = "{$namespace}/{$fileDecoded['blockName']}";
				}
				break;
			case self::SETTINGS_KEY:
				$this->blocksNamespace = $fileDecoded['namespace'] ?? '';
				break;
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
	 * @return array<string, array> Array of items.
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
	 * @param string $name Name of the item.
	 *
	 * @return string Full path.
	 */
	private function getFullPath($type, $name = ''): string
	{
		$data = $this->getCacheBuilder()[$type] ?? [];
		$sep = \DIRECTORY_SEPARATOR;

		if (!$data) {
			return '';
		}

		$path = $data['path'] ?? '';
		$fileName = $data['fileName'] ?? '';

		$path = Components::getProjectPaths($path) ?? $path;

		if (!$name) {
			return \rtrim($path, $sep) . "{$sep}{$fileName}";
		}

		return \rtrim($path, $sep) . "{$sep}{$name}{$sep}{$fileName}";
	}
}
