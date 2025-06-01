<?php

/**
 * Helpers for rendering components and blocks.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

use EightshiftLibs\Exception\InvalidPath;

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
				'path' => self::getProjectPaths('components', [$renderPrefixPath, "{$renderName}.php"]),
				'manifest' => self::getComponent($componentName)
			];
		}

		return [
			'path' => self::getProjectPaths('components', [$renderPrefixPath, $renderName, "{$renderName}.php"]),
			'manifest' => self::getComponent($renderName)
		];
	}

	/**
	 * Handle wrapper render logic.
	 *
	 * @param string $renderName Render name.
	 * @param string $renderPrefixPath Prefix path.
	 * @param string $componentName Component name.
	 *
	 * @return array{path: string, manifest: array<mixed>}
	 */
	private static function handleWrapperRender(string $renderName, string $renderPrefixPath, string $componentName): array
	{
		return [
			'path' => self::getProjectPaths('wrapper', ["{$renderName}.php"]),
			'manifest' => self::getWrapper()
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
				'path' => self::getProjectPaths('blocks', [$renderPrefixPath, "{$renderName}.php"]),
				'manifest' => self::getBlock($componentName)
			];
		}

		return [
			'path' => self::getProjectPaths('blocks', [$renderPrefixPath, $renderName, "{$renderName}.php"]),
			'manifest' => self::getBlock($renderName)
		];
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
		string $renderContent = ''
	): string {
		// Initialize render caches and path caches
		self::initializeRenderCaches();

		// Initialize path caches if the method exists (for path operations)
		if (\method_exists(self::class, 'initializePathCaches')) {
			self::initializePathCaches();
		}

		// Set default path name if not provided (optimized with early return)
		if (!$renderPathName) {
			$renderPathName = self::getConfigUseLegacyComponents() ? 'components' : 'blocks';
		}

		// Fast path validation using pre-cached flipped array
		if (!isset(self::$allowedNamesFlipped[$renderPathName])) {
			throw InvalidPath::wrongOrNotAllowedParentPathException($renderPathName, \implode(', ', self::PROJECT_RENDER_ALLOWED_NAMES));
		}

		// Extract component/block name once if needed (optimized extraction)
		$componentName = '';
		if ($renderPrefixPath && ($renderPathName === 'components' || $renderPathName === 'blocks')) {
			$separatorPos = \strpos($renderPrefixPath, \DIRECTORY_SEPARATOR);
			$componentName = $separatorPos !== false ? \substr($renderPrefixPath, 0, $separatorPos) : $renderPrefixPath;
		}

		// Use optimized render handlers
		if (isset(self::$renderHandlers[$renderPathName])) {
			$result = self::$renderHandlers[$renderPathName]($renderName, $renderPrefixPath, $componentName);
			$renderPath = $result['path'];
			$manifest = $result['manifest'];
		} else {
			// Default case - optimized path building
			$renderPath = self::getProjectPaths('', [$renderPathName, $renderPrefixPath, "{$renderName}.php"]);
			$manifest = [];
		}

		// Early file existence check to fail fast
		if (!\file_exists($renderPath)) {
			throw InvalidPath::missingFileException($renderPath);
		}

		// Optimize attribute merging with early return
		if ($renderUseComponentDefaults && !empty($manifest)) {
			$renderAttributes = self::getDefaultRenderAttributes($manifest, $renderAttributes);
		}

		// Optimize output buffering and variable assignment
		\ob_start();

		// Pre-assign variables for performance (avoid repeated method calls)
		$attributes = $renderAttributes;
		$globalManifest = self::getSettings();

		// Unset variables for memory optimization
		unset($renderName, $renderAttributes, $renderPathName, $renderUseComponentDefaults, $renderPrefixPath, $componentName);

		include $renderPath;

		// Clean up variables
		unset($attributes, $renderContent, $renderPath, $manifest, $globalManifest);

		return \trim((string) \ob_get_clean());
	}
}
