<?php

/**
 * Helpers for path operations and management.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

/**
 * Class PathsTrait Helper
 */
trait PathsTrait
{
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
	 * Initialize path-related static caches if not already done.
	 *
	 * @return void
	 */
	public static function initializePathCaches(): void
	{
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
		self::initializePathCaches();

		// Convert string suffix to array efficiently
		if (\is_string($suffix)) {
			$suffix = $suffix !== '' ? [$suffix] : [];
		}

		// Fast path for empty type
		if ($type === '') {
			return self::joinPaths(\array_merge([self::$basePaths['root']], $suffix));
		}

		// Use cached path configuration for fast lookup
		if (isset(self::$pathConfigs[$type])) {
			return self::joinPaths(\array_merge(self::$pathConfigs[$type], $suffix));
		}

		// Fallback for unknown type (should rarely happen)
		return self::joinPaths(\array_merge([self::$basePaths['root']], $suffix));
	}

	/**
	 * Optimized paths join with reduced function calls and memory allocations.
	 *
	 * @param array<int, string> $paths Paths to join.
	 *
	 * @return string
	 */
	public static function joinPaths(array $paths): string
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
