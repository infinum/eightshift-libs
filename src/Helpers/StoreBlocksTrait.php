<?php

/**
 * Helpers for blocks store.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

use EightshiftLibs\Cache\AbstractManifestCache;

/**
 * Class StoreTrait Helper
 */
trait StoreBlocksTrait
{
	/**
	 * Store default state
	 *
	 * @var array<mixed>
	 */
	public static $defaultState = [
		AbstractManifestCache::BLOCKS_KEY => [],
		AbstractManifestCache::COMPONENTS_KEY => [],
		AbstractManifestCache::CONFIG_KEY => [
			'outputCssGlobally' => false,
			'outputCssOptimize' => false,
			'outputCssSelectorName' => 'esCssVariables',
			'outputCssGloballyAdditionalStyles' => [],
			'useWrapper' => true,
			'useComponents' => true,
			'useBlock' => true,
			'useVariations' => true,
		],
		AbstractManifestCache::WRAPPER_KEY => [],
		AbstractManifestCache::VARIATIONS_KEY => [],
		AbstractManifestCache::SETTINGS_KEY => [],
		AbstractManifestCache::STYLES_KEY => [],
		AbstractManifestCache::ASSETS_KEY => [],
	];

	/**
	 * Get full store name.
	 *
	 * @return string
	 */
	public static function getStoreName(): string
	{
		return \basename(Helpers::getProjectPaths('root'));
	}

	/**
	 * Set internal store.
	 *
	 * @return void
	 */
	public static function setStore(): void
	{
		global $esBlocks;

		$store = self::getStore();

		if (!$store) {
			$esBlocks[self::getStoreName()] = self::$defaultState;
		}
	}

	/**
	 * Get store details.
	 *
	 * @return array<mixed>
	 */
	public static function getStore(): array
	{
		global $esBlocks;

		return $esBlocks[self::getStoreName()] ?? [];
	}

	/**
	 * Set blocks details.
	 *
	 * @param array<mixed> $blocks Blocks list to store.
	 *
	 * @return void
	 */
	public static function setBlocks(array $blocks): void
	{
		global $esBlocks;

		if (self::getStore()) {
			$esBlocks[self::getStoreName()][AbstractManifestCache::BLOCKS_KEY] = $blocks;
		}
	}

	/**
	 * Get blocks details.
	 *
	 * @return array<mixed>
	 */
	public static function getBlocks(): array
	{
		return self::getStore()[AbstractManifestCache::BLOCKS_KEY] ?? [];
	}

	/**
	 * Get block details.
	 *
	 * @param string $block Block name to get.
	 *
	 * @return array<mixed>
	 */
	public static function getBlock(string $block): array
	{
		return self::getBlocks()[$block] ?? [];
	}

	/**
	 * Set components details.
	 *
	 * @param array<mixed> $components Components list to store.
	 *
	 * @return void
	 */
	public static function setComponents(array $components): void
	{
		global $esBlocks;

		if (self::getStore()) {
			$esBlocks[self::getStoreName()][AbstractManifestCache::COMPONENTS_KEY] = $components;
		}
	}

	/**
	 * Get components details.
	 *
	 * @return array<mixed>
	 */
	public static function getComponents(): array
	{
		return self::getStore()[AbstractManifestCache::COMPONENTS_KEY] ?? [];
	}

	/**
	 * Get component details.
	 *
	 * @param string $component Componennt name to get.
	 *
	 * @return array<mixed>
	 */
	public static function getComponent(string $component): array
	{
		return self::getComponents()[$component] ?? [];
	}

	/**
	 * Set variations details.
	 *
	 * @param array<mixed> $variations Variations list to store.
	 *
	 * @return void
	 */
	public static function setVariations(array $variations): void
	{
		global $esBlocks;

		if (self::getStore()) {
			$esBlocks[self::getStoreName()][AbstractManifestCache::VARIATIONS_KEY] = $variations;
		}
	}

	/**
	 * Get variations details.
	 *
	 * @return array<mixed>
	 */
	public static function getVariations(): array
	{
		return self::getStore()[AbstractManifestCache::VARIATIONS_KEY] ?? [];
	}

