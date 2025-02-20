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
	public const TRANSIENT_NAME = 'eightshift_manifest_cache_';

	// Cache keys.
	public const VERSION_KEY = 'version';
	public const BLOCKS_KEY = 'blocks';
	public const COMPONENTS_KEY = 'components';
	public const VARIATIONS_KEY = 'variations';
	public const WRAPPER_KEY = 'wrapper';
	public const SETTINGS_KEY = 'settings';
	public const ASSETS_KEY = 'assets';
	public const COUNTRIES_KEY = 'countries';


	// Cache types.
	public const TYPE_BLOCKS = 'blocks';
	public const TYPE_ASSETS = 'assets';
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
	 * Get cache version.
	 *
	 * @return string Cache version.
	 */
	abstract public function getVersion(): string;

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
	 * Set all cache.
	 *
	 * @return void
	 */
	public function setAllCache(): void
	{
		Helpers::setCacheDetails(
			$this->getCacheBuilder(),
			$this->getCacheName(),
			$this->getVersion()
		);

		Helpers::shouldCache();

		if (!Helpers::isCacheVersionValid() || !Helpers::shouldCache()) {
			Helpers::deleteCacheVersion();
		}

		$this->setCache(self::TYPE_BLOCKS);
		$this->setCache(self::TYPE_ASSETS);
		$this->setCache(self::TYPE_GEOLOCATION);

		Helpers::setCacheVersion();
		Helpers::setCache();
	}

	/**
	 * Set cache.
	 *
	 * @param string $cacheType Type of the cache.
	 *
	 * @return void
	 */
	protected function setCache(string $cacheType): void
	{
		if (!\get_transient(Helpers::getCacheTransientName($cacheType))) {
			\set_transient(
				Helpers::getCacheTransientName($cacheType),
				\wp_json_encode($this->getAllManifests($cacheType)),
				$this->getDuration()
			);
		}
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
					'autoset' => [
						[
							'key' => 'outputCssGlobally',
							'value' => true,
							'parent' => 'config',
						],
						[
							'key' => 'outputCssOptimize',
							'value' => true,
							'parent' => 'config',
						],
						[
							'key' => 'useWrapper',
							'value' => true,
							'parent' => 'config',
						],
						[
							'key' => 'useComponents',
							'value' => true,
							'parent' => 'config',
						],
						[
							'key' => 'useBlocks',
							'value' => true,
							'parent' => 'config',
						],
						[
							'key' => 'useVariations',
							'value' => true,
							'parent' => 'config',
						],
						[
							'key' => 'outputCssSelectorName',
							'value' => 'esCssVariables',
							'parent' => 'config',
						],
						[
							'key' => 'outputCssGloballyAdditionalStyles',
							'value' => [],
							'parent' => 'config',
						],
					],
				],
				self::BLOCKS_KEY => [
					'path' => 'blocksDestinationCustom',
					'id' => 'blockName',
					'multiple' => true,
					'autoset' => [
						[
							'key' => 'classes',
							'value' => [],
						],
						[
							'key' => 'attributes',
							'value' => [],
						],
						[
							'key' => 'hasInnerBlocks',
							'value' => false,
						],
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
	private function getAllManifests(string $cacheType): array
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
			foreach ($autoset as $autosetItem) {
				$autosetItemKey = $autosetItem['key'] ?? '';
				$autosetItemValue = $autosetItem['value'] ?? '';
				$autosetItemParent = $autosetItem['parent'] ?? '';

				if (!$autosetItemKey || !$autosetItemValue) {
					continue;
				}

				// Handle the case where there is no parent.
				if (!$autosetItemParent) {
					if (!isset($fileDecoded[$autosetItemKey])) {
						$fileDecoded[$autosetItemKey] = $autosetItemValue;
					}
					continue;
				}

				// Handle the case where there is a parent.
				if (!isset($fileDecoded[$autosetItemParent][$autosetItemKey])) {
					$fileDecoded[$autosetItemParent][$autosetItemKey] = $autosetItemValue;
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
	private function getFullPath($type, string $cacheType, $name = ''): string
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
