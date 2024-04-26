<?php

/**
 * The file that defines a manifest cache.
 *
 * This file is used to define the methods that are used in the manifest cache class.
 *
 * @package EightshiftLibs\Cache
 */

declare(strict_types=1);

namespace EightshiftLibs\Cache;

use EightshiftLibs\Helpers\Components;

abstract class AbstractManifestCache implements ManifestCacheInterface
{
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
	 * Set cache.
	 *
	 * @return void
	 */
	public function setCache(): void
	{
		$cache = \get_transient('eightshift_manifest_cache_' . $this->getCacheName());

		if (!$cache) {
			\set_transient('eightshift_manifest_cache_' . $this->getCacheName(), \wp_json_encode($this->getAllManifests()), $this->getDuration());
		}
	}

	/**
	 * Get cache.
	 *
	 * @return array<string, array> Array of cache.
	 */
	public function getCache(): array
	{
		$cache = \get_transient('eightshift_manifest_cache_' . $this->getCacheName());

		if (!$cache) {
			$this->setCache();
		}

		return \json_decode($cache, true) ?? [];
	}

	/**
	 * Unset cache.
	 *
	 * @return void
	 */
	public function deleteCache(): void
	{
		\delete_transient('eightshift_manifest_cache_' . $this->getCacheName());
	}

	/**
	 * Get manifest cache top item.
	 *
	 * @param string $key Key of the cache.
	 *
	 * @return array<string, mixed> Array of cache item.
	 */
	public function getManifestCacheTopItem(string $key): array
	{
		$output = [
			'data' => [],
			'path' => $this->getCacheBuilder()[$key]['path'] ?? '',
			'key' => $key,
		];

		$data = $this->getCache()[$key] ?? [];

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
	 * @param string $path Path of the cache.
	 *
	 * @return array<string, mixed> Array of cache item.
	 */
	public function getManifestCacheSubItem(string $key, string $path): array
	{
		$output = [
			'data' => [],
			'path' => $path,
			'key' => $key,
		];

		$data = $this->getManifestCacheTopItem($key)[$path] ?? [];

		if (!$data) {
			return $output;
		}

		$output['data'] = $data;

		return $output;
	}

	/**
	 * Get cache builder.
	 *
	 * @return array<string, array> Array of cache builder.
	 */
	protected function getCacheBuilder(): array
	{
		return [
			'blocks' => [
				'path' => 'blocksDestinationCustom',
				'fileName' => 'manifest.json',
				'multiple' => true,
				'autoset' => [
					'classes' => 'array',
					'attributes' => 'array',
					'hasInnerBlocks' => 'boolean',
				],
			],
			'components' => [
				'path' => 'blocksDestinationComponents',
				'fileName' => 'manifest.json',
				'multiple' => true,
			],
			'variations' => [
				'path' => 'blocksDestinationVariations',
				'fileName' => 'manifest.json',
				'multiple' => true,
			],
			'wrapper' => [
				'path' => 'blocksDestinationWrapper',
				'fileName' => 'manifest.json',
				'multiple' => false,
			],
			'settings' => [
				'path' => 'blocksDestination',
				'fileName' => 'manifest.json',
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
		$sep = \DIRECTORY_SEPARATOR;

		foreach ($this->getCacheBuilder() as $parent => $data) {
			$path = $data['path'] ?? '';
			$multiple = $data['multiple'] ?? false;
			$fileName = $data['fileName'] ?? '';

			if (!$path) {
				continue;
			}

			$path = Components::getProjectPaths($path) ?? $path;

			if ($multiple) {
				$output[$parent] = $this->getMultipleItems($path, $data);
			} else {
				$output[$parent] = $this->getSingleItem(\rtrim($path, $sep) . "{$sep}{$fileName}", $data);
			}
		}

		return $output;
	}

	/**
	 * Get single item from the path.
	 *
	 * @param string $path Path to the item.
	 * @param array<mixed> $data Data array.
	 *
	 * @return array<string, mixed> Item.
	 */
	private function getSingleItem(string $path, array $data): array
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

		return $fileDecoded;
	}

	/**
	 * Get multiple items from the path.
	 *
	 * @param string $path Path to the items.
	 * @param array<mixed> $data Data array.
	 *
	 * @return array<string, array> Array of items.
	 */
	private function getMultipleItems(string $path, array $data): array
	{
		$output = [];

		$sep = \DIRECTORY_SEPARATOR;
		$fileName = $data['fileName'] ?? '';

		$path = \rtrim($path, $sep) . "{$sep}*{$sep}{$fileName}";

		foreach ((array)\glob($path) as $itemPath) {
			$output[$itemPath] = $this->getSingleItem($itemPath, $data);
		}

		return $output;
	}
}
