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
	 * Styles key
	 *
	 * @var array<mixed>
	 */
	public static $styles = [];

	/**
	 * Cache for frequently accessed data to avoid repeated processing.
	 *
	 * @var array<string, mixed>
	 */
	private static array $dataCache = [];

	/**
	 * Cache for filter existence checks to avoid repeated has_filter() calls.
	 *
	 * @var array<string, bool>
	 */
	private static array $filterExistsCache = [];

	/**
	 * Cache for applied filters to avoid repeated filter processing.
	 *
	 * @var array<string, mixed>
	 */
	private static array $appliedFiltersCache = [];

	/**
	 * Get filters.
	 *
	 * @return array<string, string>
	 */
	private const FILTERS_PREFIX = [
		AbstractManifestCache::BLOCKS_KEY => 'es_boilerplate_get_blocks',
		AbstractManifestCache::COMPONENTS_KEY => 'es_boilerplate_get_components',
		AbstractManifestCache::VARIATIONS_KEY => 'es_boilerplate_get_variations',
		AbstractManifestCache::WRAPPER_KEY => 'es_boilerplate_get_wrapper',
		AbstractManifestCache::SETTINGS_KEY => 'es_boilerplate_get_settings',
	];

	// -----------------------------------------------------
	// CORE DATA ACCESS
	// -----------------------------------------------------

	/**
	 * Get cached data with optimized access patterns.
	 *
	 * @param string $type Data type to retrieve.
	 * @param string $key Data key to retrieve.
	 *
	 * @return array<mixed>
	 */
	private static function getCachedData(string $type, string $key): array
	{
		$cacheKey = "{$type}_{$key}";

		// Return cached data if available
		if (isset(self::$dataCache[$cacheKey])) {
			return self::$dataCache[$cacheKey];
		}

		// Get data from main cache
		$data = Helpers::getCache()[$type][$key] ?? [];

		// Cache the result (limit cache size to prevent memory bloat)
		if (\count(self::$dataCache) < 50) {
			self::$dataCache[$cacheKey] = $data;
		}

		return $data;
	}

	/**
	 * Apply filters with optimized caching.
	 *
	 * @param string $filterName Filter name to apply.
	 * @param array<mixed> $data Data to filter.
	 *
	 * @return array<mixed>
	 */
	private static function applyFiltersOptimized(string $filterName, array $data): array
	{
		// Check filter existence cache first
		if (!isset(self::$filterExistsCache[$filterName])) {
			self::$filterExistsCache[$filterName] = \has_filter($filterName);
		}

		// Early return if no filter exists
		if (!self::$filterExistsCache[$filterName]) {
			return $data;
		}

		// Generate cache key for filtered data
		$cacheKey = $filterName . '_' . \hash('xxh3', \serialize($data));

		// Return cached filtered data if available
		if (isset(self::$appliedFiltersCache[$cacheKey])) {
			return self::$appliedFiltersCache[$cacheKey];
		}

		// Apply filter and cache result
		$filteredData = \apply_filters($filterName, $data, Helpers::getCacheName());

		// Cache the result (limit cache size)
		if (\count(self::$appliedFiltersCache) < 20) {
			self::$appliedFiltersCache[$cacheKey] = $filteredData;
		}

		return $filteredData;
	}

	// -----------------------------------------------------
	// BLOCKS
	// -----------------------------------------------------

	/**
	 * Get blocks details with optimized caching.
	 *
	 * @throws InvalidBlock If blocks are missing.
	 *
	 * @return array<mixed>
	 */
	public static function getBlocks(): array
	{
		$data = self::getCachedData(AbstractManifestCache::TYPE_BLOCKS, AbstractManifestCache::BLOCKS_KEY);
		return self::applyFiltersOptimized(self::FILTERS_PREFIX[AbstractManifestCache::BLOCKS_KEY], $data);
	}

	/**
	 * Get block details with optimized error handling.
	 *
	 * @throws InvalidBlock If block is missing.
	 *
	 * @param string $block Block name to get.
	 *
	 * @return array<mixed>
	 */
	public static function getBlock(string $block): array
	{
		// Early return for empty block name
		if ($block === '') {
			throw InvalidBlock::missingItemException($block, 'block');
		}

		$blocks = self::getBlocks();

		if (!isset($blocks[$block]) || empty($blocks[$block])) {
			throw InvalidBlock::missingItemException($block, 'block');
		}

		return $blocks[$block];
	}

	/**
	 * Get components details with optimized caching.
	 *
	 * @throws InvalidBlock If components are missing.
	 *
	 * @return array<mixed>
	 */
	public static function getComponents(): array
	{
		$data = self::getCachedData(AbstractManifestCache::TYPE_BLOCKS, AbstractManifestCache::COMPONENTS_KEY);
		return self::applyFiltersOptimized(self::FILTERS_PREFIX[AbstractManifestCache::COMPONENTS_KEY], $data);
	}

	/**
	 * Get component details with optimized error handling.
	 *
	 * @param string $component Component name to get.
	 *
	 * @throws InvalidBlock If component is missing.
	 *
	 * @return array<mixed>
	 */
	public static function getComponent(string $component): array
	{
		// Early return for empty component name
		if ($component === '') {
			throw InvalidBlock::missingItemException($component, 'component');
		}

		$components = self::getComponents();

		if (!isset($components[$component]) || empty($components[$component])) {
			throw InvalidBlock::missingItemException($component, 'component');
		}

		return $components[$component];
	}

	/**
	 * Get variations details with optimized caching.
	 *
	 * @throws InvalidBlock If variations are missing.
	 *
	 * @return array<mixed>
	 */
	public static function getVariations(): array
	{
		$data = self::getCachedData(AbstractManifestCache::TYPE_BLOCKS, AbstractManifestCache::VARIATIONS_KEY);
		return self::applyFiltersOptimized(self::FILTERS_PREFIX[AbstractManifestCache::VARIATIONS_KEY], $data);
	}

	/**
	 * Get variation details with optimized error handling.
	 *
	 * @param string $variation Variation name to get.
	 *
	 * @throws InvalidBlock If variation is missing.
	 *
	 * @return array<mixed>
	 */
	public static function getVariation(string $variation): array
	{
		// Early return for empty variation name
		if ($variation === '') {
			throw InvalidBlock::missingItemException($variation, 'variation');
		}

		$variations = self::getVariations();

		if (!isset($variations[$variation]) || empty($variations[$variation])) {
			throw InvalidBlock::missingItemException($variation, 'variation');
		}

		return $variations[$variation];
	}

	/**
	 * Get wrapper details with optimized caching.
	 *
	 * @throws InvalidBlock If wrapper is missing.
	 *
	 * @return array<mixed>
	 */
	public static function getWrapper(): array
	{
		$data = self::getCachedData(AbstractManifestCache::TYPE_BLOCKS, AbstractManifestCache::WRAPPER_KEY);
		return self::applyFiltersOptimized(self::FILTERS_PREFIX[AbstractManifestCache::WRAPPER_KEY], $data);
	}

	// -----------------------------------------------------
	// SETTINGS CONFIG
	// -----------------------------------------------------

	/**
	 * Get all global config settings with optimized caching.
	 *
	 * @return array<mixed>
	 */
	public static function getConfig(): array
	{
		// Use static cache for config since it's accessed frequently
		static $configCache = null;

		if ($configCache !== null) {
			return $configCache;
		}

		$settings = self::getSettings();
		$configCache = $settings['config'] ?? [];

		return $configCache;
	}

	/**
	 * Get global config value for output css globally with type safety.
	 *
	 * @return boolean
	 */
	public static function getConfigOutputCssGlobally(): bool
	{
		$config = self::getConfig();
		return isset($config['outputCssGlobally']) ? (bool) $config['outputCssGlobally'] : false;
	}

	/**
	 * Get global config value for output css optimize with type safety.
	 *
	 * @return boolean
	 */
	public static function getConfigOutputCssOptimize(): bool
	{
		$config = self::getConfig();
		return isset($config['outputCssOptimize']) ? (bool) $config['outputCssOptimize'] : false;
	}

	/**
	 * Get global config value for output css selector name with type safety.
	 *
	 * @return string
	 */
	public static function getConfigOutputCssSelectorName(): string
	{
		$config = self::getConfig();
		return $config['outputCssSelectorName'] ?? '';
	}

	/**
	 * Get global config value for output css globally additional styles with type safety.
	 *
	 * @return array<string>
	 */
	public static function getConfigOutputCssGloballyAdditionalStyles(): array
	{
		$config = self::getConfig();
		$styles = $config['outputCssGloballyAdditionalStyles'] ?? [];

		// Ensure array return type
		return \is_array($styles) ? $styles : [];
	}

	/**
	 * Get global config value for use wrapper with type safety.
	 *
	 * @return bool
	 */
	public static function getConfigUseWrapper(): bool
	{
		$config = self::getConfig();
		return isset($config['useWrapper']) ? (bool) $config['useWrapper'] : false;
	}

	/**
	 * Get global config value for use legacy components with type safety.
	 *
	 * @return bool
	 */
	public static function getConfigUseLegacyComponents(): bool
	{
		$config = self::getConfig();
		return isset($config['useLegacyComponents']) ? (bool) $config['useLegacyComponents'] : false;
	}

	// -----------------------------------------------------
	// SETTINGS
	// -----------------------------------------------------

	/**
	 * Get global settings details with optimized caching and error handling.
	 *
	 * @throws InvalidBlock If settings are missing.
	 *
	 * @return array<mixed>
	 */
	public static function getSettings(): array
	{
		$data = self::getCachedData(AbstractManifestCache::TYPE_BLOCKS, AbstractManifestCache::SETTINGS_KEY);

		if (empty($data)) {
			throw InvalidBlock::missingItemException('project', 'global settings');
		}

		return self::applyFiltersOptimized(self::FILTERS_PREFIX[AbstractManifestCache::SETTINGS_KEY], $data);
	}

	/**
	 * Get global settings details - namespace with optimized access.
	 *
	 * @return string
	 */
	public static function getSettingsNamespace(): string
	{
		static $namespaceCache = null;

		if ($namespaceCache !== null) {
			return $namespaceCache;
		}

		$settings = self::getSettings();
		$namespaceCache = $settings['namespace'] ?? '';

		return $namespaceCache;
	}

	/**
	 * Get global settings details - global variables with optimized access.
	 *
	 * @return array<mixed>
	 */
	public static function getSettingsGlobalVariables(): array
	{
		static $globalVarsCache = null;

		if ($globalVarsCache !== null) {
			return $globalVarsCache;
		}

		$settings = self::getSettings();
		$globalVarsCache = $settings['globalVariables'] ?? [];

		return $globalVarsCache;
	}

	/**
	 * Get global settings details - global variables breakpoints with optimized access.
	 *
	 * @return array<mixed>
	 */
	public static function getSettingsGlobalVariablesBreakpoints(): array
	{
		static $breakpointsCache = null;

		if ($breakpointsCache !== null) {
			return $breakpointsCache;
		}

		$globalVars = self::getSettingsGlobalVariables();
		$breakpointsCache = $globalVars['breakpoints'] ?? [];

		return $breakpointsCache;
	}

	/**
	 * Get global settings details - global variables colors with optimized access.
	 *
	 * @return array<mixed>
	 */
	public static function getSettingsGlobalVariablesColors(): array
	{
		static $colorsCache = null;

		if ($colorsCache !== null) {
			return $colorsCache;
		}

		$globalVars = self::getSettingsGlobalVariables();
		$colorsCache = $globalVars['colors'] ?? [];

		return $colorsCache;
	}

	// -----------------------------------------------------
	// STYLES
	// -----------------------------------------------------

	/**
	 * Set styles details with validation.
	 *
	 * @param array<mixed> $style Style to store.
	 *
	 * @return void
	 */
	public static function setStyle(array $style): void
	{
		// Early return for empty style
		if (empty($style)) {
			return;
		}

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
	 * Get asset details with optimized error handling.
	 *
	 * @param string $asset Asset name to get.
	 *
	 * @throws InvalidBlock If asset is missing.
	 *
	 * @return string
	 */
	public static function getAsset(string $asset): string
	{
		// Early return for empty asset name
		if ($asset === '') {
			throw InvalidBlock::missingItemException($asset, 'public asset');
		}

		$assets = self::getCachedData(AbstractManifestCache::TYPE_ASSETS, AbstractManifestCache::ASSETS_KEY);

		if (!isset($assets[$asset]) || $assets[$asset] === '') {
			throw InvalidBlock::missingItemException($asset, 'public asset');
		}

		return (string) $assets[$asset];
	}

	// -----------------------------------------------------
	// GEOLOCATION
	// -----------------------------------------------------

	/**
	 * Get geolocation countries details with optimized error handling.
	 *
	 * @throws InvalidManifest If geolocation countries are missing.
	 *
	 * @return array<mixed>
	 */
	public static function getGeolocationCountries(): array
	{
		$data = self::getCachedData(AbstractManifestCache::TYPE_GEOLOCATION, AbstractManifestCache::COUNTRIES_KEY);

		if (empty($data)) {
			throw InvalidManifest::missingManifestException(AbstractManifestCache::TYPE_GEOLOCATION);
		}

		return $data;
	}
}