	/**
	 * Get variation details.
	 *
	 * @param string $variation Variation name to get.
	 *
	 * @return array<mixed>
	 */
	public static function getVariation(string $variation): array
	{
		return self::getVariations()[$variation] ?? [];
	}

	/**
	 * Set all config flags overriding from global settings manifest.json.
	 *
	 * @return void
	 */
	public static function setConfigFlags(): void
	{
		$config = self::getSettings()[AbstractManifestCache::CONFIG_KEY] ?? [];

		if ($config) {
			// outputCssGlobally.
			if (isset($config['outputCssGlobally']) && \gettype($config['outputCssGlobally']) === 'boolean') {
				Helpers::setConfigOutputCssGlobally($config['outputCssGlobally']);
			}

			// outputCssOptimize.
			if (isset($config['outputCssOptimize']) && \gettype($config['outputCssOptimize']) === 'boolean') {
				Helpers::setConfigOutputCssOptimize($config['outputCssOptimize']);
			}

			// outputCssSelectorName.
			if (isset($config['outputCssSelectorName']) && \gettype($config['outputCssSelectorName']) === 'string') {
				Helpers::setConfigOutputCssSelectorName($config['outputCssSelectorName']);
			}

			// outputCssGloballyAdditionalStyles.
			if (isset($config['outputCssGloballyAdditionalStyles']) && \gettype($config['outputCssGloballyAdditionalStyles']) === 'array') {
				Helpers::setConfigOutputCssGloballyAdditionalStyles($config['outputCssGloballyAdditionalStyles']);
			}

			// useWrapper.
			if (isset($config['useWrapper']) && \gettype($config['useWrapper']) === 'boolean') {
				Helpers::setConfigUseWrapper($config['useWrapper']);
			}

			// useComponents.
			if (isset($config['useComponents']) && \gettype($config['useComponents']) === 'boolean') {
				Helpers::setConfigUseComponents($config['useComponents']);
			}

			// useBlocks.
			if (isset($config['useBlocks']) && \gettype($config['useBlocks']) === 'boolean') {
				Helpers::setConfigUseBlocks($config['useBlocks']);
			}

			// useVariations.
			if (isset($config['useVariations']) && \gettype($config['useVariations']) === 'boolean') {
				Helpers::setConfigUseVariations($config['useVariations']);
			}
		}
	}

	/**
	 * Get all global config settings.
	 *
	 * @return array<mixed>
	 */
	public static function getConfig(): array
	{
		return self::getStore()[AbstractManifestCache::CONFIG_KEY] ?? [];
	}

	/**
	 * Set global config setting for output css globally.
	 *
	 * @param boolean $config Config value.
	 *
	 * @return void
	 */
	public static function setConfigOutputCssGlobally(bool $config): void
	{
		global $esBlocks;

		if (self::getStore() && self::getConfig()) {
			$esBlocks[self::getStoreName()][AbstractManifestCache::CONFIG_KEY]['outputCssGlobally'] = $config;
		}
	}

	/**
	 * Get global config value for output css globally.
	 *
	 * @return boolean
	 */
	public static function getConfigOutputCssGlobally(): bool
	{
		return self::getConfig()['outputCssGlobally'] ?? self::$defaultState[AbstractManifestCache::CONFIG_KEY]['outputCssGlobally'];
	}

	/**
	 * Set global config setting for output css optimize.
	 *
	 * @param boolean $config Config value.
	 *
	 * @return void
	 */
	public static function setConfigOutputCssOptimize(bool $config): void
	{
		global $esBlocks;

		if (self::getStore() && self::getConfig()) {
			$esBlocks[self::getStoreName()][AbstractManifestCache::CONFIG_KEY]['outputCssOptimize'] = $config;
		}
	}

	/**
	 * Get global config value for output css optimize.
	 *
	 * @return boolean
	 */
	public static function getConfigOutputCssOptimize(): bool
	{
		return self::getConfig()['outputCssOptimize'] ?? self::$defaultState[AbstractManifestCache::CONFIG_KEY]['outputCssOptimize'];
	}

	/**
	 * Set global config setting for output css selector name.
	 *
	 * @param string $config Config value.
	 *
	 * @return void
	 */
	public static function setConfigOutputCssSelectorName(string $config): void
	{
		global $esBlocks;

		if (self::getStore() && self::getConfig()) {
			$esBlocks[self::getStoreName()][AbstractManifestCache::CONFIG_KEY]['outputCssSelectorName'] = $config;
		}
	}

