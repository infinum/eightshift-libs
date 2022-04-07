<?php

/**
 * Helpers for store.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

/**
 * Class StoreTrait Helper
 */
trait StoreTrait
{
	/**
	 * Store default state
	 *
	 * @var array<mixed>
	 */
	public static $defaultState = [
		'blocks' => [],
		'components' => [],
		'config' => [
			'outputCssGlobally' => false,
			'outputCssOptimize' => false,
			'outputCssSelectorName' => 'esCssVariables',
			'outputCssGloballyAdditionalStyles' => [],
		],
		'wrapper' => [],
		'settings' => [],
		'styles' => [],
	];

	/**
	 * Get full store name.
	 *
	 * @return string
	 */
	public static function getStoreName(): string
	{
		return \basename(\dirname(__DIR__, 5));
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
			$esBlocks[self::getStoreName()]['blocks'] = $blocks;
		}
	}

	/**
	 * Get blocks details.
	 *
	 * @return array<mixed>
	 */
	public static function getBlocks(): array
	{
		return self::getStore()['blocks'] ?? [];
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
		$blocks = \array_filter(
			self::getBlocks(),
			static function ($item) use ($block) {
				return $item['blockName'] === $block;
			}
		);

		return \reset($blocks) ?: []; // phpcs:ignore WordPress.PHP.DisallowShortTernary.Found
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
			$esBlocks[self::getStoreName()]['components'] = $components;
		}
	}

	/**
	 * Get components details.
	 *
	 * @return array<mixed>
	 */
	public static function getComponents(): array
	{
		return self::getStore()['components'] ?? [];
	}

	/**
	 * Get component details.
	 *
	 * @param string $component Componennt name to get.
	 * @return array<mixed>
	 */
	public static function getComponent(string $component): array
	{
		$components = \array_filter(
			self::getComponents(),
			static function ($item) use ($component) {
				return $item['componentName'] === $component;
			}
		);

		return \reset($components) ?: []; // phpcs:ignore WordPress.PHP.DisallowShortTernary.Found
	}

	/**
	 * Set all config flags overriding from global settings manifest.json.
	 *
	 * @return void
	 */
	public static function setConfigFlags(): void
	{
		$config = self::getSettings()['config'] ?? [];

		if ($config) {
			// outputCssGlobally.
			if (isset($config['outputCssGlobally']) && \gettype($config['outputCssGlobally']) === 'boolean') {
				Components::setConfigOutputCssGlobally($config['outputCssGlobally']);
			}

			// outputCssOptimize.
			if (isset($config['outputCssOptimize']) && \gettype($config['outputCssOptimize']) === 'boolean') {
				Components::setConfigOutputCssOptimize($config['outputCssOptimize']);
			}

			// outputCssSelectorName.
			if (isset($config['outputCssSelectorName']) && \gettype($config['outputCssSelectorName']) === 'string') {
				Components::setConfigOutputCssSelectorName($config['outputCssSelectorName']);
			}

			// outputCssGloballyAdditionalStyles.
			if (isset($config['outputCssGloballyAdditionalStyles']) && \gettype($config['outputCssGloballyAdditionalStyles']) === 'array') {
				Components::setConfigOutputCssGloballyAdditionalStyles($config['outputCssGloballyAdditionalStyles']);
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
		return self::getStore()['config'] ?? [];
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
			$esBlocks[self::getStoreName()]['config']['outputCssGlobally'] = $config;
		}
	}

	/**
	 * Get global config value for output css globally.
	 *
	 * @return boolean
	 */
	public static function getConfigOutputCssGlobally(): bool
	{
		return self::getConfig()['outputCssGlobally'] ?? self::$defaultState['config']['outputCssGlobally'];
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
			$esBlocks[self::getStoreName()]['config']['outputCssOptimize'] = $config;
		}
	}

	/**
	 * Get global config value for output css optimize.
	 *
	 * @return boolean
	 */
	public static function getConfigOutputCssOptimize(): bool
	{
		return self::getConfig()['outputCssOptimize'] ?? self::$defaultState['config']['outputCssOptimize'];
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
			$esBlocks[self::getStoreName()]['config']['outputCssSelectorName'] = $config;
		}
	}

	/**
	 * Get global config value for output css selector name.
	 *
	 * @return string
	 */
	public static function getConfigOutputCssSelectorName(): string
	{
		return self::getConfig()['outputCssSelectorName'] ?? self::$defaultState['config']['outputCssSelectorName'];
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
			$esBlocks[self::getStoreName()]['config']['outputCssGloballyAdditionalStyles'] = $config;
		}
	}


	/**
	 * Get global config value for output css globally additional styles.
	 *
	 * @return array<string>
	 */
	public static function getConfigOutputCssGloballyAdditionalStyles(): array
	{
		return self::getConfig()['outputCssGloballyAdditionalStyles'] ?? self::$defaultState['config']['outputCssGloballyAdditionalStyles'];
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
			$esBlocks[self::getStoreName()]['wrapper'] = $wrapper;
		}
	}

	/**
	 * Get wrapper details.
	 *
	 * @return array<mixed>
	 */
	public static function getWrapper(): array
	{
		return self::getStore()['wrapper'] ?? [];
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
			$esBlocks[self::getStoreName()]['settings'] = $settings;
		}
	}

	/**
	 * Get global settings details.
	 *
	 * @return array<mixed>
	 */
	public static function getSettings(): array
	{
		return self::getStore()['settings'] ?? [];
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
			$esBlocks[self::getStoreName()]['styles'] = $styles;
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
			$esBlocks[self::getStoreName()]['styles'][] = $style;
		}
	}

	/**
	 * Get styles details.
	 *
	 * @return array<mixed>
	 */
	public static function getStyles(): array
	{
		return self::getStore()['styles'] ?? [];
	}
}
