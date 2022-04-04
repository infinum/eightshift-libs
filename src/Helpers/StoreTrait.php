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

	public static $defaultState = [
		'blocks' => [],
		'components' => [],
		'config' => [
			'outputCssGlobally' => false,
			'outputCssOptimize' => false,
			'outputCssSelectorName' => 'esCssVariables',
		],
		'wrapper' => [],
		'settings' => [],
		'styles' => [],
	];

	public static function getStoreName(): string
	{
		return \basename(\dirname(__DIR__, 5));
	}

	public static function setStore(): void
	{
		global $esBlocks;

		$store = self::getStore();

		if (!$store) {
			$esBlocks[self::getStoreName()] = self::$defaultState;
		}
	}

	public static function getStore(): array
	{
		global $esBlocks;

		return $esBlocks[self::getStoreName()] ?? [];
	}

	public static function setBlocks(array $blocks): void
	{
		global $esBlocks;

		$store = self::getBlocks();

		if (!$store) {
			$esBlocks[self::getStoreName()]['blocks'] = $blocks;
		}
	}

	public static function getBlocks(): array
	{
		return self::getStore()['blocks'] ?? [];
	}

	public static function getBlock(string $block): array
	{
		$blocks = array_filter(
			self::getBlocks(),
			static function ($item) use ($block) {
				return $item['blockName'] === $block;
			}
		);

		return reset($blocks) ?: [];
	}

	public static function setComponents(array $components): void
	{
		global $esBlocks;

		$store = self::getComponents();

		if (!$store) {
			$esBlocks[self::getStoreName()]['components'] = $components;
		}
	}

	public static function getComponents(): array
	{
		return self::getStore()['components'] ?? [];
	}

	public static function getComponent(string $component): array
	{
		$components = array_filter(
			self::getComponents(),
			static function ($item) use ($component) {
				return $item['componentName'] === $component;
			}
		);

		return reset($components) ?: [];
	}

	public static function setConfigFlags(): void
	{
		$config = self::getSettings()['config'] ?? [];

		if ($config) {
			// outputCssGlobally.
			if (isset($config['outputCssGlobally']) && gettype($config['outputCssGlobally']) === 'boolean') {
				Components::setConfigOutputCssGlobally($config['outputCssGlobally']);
			}

			// outputCssOptimize.
			if (isset($config['outputCssOptimize']) && gettype($config['outputCssOptimize']) === 'boolean') {
				Components::setConfigOutputCssOptimize($config['outputCssOptimize']);
			}

			// outputCssSelectorName.
			if (isset($config['outputCssSelectorName']) && gettype($config['outputCssSelectorName']) === 'string') {
				Components::setConfigOutputCssSelectorName($config['outputCssSelectorName']);
			}
		}
	}

	public static function getConfig(): array
	{
		return self::getStore()['config'] ?? [];
	}

	public static function setConfigOutputCssGlobally(bool $config): void
	{
		global $esBlocks;

		$esBlocks[self::getStoreName()]['config']['outputCssGlobally'] = $config;
	}

	public static function getConfigOutputCssGlobally(): bool
	{
		return self::getConfig()['outputCssGlobally'] ?? self::$defaultState['config']['outputCssGlobally'];
	}

	public static function setConfigOutputCssOptimize(bool $config): void
	{
		global $esBlocks;

		$esBlocks[self::getStoreName()]['config']['outputCssOptimize'] = $config;
	}

	public static function getConfigOutputCssOptimize(): bool
	{
		return self::getConfig()['outputCssOptimize'] ?? self::$defaultState['config']['outputCssOptimize'];
	}

	public static function setConfigOutputCssSelectorName(string $config): void
	{
		global $esBlocks;

		$esBlocks[self::getStoreName()]['config']['outputCssSelectorName'] = $config;
	}

	public static function getConfigOutputCssSelectorName(): string
	{
		return self::getConfig()['outputCssSelectorName'] ?? self::$defaultState['config']['outputCssSelectorName'];
	}

	public static function setWrapper(array $wrapper): void
	{
		global $esBlocks;

		$store = self::getWrapper();

		if (!$store) {
			$esBlocks[self::getStoreName()]['wrapper'] = $wrapper;
		}
	}

	public static function getWrapper(): array
	{
		return self::getStore()['wrapper'] ?? [];
	}

	public static function getWrapperAttributes(): array
	{
		return self::getWrapper()['attributes'] ?? [];
	}

	public static function setSettings(array $settings): void
	{
		global $esBlocks;

		$esBlocks[self::getStoreName()]['settings'] = $settings;
	}

	public static function getSettings(): array
	{
		return self::getStore()['settings'] ?? [];
	}

	public static function getSettingsBlockClassPrefix(): string
	{
		return self::getSettings()['blockClassPrefix'] ?? 'block';
	}

	public static function getSettingsAttributes(): array
	{
		return self::getSettings()['attributes'] ?? [];
	}

	public static function getSettingsNamespace(): string
	{
		return self::getSettings()['namespace'] ?? '';
	}

	public static function getSettingsGlobalVariables(): array
	{
		return self::getSettings()['globalVariables'] ?? [];
	}

	public static function getSettingsGlobalVariablesCustomBlockName(): string
	{
		return self::getSettingsGlobalVariables()['customBlocksName'] ?? '';
	}

	public static function getSettingsGlobalVariablesBreakpoints(): array
	{
		return self::getSettingsGlobalVariables()['breakpoints'] ?? [];
	}

	public static function getSettingsGlobalVariablesColors(): array
	{
		return self::getSettingsGlobalVariables()['colors'] ?? [];
	}

	public static function setStyle(array $style): void
	{
		global $esBlocks;

		$esBlocks[self::getStoreName()]['style'] = $style;
	}

	public static function getStyles(): array
	{
		return self::getStore()['styles'] ?? [];
	}
}
