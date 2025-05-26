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
		// Set default path name if not provided.
		if (!$renderPathName) {
			$renderPathName = Helpers::getConfigUseLegacyComponents() ? 'components' : 'blocks';
		}

		// Validate path name early.
		if (self::$allowedNamesFlipped === null) {
			self::$allowedNamesFlipped = \array_flip(self::PROJECT_RENDER_ALLOWED_NAMES);
		}

		if (!isset(self::$allowedNamesFlipped[$renderPathName])) {
			throw InvalidPath::wrongOrNotAllowedParentPathException($renderPathName, \implode(', ', self::PROJECT_RENDER_ALLOWED_NAMES));
		}

		// Initialize variables.
		$manifest = [];
		$renderPath = '';
		$componentName = '';

		// Extract component/block name once if needed.
		if ($renderPrefixPath && ($renderPathName === 'components' || $renderPathName === 'blocks')) {
			$componentName = \explode(\DIRECTORY_SEPARATOR, $renderPrefixPath)[0] ?? '';
		}

		// Build path and get manifest based on path type.
		switch ($renderPathName) {
			case 'components':
				if ($componentName) {
					$renderPath = Helpers::getProjectPaths('components', [$renderPrefixPath, "{$renderName}.php"]);
					$manifest = Helpers::getComponent($componentName);
				} else {
					$renderPath = Helpers::getProjectPaths('components', [$renderPrefixPath, $renderName, "{$renderName}.php"]);
					$manifest = Helpers::getComponent($renderName);
				}
				break;
			case 'wrapper':
				$renderPath = Helpers::getProjectPaths('wrapper', ["{$renderName}.php"]);
				$manifest = Helpers::getWrapper();
				break;
			case 'blocks':
				if ($componentName) {
					$renderPath = Helpers::getProjectPaths('blocks', [$renderPrefixPath, "{$renderName}.php"]);
					$manifest = Helpers::getBlock($componentName);
				} else {
					$renderPath = Helpers::getProjectPaths('blocks', [$renderPrefixPath, $renderName, "{$renderName}.php"]);
					$manifest = Helpers::getBlock($renderName);
				}
				break;
			default:
				$renderPath = Helpers::getProjectPaths('', [$renderPathName, $renderPrefixPath, "{$renderName}.php"]);
				break;
		}

		// Check if file exists.
		if (!\file_exists($renderPath)) {
			throw InvalidPath::missingFileException($renderPath);
		}

		// Merge default attributes with the component attributes if needed.
		if ($renderUseComponentDefaults && !empty($manifest)) {
			$renderAttributes = Helpers::getDefaultRenderAttributes($manifest, $renderAttributes);
		}

		// Start output buffering and include the file.
		\ob_start();

		// Allowed variables are $attributes, $renderAttributes, $renderContent, $renderPath, $manifest, $globalManifest.
		$attributes = $renderAttributes;
		$globalManifest = Helpers::getSettings();

		unset(
			$renderName,
			$renderAttributes,
			$renderPathName,
			$renderUseComponentDefaults,
			$renderPrefixPath,
			$componentName,
		);

		include $renderPath;

		unset(
			$attributes,
			$renderContent,
			$renderPath,
			$manifest,
			$globalManifest
		);

		return \trim((string) \ob_get_clean());
	}


	/**
	 * Internal helper for getting all project paths for easy mocking in tests.
	 *
	 * @param string $type Type fo path to return.
	 * @param string|array<int, string> $suffix Suffix to add to the path.
	 *
	 * @return string
	 */
	public static function getProjectPaths(string $type = '', array|string $suffix = ''): string
	{
		$root = \dirname(__FILE__, 6);

		if (\is_string($suffix)) {
			$suffix = [$suffix];
		}

		$projectRoot = \dirname(__FILE__, 9);

		switch ($type) {
			case 'root':
				return self::joinPaths([$projectRoot, ...$suffix]);
			case 'eightshift':
				return self::joinPaths([$root, 'eightshift', ...$suffix]);
			case 'src':
				return self::joinPaths([$root, 'src', ...$suffix]);
			case 'public':
				return self::joinPaths([$root, 'public', ...$suffix]);
			case 'libsPrefixed':
				return self::joinPaths([$root, 'vendor-prefixed', 'infinum', 'eightshift-libs', ...$suffix]);
			case 'libsPrefixedGeolocation':
				return self::joinPaths([$root, 'vendor-prefixed', 'infinum', 'eightshift-libs', 'src', 'Geolocation', ...$suffix]);
			case 'blocksRoot':
				return self::joinPaths([$root, 'src', 'Blocks', ...$suffix]);
			case 'blocks':
				return self::joinPaths([$root, 'src', 'Blocks', 'custom', ...$suffix]);
			case 'components':
				return self::joinPaths([$root, 'src', 'Blocks', 'components', ...$suffix]);
			case 'variations':
				return self::joinPaths([$root, 'src', 'Blocks', 'variations', ...$suffix]);
			case 'wrapper':
				return self::joinPaths([$root, 'src', 'Blocks', 'wrapper', ...$suffix]);
			default:
				return self::joinPaths([$root, ...$suffix]);
		}
	}

	/**
	 * Paths join
	 *
	 * @param array<int, string> $paths Paths to join.
	 *
	 * @return string
	 */
	public static function joinPaths(array $paths): string
	{
		$sep = \DIRECTORY_SEPARATOR;

		$paths = \array_map(fn($path) => \trim($path, $sep), $paths);
		$paths = \array_filter($paths);

		$path = $sep . \implode($sep, $paths);

		return !\pathinfo($path, \PATHINFO_EXTENSION) ? $path . $sep : $path;
	}

	/**
	 * Get eightshift root folder output path and create the directory if it doesn't exist.
	 *
	 * @param string $fileName File name to append to the path.
	 *
	 * @return string
	 */
	public static function getEightshiftOutputPath($fileName = ''): string
	{
		$filePath = Helpers::getProjectPaths('eightshift');

		if (!\is_dir($filePath)) {
			\mkdir($filePath, 0755, true); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_mkdir
		}

		if ($fileName) {
			return "{$filePath}{$fileName}";
		}

		return $filePath;
	}
}
