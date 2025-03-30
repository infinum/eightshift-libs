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
		string $renderPathName = 'components',
		bool $renderUseComponentDefaults = false,
		string $renderPrefixPath = '',
		string $renderContent = ''
	): string {
		$manifest = [];

		switch ($renderPathName) {
			case 'components':
				$componentName = \explode(\DIRECTORY_SEPARATOR, $renderPrefixPath)[0] ?? '';

				if ($componentName) {
					$renderPath = Helpers::getProjectPaths('components', [$renderPrefixPath, "{$renderName}.php"]);
					$manifest = Helpers::getComponent($componentName);
				} else {
					$renderPath = Helpers::getProjectPaths('components', [$renderPrefixPath, $renderName, "{$renderName}.php"]);
					$manifest = Helpers::getComponent($renderName);
				}

				unset($componentName);

				break;
			case 'wrapper':
				$manifest = Helpers::getWrapper();
				$renderPath = Helpers::getProjectPaths('wrapper', ["{$renderName}.php"]);
				break;
			case 'blocks':
				$blockName = \explode(\DIRECTORY_SEPARATOR, $renderPrefixPath)[0] ?? '';

				if ($blockName) {
					$renderPath = Helpers::getProjectPaths('blocks', [$renderPrefixPath, "{$renderName}.php"]);
					$manifest = Helpers::getBlock($blockName);
				} else {
					$renderPath = Helpers::getProjectPaths('blocks', [$renderPrefixPath, $renderName, "{$renderName}.php"]);
					$manifest = Helpers::getBlock($renderName);
				}

				unset($blockName);
				break;
			default:
				$renderPath = Helpers::getProjectPaths('', [$renderPathName, $renderPrefixPath, "{$renderName}.php"]);
				break;
		}

		if (!isset(\array_flip(self::PROJECT_RENDER_ALLOWED_NAMES)[$renderPathName])) {
			throw InvalidPath::wrongOrNotAllowedParentPathException($renderPathName, \implode(', ', self::PROJECT_RENDER_ALLOWED_NAMES));
		}

		if (!\file_exists($renderPath)) {
			throw InvalidPath::missingFileException($renderPath);
		}

		// Merge default attributes with the component attributes.
		if ($renderUseComponentDefaults && !empty($manifest)) {
			$renderAttributes = Helpers::getDefaultRenderAttributes($manifest, $renderAttributes);
		}

		\ob_start();

		// Allowed variables are $attributes, $renderAttributes, $renderContent, $renderPath, $manifest, $globalManifest.
		$attributes = $renderAttributes;
		$globalManifest = Helpers::getSettings();

		unset(
			$renderName,
			$renderPathName,
			$renderUseComponentDefaults,
			$renderPrefixPath,
		);

		include $renderPath;

		unset(
			$renderAttributes,
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
	public static function getProjectPaths(string $type, array|string $suffix = ''): string
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
				return self::joinPaths([$projectRoot, 'eightshift', ...$suffix]);
			case 'src':
				return self::joinPaths([$root, 'src', ...$suffix]);
			case 'public':
				return self::joinPaths([$root, 'public', ...$suffix]);
			case 'libsPrefixed':
				return self::joinPaths([$root, 'vendor-prefixed', 'infinum', 'eightshift-libs']);
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
			return $filePath . \DIRECTORY_SEPARATOR . $fileName;
		}

		return $filePath;
	}
}