	/**
	 * Get global config value for output css selector name.
	 *
	 * @return string
	 */
	public static function getConfigOutputCssSelectorName(): string
	{
		return self::getConfig()['outputCssSelectorName'] ?? self::$defaultState[AbstractManifestCache::CONFIG_KEY]['outputCssSelectorName'];
	}

	/**
	 * Set global config value for output css globally additional styles.
	 *
	 * @param array<string> $config Config value.
	 *
	 * @return void
	 */
	public static function setConfigOutputCssGloballyAdditionalStyles(array $config): void
	{
		global $esBlocks;

		if (self::getStore() && self::getConfig()) {
			$esBlocks[self::getStoreName()][AbstractManifestCache::CONFIG_KEY]['outputCssGloballyAdditionalStyles'] = $config;
		}
	}

	/**
	 * Get global config value for output css globally additional styles.
	 *
	 * @return array<string>
	 */
	public static function getConfigOutputCssGloballyAdditionalStyles(): array
	{
		return self::getConfig()['outputCssGloballyAdditionalStyles'] ?? self::$defaultState[AbstractManifestCache::CONFIG_KEY]['outputCssGloballyAdditionalStyles'];
	}

	/**
	 * Set global config value for use wrapper.
	 *
	 * @param bool $config Config value.
	 *
	 * @return void
	 */
	public static function setConfigUseWrapper(bool $config): void
	{
		global $esBlocks;

		if (self::getStore() && self::getConfig()) {
			$esBlocks[self::getStoreName()][AbstractManifestCache::CONFIG_KEY]['useWrapper'] = $config;
		}
	}

	/**
	 * Get global config value for use wrapper.
	 *
	 * @return bool
	 */
	public static function getConfigUseWrapper(): bool
	{
		return self::getConfig()['useWrapper'] ?? self::$defaultState[AbstractManifestCache::CONFIG_KEY]['useWrapper'];
	}

	/**
	 * Set global config value for use components.
	 *
	 * @param bool $config Config value.
	 *
	 * @return void
	 */
	public static function setConfigUseComponents(bool $config): void
	{
		global $esBlocks;

		if (self::getStore() && self::getConfig()) {
			$esBlocks[self::getStoreName()][AbstractManifestCache::CONFIG_KEY]['useComponents'] = $config;
		}
	}

	/**
	 * Get global config value for use components.
	 *
	 * @return bool
	 */
	public static function getConfigUseComponents(): bool
	{
		return self::getConfig()['useComponents'] ?? self::$defaultState[AbstractManifestCache::CONFIG_KEY]['useComponents'];
	}

	/**
	 * Set global config value for use blocks.
	 *
	 * @param bool $config Config value.
	 *
	 * @return void
	 */
	public static function setConfigUseBlocks(bool $config): void
	{
		global $esBlocks;

		if (self::getStore() && self::getConfig()) {
			$esBlocks[self::getStoreName()][AbstractManifestCache::CONFIG_KEY]['useBlocks'] = $config;
		}
	}

	/**
	 * Get global config value for use blocks.
	 *
	 * @return bool
	 */
	public static function getConfigUseBlocks(): bool
	{
		return self::getConfig()['useBlocks'] ?? self::$defaultState[AbstractManifestCache::CONFIG_KEY]['useBlocks'] ?? false;
	}

	/**
	 * Set global config value for use variations.
	 *
	 * @param bool $config Config value.
	 *
	 * @return void
	 */
	public static function setConfigUseVariations(bool $config): void
	{
		global $esBlocks;

		if (self::getStore() && self::getConfig()) {
			$esBlocks[self::getStoreName()][AbstractManifestCache::CONFIG_KEY]['useVariations'] = $config;
		}
	}

	/**
	 * Get global config value for use variations.
	 *
	 * @return bool
	 */
	public static function getConfigUseVariations(): bool
	{
		return self::getConfig()['useVariations'] ?? self::$defaultState[AbstractManifestCache::CONFIG_KEY]['useVariations'];
	}

