<?php

/**
 * All the helpers for components.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

use EightshiftLibs\Exception\InvalidPath;

/**
 * All the helpers for components.
 */
class Helpers
{
	/**
	 * Cache trait.
	 */
	use CacheTrait;

	/**
	 * Store blocks trait.
	 */
	use StoreBlocksTrait;

	/**
	 * Css Variables trait.
	 */
	use CssVariablesTrait;

	/**
	 * Selectors trait.
	 */
	use SelectorsTrait;

	/**
	 * Attributes trait.
	 */
	use AttributesTrait;

	/**
	 * Generic object helper trait.
	 */
	use ObjectHelperTrait;

	/**
	 * Shortcode trait.
	 */
	use ShortcodeTrait;

	/**
	 * Post trait.
	 */
	use PostTrait;

	/**
	 * Media trait.
	 */
	use MediaTrait;

	/**
	 * API trait.
	 */
	use ApiTrait;

	/**
	 * Project info trait.
	 */
	use ProjectInfoTrait;

	/**
	 * Deprecated trait.
	 */
	use DeprecatedTrait;

	/**
	 * TailwindCSS trait.
	 */
	use TailwindTrait;

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
	 * Cached base paths for performance optimization.
	 *
	 * @var array<string, string>|null
	 */
	private static ?array $basePaths = null;

	/**
	 * Cached path configurations for fast lookups.
	 *
	 * @var array<string, array<string>>|null
	 */
	private static ?array $pathConfigs = null;

	/**
	 * Cached render type patterns for performance.
	 *
	 * @var array<string, callable>|null
	 */
	private static ?array $renderHandlers = null;

	/**
	 * Initialize static caches if not already done.
	 *
	 * @return void
	 */
	private static function initializeCaches(): void
	{
		if (self::$allowedNamesFlipped === null) {
			self::$allowedNamesFlipped = \array_flip(self::PROJECT_RENDER_ALLOWED_NAMES);
		}

		if (self::$basePaths === null) {
			$root = \dirname(__FILE__, 6);
			$projectRoot = \dirname(__FILE__, 9);

			self::$basePaths = [
				'root' => $root,
				'projectRoot' => $projectRoot,
				'src' => $root . \DIRECTORY_SEPARATOR . 'src',
				'public' => $root . \DIRECTORY_SEPARATOR . 'public',
				'blocksRoot' => $root . \DIRECTORY_SEPARATOR . 'src' . \DIRECTORY_SEPARATOR . 'Blocks',
			];
		}

		if (self::$pathConfigs === null) {
			$sep = \DIRECTORY_SEPARATOR;
			$root = self::$basePaths['root'];
			$projectRoot = self::$basePaths['projectRoot'];

			self::$pathConfigs = [
				'root' => [$projectRoot],
				'eightshift' => [$root, 'eightshift'],
				'eightshiftRoot' => [$projectRoot, 'eightshift'],
				'src' => [$root, 'src'],
				'public' => [$root, 'public'],
				'libsPrefixed' => [$root, 'vendor-prefixed', 'infinum', 'eightshift-libs'],
				'libsPrefixedGeolocation' => [$root, 'vendor-prefixed', 'infinum', 'eightshift-libs', 'src', 'Geolocation'],
				'blocksRoot' => [$root, 'src', 'Blocks'],
				'blocks' => [$root, 'src', 'Blocks', 'custom'],
				'components' => [$root, 'src', 'Blocks', 'components'],
				'variations' => [$root, 'src', 'Blocks', 'variations'],
				'wrapper' => [$root, 'src', 'Blocks', 'wrapper'],
			];
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
		// Initialize all caches once
		self::initializeCaches();

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

	/**
	 * Internal helper for getting all project paths for easy mocking in tests.
	 * Optimized with static caching and reduced function calls.
	 *
	 * @param string $type Type fo path to return.
	 * @param string|array<int, string> $suffix Suffix to add to the path.
	 *
	 * @return string
	 */
	public static function getProjectPaths(string $type = '', array|string $suffix = ''): string
	{
		// Initialize caches if needed
		self::initializeCaches();

		// Convert string suffix to array efficiently
		if (\is_string($suffix)) {
			$suffix = $suffix !== '' ? [$suffix] : [];
		}

		// Fast path for empty type
		if ($type === '') {
			return self::joinPathsOptimized(\array_merge([self::$basePaths['root']], $suffix));
		}

		// Use cached path configuration for fast lookup
		if (isset(self::$pathConfigs[$type])) {
			return self::joinPathsOptimized(\array_merge(self::$pathConfigs[$type], $suffix));
		}

		// Fallback for unknown type (should rarely happen)
		return self::joinPathsOptimized(\array_merge([self::$basePaths['root']], $suffix));
	}

	/**
	 * Optimized paths join with reduced function calls and memory allocations.
	 *
	 * @param array<int, string> $paths Paths to join.
	 *
	 * @return string
	 */
	private static function joinPathsOptimized(array $paths): string
	{
		// Early return for empty paths
		if (empty($paths)) {
			return \DIRECTORY_SEPARATOR;
		}

		$sep = \DIRECTORY_SEPARATOR;

		// Pre-allocate and filter in single pass for better performance
		$filteredPaths = [];
		foreach ($paths as $path) {
			$trimmed = \trim($path, $sep);
			if ($trimmed !== '') {
				$filteredPaths[] = $trimmed;
			}
		}

		if (empty($filteredPaths)) {
			return $sep;
		}

		$joinedPath = $sep . \implode($sep, $filteredPaths);

		// Optimized extension check using string comparison
		$lastPart = $filteredPaths[\count($filteredPaths) - 1];
		$hasExtension = \str_contains($lastPart, '.');

		return $hasExtension ? $joinedPath : $joinedPath . $sep;
	}

	/**
	 * Legacy joinPaths method for backward compatibility.
	 *
	 * @param array<int, string> $paths Paths to join.
	 *
	 * @return string
	 */
	public static function joinPaths(array $paths): string
	{
		return self::joinPathsOptimized($paths);
	}

	/**
	 * Get eightshift root folder output path and create the directory if it doesn't exist.
	 * Optimized with static caching.
	 *
	 * @param string $fileName File name to append to the path.
	 *
	 * @return string
	 */
	public static function getEightshiftOutputPath($fileName = ''): string
	{
		static $eightshiftPath = null;

		if ($eightshiftPath === null) {
			$eightshiftPath = self::getProjectPaths('eightshift');

			if (!\is_dir($eightshiftPath)) {
				\mkdir($eightshiftPath, 0755, true); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_mkdir
			}
		}

		return $fileName !== '' ? $eightshiftPath . $fileName : $eightshiftPath;
	}
}
