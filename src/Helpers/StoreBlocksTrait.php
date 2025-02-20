<?php

/**
 * Helpers for blocks store.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

use EightshiftLibs\Cache\AbstractManifestCache;
use EightshiftLibs\Exception\InvalidBlock;
use EightshiftLibs\Exception\InvalidManifest;

/**
 * Class StoreTrait Helper
 */
trait StoreBlocksTrait
{
	/**
	 * Cache.
	 *
	 * @var array<mixed>
	 */
	private static $cache = [];

	/**
	 * Cache builder.
	 *
	 * @var array<mixed>
	 */
	private static $cacheBuilder = [];

	/**
	 * Cache name.
	 *
	 * @var string
	 */
	private static $cacheName = '';

	/**
	 * Cache transient prefix.
	 *
	 * @var string
	 */
	private static $cacheTransientPrefix = '';

	/**
	 * Cache version.
	 *
	 * @var string
	 */
	private static $version = '';

	/**
	 * Styles key
	 *
	 * @var array<mixed>
	 */
	public static $styles = [];

	// -----------------------------------------------------
	// CACHE
	// -----------------------------------------------------

	/**
	 * Set cache details.
	 *
	 * @param string $cacheTransientPrefix Cache transient prefix.
	 * @param array<mixed> $cacheBuilder Cache builder.
	 * @param string $cacheName Cache name.
	 * @param string $version Cache version.
	 *
	 * @return void
	 */
	public static function setCacheDetails(
		string $cacheTransientPrefix,
		array $cacheBuilder,
		string $cacheName,
		string $version
	): void {
		self::$cacheBuilder = $cacheBuilder;
		self::$cacheName = $cacheName;
		self::$cacheTransientPrefix = $cacheTransientPrefix;
		self::$version = $version;
	}

	/**
	 * Set internal cache.
	 *
	 * @return void
	 */
	public static function setCache(): void
	{
		if (self::$cache) {
			return;
		}

		$output = [];

		foreach (self::$cacheBuilder as $type => $value) {
			$data = \get_transient(self::getCacheName($type));

			if (!$data) {
				continue;
			}

			if (!Helpers::isJson($data)) {
				continue;
			}

			$data = \json_decode($data, true);

			if (!$data) {
				continue;
			}

			$output[$type] = $data;
		}

		self::$cache = $output;
	}

	/**
	 * Get cache.
	 *
	 * @return array<mixed>
	 */
	public static function getCache(): array
	{
		return self::$cache;
	}

	/**
	 * Get cache name.
	 *
	 * @param string $type Type of the cache.
	 *
	 * @return string
	 */
	public static function getCacheName(string $type): string
	{
		return self::$cacheTransientPrefix . "_{$type}";
	}

	/**
	 * Check if cache version is valid.
	 *
	 * @return bool
	 */
	public static function isCacheVersionValid(): bool
	{
		return self::getCacheVersion() === self::$version;
	}

	/**
	 * Set version cache.
	 *
	 * @return void
	 */
	public static function setCacheVersion(): void
	{
		$name = self::getCacheName(AbstractManifestCache::VERSION_KEY);

		$cache = \get_transient($name);

		if (!$cache) {
			\set_transient($name, self::$version);
		}
	}

	/**
	 * Get cache version.
	 *
	 * @return string
	 */
	public static function getCacheVersion(): string
	{
		$cache = \get_transient(self::getCacheName(AbstractManifestCache::VERSION_KEY)) ?: ''; // phpcs:ignore WordPress.PHP.DisallowShortTernary.Found

		if (!$cache) {
			self::setCacheVersion();
		}

		return $cache;
	}

	/**
	 * Unset cache version.
	 *
	 * @return void
	 */
	public static function deleteCacheVersion(): void
	{
		\delete_transient(self::getCacheName(AbstractManifestCache::VERSION_KEY));
	}

	/**
	 * Unset all manifest cache.
	 *
	 * @return void
	 */
	public static function deleteAllCache(): void
	{
		$data = \array_keys(self::$cacheBuilder);

		foreach ($data as $cache) {
			self::deleteCache($cache);
		}

		self::deleteCacheVersion();
	}

	/**
	 * Unset cache item by type.
	 *
	 * @param string $cacheType Type of the cache.
	 *
	 * @return void
	 */
	public static function deleteCache(string $cacheType): void
	{
		\delete_transient(self::getCacheName($cacheType));
	}

