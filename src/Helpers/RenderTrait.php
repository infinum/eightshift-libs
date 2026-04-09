<?php

/**
 * Helpers for rendering components and blocks.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

use EightshiftLibs\Exception\InvalidPath;
use WP_Block;

/**
 * Class RenderTrait Helper
 */
trait RenderTrait
{
	/**
	 * Get all project paths allowed to be used in the different render methods.
	 *
	 * @var array<int, string>
	 */
	private const PROJECT_RENDER_ALLOWED_NAMES = [
		'src',
		'blocksRoot',
		'blocks',
		'components',
		'variations',
		'wrapper',
		'themeRoot',
		'pluginRoot',
	];

	/**
	 * Cached flipped array for faster lookups.
	 *
	 * @var array<string, int>|null
	 */
	private static ?array $allowedNamesFlipped = null;

	/**
	 * Cached render type patterns for performance.
	 *
	 * @var array<string, callable>|null
	 */
	private static ?array $renderHandlers = null;

	/**
	 * Initialize render-related static caches if not already done.
	 *
	 * @return void
	 */
	private static function initializeRenderCaches(): void
	{
		if (self::$allowedNamesFlipped === null) {
			self::$allowedNamesFlipped = \array_flip(self::PROJECT_RENDER_ALLOWED_NAMES);
		}

		if (self::$renderHandlers === null) {
			self::$renderHandlers = [
				'components' => [self::class, 'handleComponentsRender'],
				'wrapper' => [self::class, 'handleWrapperRender'],
				'blocks' => [self::class, 'handleBlocksRender'],
			];
		}
	}

	/**
	 * Handle components render logic.
	 *
	 * @param string $renderName Render name.
	 * @param string $renderPrefixPath Prefix path.
	 * @param string $componentName Component name.
	 *
	 * @return array{path: string, manifest: array<mixed>}
	 */
	private static function handleComponentsRender(string $renderName, string $renderPrefixPath, string $componentName): array
	{
		if ($componentName) {
			return [
				'path' => Helpers::getProjectPaths('components', [$renderPrefixPath, "{$renderName}.php"]),
				'manifest' => Helpers::getComponent($componentName)
			];
		}

		return [
			'path' => Helpers::getProjectPaths('components', [$renderPrefixPath, $renderName, "{$renderName}.php"]),
			'manifest' => Helpers::getComponent($renderName)
		];
	}

	/**
	 * Handle wrapper render logic.
	 *
	 * @param string $renderName Render name.
	 *
	 * @return array{path: string, manifest: array<mixed>}
	 */
	private static function handleWrapperRender(string $renderName): array
	{
		return [
			'path' => Helpers::getProjectPaths('wrapper', ["{$renderName}.php"]),
			'manifest' => Helpers::getWrapper()
		];
	}

	/**
	 * Handle blocks render logic.
	 *
	 * @param string $renderName Render name.
	 * @param string $renderPrefixPath Prefix path.
	 * @param string $componentName Component name.
	 *
	 * @return array{path: string, manifest: array<mixed>}
	 */
	private static function handleBlocksRender(string $renderName, string $renderPrefixPath, string $componentName): array
	{
		if ($componentName) {
			return [
				'path' => Helpers::getProjectPaths('blocks', [$renderPrefixPath, "{$renderName}.php"]),
				'manifest' => Helpers::getBlock($componentName)
			];
		}

		return [
			'path' => Helpers::getProjectPaths('blocks', [$renderPrefixPath, $renderName, "{$renderName}.php"]),
			'manifest' => Helpers::getBlock($renderName)
		];
	}

	/**
	 * Recursively clean inner blocks data to only include necessary properties.
	 *
	 * @param array<int, WP_Block> $innerBlocks The inner blocks data to clean.
	 *
	 * @return array<int, array{name: string, attributes: array<string, mixed>, innerBlocks: array<int, array>}> Cleaned inner blocks data.
	 */
	private static function cleanInnerBlocks(array $innerBlocks): array // @phpstan-ignore-line
	{
		return \array_map(static function ($blockData) {
			return [
				'name' => $blockData->name,
				'attributes' => $blockData->attributes,
				// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
				'innerBlocks' => self::cleanInnerBlocks([...($blockData->inner_blocks ?? [])]),
			];
		}, $innerBlocks);
	}

