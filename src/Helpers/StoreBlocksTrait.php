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
	 * Get filters.
	 *
	 * @return array<mixed>
	 */
	private const FILTERS_PREFIX = [
		AbstractManifestCache::BLOCKS_KEY => 'es_boilerplate_get_blocks',
		AbstractManifestCache::COMPONENTS_KEY => 'es_boilerplate_get_components',
		AbstractManifestCache::VARIATIONS_KEY => 'es_boilerplate_get_variations',
		AbstractManifestCache::WRAPPER_KEY => 'es_boilerplate_get_wrapper',
		AbstractManifestCache::SETTINGS_KEY => 'es_boilerplate_get_settings',
	];

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

		$filterName = self::FILTERS_PREFIX[AbstractManifestCache::BLOCKS_KEY];

		if (\has_filter($filterName)) {
			$output = \apply_filters($filterName, $output, Helpers::getCacheName());
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

		$filterName = self::FILTERS_PREFIX[AbstractManifestCache::COMPONENTS_KEY];

		if (\has_filter($filterName)) {
			$output = \apply_filters($filterName, $output, Helpers::getCacheName());
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

		$filterName = self::FILTERS_PREFIX[AbstractManifestCache::VARIATIONS_KEY];

		if (\has_filter($filterName)) {
			$output = \apply_filters($filterName, $output, Helpers::getCacheName());
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

		$filterName = self::FILTERS_PREFIX[AbstractManifestCache::WRAPPER_KEY];

		if (\has_filter($filterName)) {
			$output = \apply_filters($filterName, $output, Helpers::getCacheName());
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
	 * Get global config value for use legacy components.
	 *
	 * @return bool
	 */
	public static function getConfigUseLegacyComponents(): bool
	{
		return (bool) self::getConfig()['useLegacyComponents'];
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

		$filterName = self::FILTERS_PREFIX[AbstractManifestCache::SETTINGS_KEY];

		if (\has_filter($filterName)) {
			$output = \apply_filters($filterName, $output, Helpers::getCacheName());
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