	/**
	 * Check if we should cache the service classes.
	 *
	 * @return bool
	 */
	public static function shouldCache(): bool
	{
		return !(
			(\defined('WP_ENVIRONMENT_TYPE') &&
			(\WP_ENVIRONMENT_TYPE === 'development' || \WP_ENVIRONMENT_TYPE === 'local')) ||
			\defined('WP_CLI')
		);
	}

	// -----------------------------------------------------
	// BLOCKS
	// -----------------------------------------------------

	/**
	 * Get blocks details.
	 *
	 * @throws InvalidBlock If blocks are missing.
	 *
	 * @return array<mixed>
	 */
	public static function getBlocks(): array
	{
		$output = self::getCache()[AbstractManifestCache::TYPE_BLOCKS][AbstractManifestCache::BLOCKS_KEY] ?? [];

		if (!$output) {
			throw InvalidBlock::missingItemException('project', 'blocks');
		}

		$filterName = 'es_boilerplate_get_blocks';

		if (\has_filter($filterName)) {
			$output = \apply_filters($filterName, $output, self::$cacheName);
		}

		return $output;
	}

	/**
	 * Get block details.
	 *
	 * @throws InvalidBlock If block is missing.
	 *
	 * @param string $block Block name to get.
	 *
	 * @return array<mixed>
	 */
	public static function getBlock(string $block): array
	{
		$output = self::getBlocks()[$block] ?? [];

		if (!$output) {
			throw InvalidBlock::missingItemException($block, 'block');
		}

		return $output;
	}

	/**
	 * Get components details.
	 *
	 * @throws InvalidBlock If components are missing.
	 *
	 * @return array<mixed>
	 */
	public static function getComponents(): array
	{
		$output = self::getCache()[AbstractManifestCache::TYPE_BLOCKS][AbstractManifestCache::COMPONENTS_KEY] ?? [];

		if (!$output) {
			throw InvalidBlock::missingItemException('project', 'components');
		}

		return $output;
	}

	/**
	 * Get component details.
	 *
	 * @param string $component Componennt name to get.
	 *
	 * @throws InvalidBlock If component is missing.
	 *
	 * @return array<mixed>
	 */
	public static function getComponent(string $component): array
	{
		$output = self::getComponents()[$component] ?? [];

		if (!$output) {
			throw InvalidBlock::missingItemException($component, 'component');
		}

		return $output;
	}

	/**
	 * Get variations details.
	 *
	 * @throws InvalidBlock If variations are missing.
	 *
	 * @return array<mixed>
	 */
	public static function getVariations(): array
	{
		$output = self::getCache()[AbstractManifestCache::TYPE_BLOCKS][AbstractManifestCache::VARIATIONS_KEY] ?? [];

		if (!$output) {
			throw InvalidBlock::missingItemException('project', 'variations');
		}

		return $output;
	}

	/**
	 * Get variation details.
	 *
	 * @param string $variation Variation name to get.
	 *
	 * @throws InvalidBlock If variation is missing.
	 *
	 * @return array<mixed>
	 */
	public static function getVariation(string $variation): array
	{
		$output = self::getVariations()[$variation] ?? [];

		if (!$output) {
			throw InvalidBlock::missingItemException($variation, 'variation');
		}

		return $output;
	}

	/**
	 * Get wrapper details.
	 *
	 * @throws InvalidBlock If wrapper is missing.
	 *
	 * @return array<mixed>
	 */
	public static function getWrapper(): array
	{
		$output = self::getCache()[AbstractManifestCache::TYPE_BLOCKS][AbstractManifestCache::WRAPPER_KEY] ?? [];

		if (!$output) {
			throw InvalidBlock::missingItemException('blocks wrapper', 'component');
		}

		return $output;
	}

	// -----------------------------------------------------
	// SETTINGS CONFIG
	// -----------------------------------------------------

	/**
	 * Get all global config settings.
	 *
	 * @return array<mixed>
	 */
	public static function getConfig(): array
	{
		return self::getCache()[AbstractManifestCache::TYPE_BLOCKS][AbstractManifestCache::SETTINGS_KEY]['config'] ?? [];
	}

	/**
	 * Get global config value for output css globally.
	 *
	 * @return boolean
	 */
	public static function getConfigOutputCssGlobally(): bool
	{
		return (bool) self::getConfig()['outputCssGlobally'];
	}

	/**
	 * Get global config value for output css optimize.
	 *
	 * @return boolean
	 */
	public static function getConfigOutputCssOptimize(): bool
	{
		return (bool) self::getConfig()['outputCssOptimize'];
	}