	/**
	 * Set wrapper details.
	 *
	 * @param array<mixed> $wrapper Wrapper details to set.
	 *
	 * @return void
	 */
	public static function setWrapper(array $wrapper): void
	{
		global $esBlocks;

		if (self::getStore()) {
			$esBlocks[self::getStoreName()][AbstractManifestCache::WRAPPER_KEY] = $wrapper;
		}
	}

	/**
	 * Get wrapper details.
	 *
	 * @return array<mixed>
	 */
	public static function getWrapper(): array
	{
		return self::getStore()[AbstractManifestCache::WRAPPER_KEY] ?? [];
	}

	/**
	 * Get wrapper details - attributes.
	 *
	 * @return array<mixed>
	 */
	public static function getWrapperAttributes(): array
	{
		return self::getWrapper()['attributes'] ?? [];
	}

	/**
	 * Set global settings details.
	 *
	 * @param array<mixed> $settings Settings details to store.
	 *
	 * @return void
	 */
	public static function setSettings(array $settings): void
	{
		global $esBlocks;

		if (self::getStore()) {
			$esBlocks[self::getStoreName()][AbstractManifestCache::SETTINGS_KEY] = $settings;
		}
	}

	/**
	 * Get global settings details.
	 *
	 * @return array<mixed>
	 */
	public static function getSettings(): array
	{
		return self::getStore()[AbstractManifestCache::SETTINGS_KEY] ?? [];
	}

	/**
	 * Get global settings details - block class prefix.
	 *
	 * @return string
	 */
	public static function getSettingsBlockClassPrefix(): string
	{
		return self::getSettings()['blockClassPrefix'] ?? 'block';
	}

	/**
	 * Get global settings details - attributes.
	 *
	 * @return array<mixed>
	 */
	public static function getSettingsAttributes(): array
	{
		return self::getSettings()['attributes'] ?? [];
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
	 * Get global settings details - global variables custom block name.
	 *
	 * @return string
	 */
	public static function getSettingsGlobalVariablesCustomBlockName(): string
	{
		return self::getSettingsGlobalVariables()['customBlocksName'] ?? '';
	}

	/**
	 * Set global settings details - global variables breakpoints.
	 *
	 * @param array<string> $breakpoints Breakpoints to store.
	 *
	 * @return void
	 */
	public static function setSettingsGlobalVariablesBreakpoints(array $breakpoints): void
	{
		global $esBlocks;

		if (self::getStore()) {
			$esBlocks[self::getStoreName()][AbstractManifestCache::SETTINGS_KEY]['globalVariables']['breakpoints'] = $breakpoints;
		}
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

	/**
	 * Set styles details full array.
	 *
	 * @param array<mixed> $styles Styles to set.
	 *
	 * @return void
	 */
	public static function setStyles(array $styles): void
	{
		global $esBlocks;

		if (self::getStore()) {
			$esBlocks[self::getStoreName()][AbstractManifestCache::STYLES_KEY] = $styles;
		}
	}

	/**
	 * Set styles details.
	 *
	 * @param array<mixed> $style Style to store.
	 *
	 * @return void
	 */
	public static function setStyle(array $style): void
	{
		global $esBlocks;

		if (self::getStore()) {
			$esBlocks[self::getStoreName()][AbstractManifestCache::STYLES_KEY][] = $style;
		}
	}

	/**
	 * Get styles details.
	 *
	 * @return array<mixed>
	 */
	public static function getStyles(): array
	{
		return self::getStore()[AbstractManifestCache::STYLES_KEY] ?? [];
	}

	/**
	 * Set assets details.
	 *
	 * @param array<mixed> $assets Assets to store.
	 *
	 * @return void
	 */
	public static function setAssets(array $assets): void
	{
		global $esBlocks;

		if (self::getStore()) {
			$esBlocks[self::getStoreName()][AbstractManifestCache::ASSETS_KEY][] = $assets;
		}
	}

	/**
	 * Get assets details.
	 *
	 * @return array<mixed>
	 */
	public static function getAssets(): array
	{
		return self::getStore()[AbstractManifestCache::ASSETS_KEY] ?? [];
	}

	/**
	 * Get asset details.
	 *
	 * @param string $asset Asset name to get.
	 *
	 * @return string
	 */
	public static function getAsset(string $asset): string
	{
		return self::getAssets()[$asset] ?? '';
	}
}