	/**
	 * Renders a components and (optionally) passes some attributes to it.
	 *
	 * @param string $renderName The name of the component to render.
	 * @param array<string, mixed> $renderAttributes The attributes to pass to the component.
	 * @param string $renderPathName The path name where the component is located.
	 * @param bool $renderUseComponentDefaults Should we use the default attributes from the component.
	 * @param string $renderPrefixPath The prefix path to the component.
	 * @param string $renderContent The content to pass to the component.
	 * @param WP_Block|null $renderBlock The current WP_Block instance, available as $block in the template.
	 *
	 * @throws InvalidPath If the file is missing.
	 *
	 * @return string
	 */
	public static function render(
		string $renderName,
		array $renderAttributes = [],
		string $renderPathName = '',
		bool $renderUseComponentDefaults = false,
		string $renderPrefixPath = '',
		string $renderContent = '',
		?WP_Block $renderBlock = null
	): string {
		// Initialize render caches and path caches.
		self::initializeRenderCaches();

		Helpers::initializePathCaches();

		// Set default path name if not provided (optimized with early return).
		if (!$renderPathName) {
			$renderPathName = Helpers::getConfigUseLegacyComponents() ? 'components' : 'blocks';
		}

		// Fast path validation using pre-cached flipped array.
		if (!isset(self::$allowedNamesFlipped[$renderPathName])) {
			throw InvalidPath::wrongOrNotAllowedParentPathException($renderPathName, \implode(', ', self::PROJECT_RENDER_ALLOWED_NAMES));
		}

		// Extract component/block name once if needed (optimized extraction).
		$componentName = '';
		if ($renderPrefixPath && ($renderPathName === 'components' || $renderPathName === 'blocks')) {
			$separatorPos = \strpos($renderPrefixPath, \DIRECTORY_SEPARATOR);
			$componentName = $separatorPos !== false ? \substr($renderPrefixPath, 0, $separatorPos) : $renderPrefixPath;
		}

		// Use optimized render handlers.
		if (isset(self::$renderHandlers[$renderPathName])) {
			$result = self::$renderHandlers[$renderPathName]($renderName, $renderPrefixPath, $componentName);
			$renderPath = $result['path'];
			$manifest = $result['manifest'];
		} else {
			// Default case - optimized path building.
			$renderPath = Helpers::getProjectPaths('', [$renderPathName, $renderPrefixPath, "{$renderName}.php"]);
			$manifest = [];
		}

		// Early file existence check to fail fast.
		if (!\file_exists($renderPath)) {
			throw InvalidPath::missingFileException($renderPath);
		}

		// Optimize attribute merging with early return.
		if ($renderUseComponentDefaults && !empty($manifest)) {
			$renderAttributes = Helpers::getDefaultRenderAttributes($manifest, $renderAttributes);
		}

		// Optimize output buffering and variable assignment.
		\ob_start();

		// Pre-assign variables for performance (avoid repeated method calls).
		$attributes = $renderAttributes;
		$globalManifest = Helpers::getSettings();

		$innerBlockData = null;

		// Only process innerBlocks data for blocks to avoid unnecessary processing.
		if ($renderPathName === 'blocks') {
			// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
			$innerBlockData = [...($renderBlock->inner_blocks ?? [])];

			if (!empty($innerBlockData)) {
				$innerBlockData = self::cleanInnerBlocks($innerBlockData);
			}
		}

		// Unset variables for memory optimization.
		unset($renderName, $renderAttributes, $renderPathName, $renderUseComponentDefaults, $renderPrefixPath, $componentName, $renderBlock);

		include $renderPath;

		// Clean up variables.
		unset($attributes, $renderContent, $innerBlockData, $renderPath, $manifest, $globalManifest);

		return \trim((string) \ob_get_clean());
	}
}