	/**
	 * Get global config value for output css selector name.
	 *
	 * @return string
	 */
	public static function getConfigOutputCssSelectorName(): string
	{
		return self::getConfig()['outputCssSelectorName'];
	}

	/**
	 * Get global config value for output css globally additional styles.
	 *
	 * @return array<string>
	 */
	public static function getConfigOutputCssGloballyAdditionalStyles(): array
	{
		return self::getConfig()['outputCssGloballyAdditionalStyles'];
	}

	/**
	 * Get global config value for use wrapper.
	 *
	 * @return bool
	 */
	public static function getConfigUseWrapper(): bool
	{
		return (bool) self::getConfig()['useWrapper'];
	}

	/**
	 * Get global config value for use components.
	 *
	 * @return bool
	 */
	public static function getConfigUseComponents(): bool
	{
		return (bool) self::getConfig()['useComponents'];
	}

	/**
	 * Get global config value for use blocks.
	 *
	 * @return bool
	 */
	public static function getConfigUseBlocks(): bool
	{
		return (bool) self::getConfig()['useBlocks'];
	}

	/**
	 * Get global config value for use variations.
	 *
	 * @return bool
	 */
	public static function getConfigUseVariations(): bool
	{
		return (bool) self::getConfig()['useVariations'];
	}

	// -----------------------------------------------------
	// SETTINGS
	// -----------------------------------------------------

	/**
	 * Get global settings details.
	 *
	 * @throws InvalidBlock If settings are missing.
	 *
	 * @return array<mixed>
	 */
	public static function getSettings(): array
	{
		$output = self::getCache()[AbstractManifestCache::TYPE_BLOCKS][AbstractManifestCache::SETTINGS_KEY] ?? [];

		if (!$output) {
			throw InvalidBlock::missingItemException('project', 'global settings');
		}

		return $output;
	}

	/**
	 * Get global settings details - namespace.
	 *
	 * @return string
	 */
	public static function getSettingsNamespace(): string
	{
		return self::getSettings()['namespace'] ?? '';
	}

	/**
	 * Get global settings details - global variables.
	 *
	 * @return array<mixed>
	 */
	public static function getSettingsGlobalVariables(): array
	{
		return self::getSettings()['globalVariables'] ?? [];
	}

	/**
	 * Get global settings details - global variables breakpoints.
	 *
	 * @return array<mixed>
	 */
	public static function getSettingsGlobalVariablesBreakpoints(): array
	{
		return self::getSettingsGlobalVariables()['breakpoints'] ?? [];
	}

	/**
	 * Get global settings details - global variables colors.
	 *
	 * @return array<mixed>
	 */
	public static function getSettingsGlobalVariablesColors(): array
	{
		return self::getSettingsGlobalVariables()['colors'] ?? [];
	}

	// -----------------------------------------------------
	// STYLES
	// -----------------------------------------------------

	/**
	 * Set styles details.
	 *
	 * @param array<mixed> $style Style to store.
	 *
	 * @return void
	 */
	public static function setStyle(array $style): void
	{
		self::$styles[] = $style;
	}

	/**
	 * Get styles details.
	 *
	 * @return array<mixed>
	 */
	public static function getStyles(): array
	{
		return self::$styles;
	}

	// -----------------------------------------------------
	// ASSETS
	// -----------------------------------------------------

	/**
	 * Get asset details.
	 *
	 * @param string $asset Asset name to get.
	 *
	 * @throws InvalidBlock If asset is missing.
	 *
	 * @return string
	 */
	public static function getAsset(string $asset): string
	{
		$output = self::getCache()[AbstractManifestCache::TYPE_ASSETS][AbstractManifestCache::ASSETS_KEY][$asset] ?? '';

		if (!$output) {
			throw InvalidBlock::missingItemException($asset, 'public asset');
		}

		return $output;
	}

	// -----------------------------------------------------
	// GEOLOCATION
	// -----------------------------------------------------

	/**
	 * Get geolocation countries details.
	 *
	 * @throws InvalidManifest If geolocation countries are missing.
	 *
	 * @return array<mixed>
	 */
	public static function getGeolocationCountries(): array
	{
		$output = self::getCache()[AbstractManifestCache::TYPE_GEOLOCATION][AbstractManifestCache::COUNTRIES_KEY] ?? [];

		if (!$output) {
			throw InvalidManifest::missingManifestException(AbstractManifestCache::TYPE_GEOLOCATION);
		}

		return $output;
	}
}
