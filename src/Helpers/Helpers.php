<?php

/**
 * All the helpers for components.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

use EightshiftLibs\Exception\InvalidManifest;
use EightshiftLibs\Exception\InvalidPath;

/**
 * All the helpers for components.
 */
class Helpers
{
	/**
	 * Store trait.
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
	 * Get all project paths allowed to be used in the different render methods.
	 *
	 * @var array<int, string>
	 */
	private const PROJECT_RENDER_ALLOWED_NAMES = [
		'blocks',
		'components',
		'variations',
		'wrapper',
		'root',
		'srcDestination',
		'themeRoot',
		'pluginRoot',
		'blocksDestination',
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
		if (empty($renderPathName)) {
			$renderPathName = 'components';
		}

		if ($renderName === 'wrapper') {
			$renderPathName = 'blocksDestination';
		}

		if (!isset(\array_flip(self::PROJECT_RENDER_ALLOWED_NAMES)[$renderPathName])) {
			throw InvalidPath::wrongOrNotAllowedParentPathException($renderPathName, \implode(', ', self::PROJECT_RENDER_ALLOWED_NAMES));
		}

		$renderPrefix = $renderName;

		if (!empty($renderPrefixPath)) {
			$renderPrefix = $renderPrefixPath;
		}

		$renderPath = self::joinPaths([Helpers::getProjectPaths($renderPathName), $renderPrefix, "{$renderName}.php"]);

		if (!\file_exists($renderPath)) {
			throw InvalidPath::missingFileException($renderPath);
		}

		// Merge default attributes with the component attributes.
		if ($renderUseComponentDefaults) {
			$renderAttributes = Helpers::getDefaultRenderAttributes(Helpers::getComponent($renderName), $renderAttributes);
		}

		\ob_start();

		// Allowed variables are $attributes, $renderAttributes, $renderContent, $renderPath.
		$attributes = $renderAttributes;

		unset(
			$renderName,
			$renderPathName,
			$renderUseComponentDefaults,
			$renderPrefixPath,
			$renderPrefix
		);

		include $renderPath;

		unset(
			$renderAttributes,
			$attributes,
			$renderContent,
			$renderPath
		);

		return \trim((string) \ob_get_clean());
	}

	/**
	 * Get manifest json by path and name.
	 *
	 * @param string $path Absolute path to .
	 *
	 * @throws InvalidManifest If the manifest is not allowed.
	 *
	 * @return array<string, mixed>
	 */
	public static function getManifestByDir(string $path): array
	{
		$sep = \DIRECTORY_SEPARATOR;
		$root = Helpers::getProjectPaths('srcDestination');
		$newPath = \str_replace($root, '', $path);
		$newPath = \explode($sep, $newPath);

		if (!isset($newPath[0]) && $newPath[0] !== 'Blocks') {
			throw InvalidManifest::notAllowedManifestPathException($path);
		}

		if (!isset($newPath[1])) {
			throw InvalidManifest::notAllowedManifestPathException($path);
		}

		switch ($newPath[1]) {
			case 'wrapper':
				return Helpers::getWrapper();
			case 'components':
				return Helpers::getComponent(\end($newPath));
			case 'custom':
				return Helpers::getBlock(\end($newPath));
			default:
				throw InvalidManifest::missingManifestException($path);
		}
	}

	/**
	 * Internal helper for getting all project paths for easy mocking in tests.
	 *
	 * @param string $type Type fo path to return.
	 * @param string $suffix Additional suffix path to add.
	 * @param string $prefix Additional prefix instead of dirname path.
	 *
	 * @return string
	 */
	public static function getProjectPaths(string $type = '', string $suffix = '', string $prefix = ''): string
	{
		$path = '';
		$internalPrefix = \dirname(__FILE__, 6);

		if (\getenv('ES_TEST')) {
			$internalPrefix = \dirname(__FILE__, 3);
		}

		$flibsPath = ["node_modules", "@eightshift", "frontend-libs", "blocks", "init"];
		$fPLibsPath = ["node_modules", "@eightshift", "frontend-libs-private", "blocks", "init"];
		$libsPath = ["vendor", "infinum", "eightshift-libs"];
		$libsPrefixedPath = ["vendor-prefixed", "infinum", "eightshift-libs"];
		$testsDataPath = ["tests", "data"];
		$srcPath = "src";
		$blocksPath = [$srcPath, "Blocks"];
		$assetsPath = "assets";
		$cliOutputPath = "cliOutput";

		$name = '';

		switch ($type) {
			case 'projectRoot':
				$internalPrefix = \dirname(__FILE__, 9);

				if (\getenv('ES_TEST')) {
					$internalPrefix = \dirname(__FILE__, 3);
				}
				break;
			case 'testsData':
				if (\getenv('ES_TEST')) {
					$internalPrefix = \dirname(__FILE__, 3);
					$path = self::joinPaths([...$testsDataPath]);
				}

				break;
			case 'srcDestination':
				$path = $srcPath;

				if (\getenv('ES_TEST')) {
					$internalPrefix = \dirname(__FILE__, 3);
					$path = self::joinPaths([$cliOutputPath, $srcPath]);
				}

				break;
			case 'cliOutput':
			case 'root':
			case 'themeRoot':
			case 'pluginRoot':
				if (\getenv('ES_TEST')) {
					$internalPrefix = \dirname(__FILE__, 3);
					$path = $cliOutputPath;
				}

				break;
			case 'wpContent':
				$internalPrefix = \dirname(__FILE__, 8);

				if (\getenv('ES_TEST')) {
					$internalPrefix = \dirname(__FILE__, 3);
				}
				break;
			case 'libs':
				$path = self::joinPaths($libsPath);

				if (\getenv('ES_TEST')) {
					$path = '';
				}
				break;
			case 'libsPrefixed':
				$path = self::joinPaths($libsPrefixedPath);

				if (\getenv('ES_TEST')) {
					$path = '';
				}
				break;
			case 'blocksGlobalAssetsSource':
				$path = self::joinPaths([...$flibsPath, $assetsPath]);

				if (\getenv('ES_TEST')) {
					$path = self::joinPaths([...$testsDataPath, $assetsPath]);
				}
				break;
			case 'blocksAssetsSource':
				$path = self::joinPaths([...$flibsPath, ...$blocksPath, $assetsPath]);

				if (\getenv('ES_TEST')) {
					$path = self::joinPaths([...$testsDataPath, ...$blocksPath, $assetsPath]);
				}
				break;
			case 'blocksSource':
				$path = self::joinPaths([...$flibsPath, ...$blocksPath]);

				if (\getenv('ES_TEST')) {
					$path = self::joinPaths([...$testsDataPath, ...$blocksPath]);
				}
				break;
			case 'blocksPrivateSource':
				$path = self::joinPaths([...$fPLibsPath, ...$blocksPath]);

				if (\getenv('ES_TEST')) {
					$path = self::joinPaths([...$testsDataPath, ...$blocksPath]);
				}
				break;
			case 'block':
			case 'blocks':
			case 'custom':
			case 'blocksDestinationCustom':
			case 'blocksSourceCustom':
			case 'blocksPrivateSourceCustom':
				$name = 'custom';
				break;
			case 'component':
			case 'components':
			case 'blocksDestinationComponents':
			case 'blocksSourceComponents':
			case 'blocksPrivateSourceComponents':
				$name = 'components';
				break;
			case 'variation':
			case 'variations':
			case 'blocksDestinationVariations':
			case 'blocksSourceVariations':
			case 'blocksPrivateSourceVariations':
				$name = 'variations';
				break;
			case 'wrapper':
			case 'blocksDestinationWrapper':
			case 'blocksSourceWrapper':
				$name = 'wrapper';
				break;

			case 'blocksGlobalAssetsDestination':
				$path = self::joinPaths([$assetsPath]);

				if (\getenv('ES_TEST')) {
					$path = self::joinPaths([$cliOutputPath, $assetsPath]);
				}
				break;
			case 'blocksAssetsDestination':
				$path = self::joinPaths([...$blocksPath, $assetsPath]);

				if (\getenv('ES_TEST')) {
					$path = self::joinPaths([$cliOutputPath, ...$blocksPath, $assetsPath]);
				}
				break;
			case 'blocksDestination':
				$path = self::joinPaths($blocksPath);

				if (\getenv('ES_TEST')) {
					$path = self::joinPaths([$cliOutputPath, ...$blocksPath]);
				}
				break;
		}

		switch ($type) {
			case 'blocksSourceCustom':
			case 'blocksSourceComponents':
			case 'blocksSourceVariations':
			case 'blocksSourceWrapper':
				$path = self::joinPaths([...$flibsPath, ...$blocksPath, $name]);

				if (\getenv('ES_TEST')) {
					$path = self::joinPaths([...$testsDataPath, ...$blocksPath, $name]);
				}
				break;

			case 'blocksPrivateSourceCustom':
			case 'blocksPrivateSourceComponents':
			case 'blocksPrivateSourceVariations':
				$path = self::joinPaths([...$fPLibsPath, ...$blocksPath, $name]);

				if (\getenv('ES_TEST')) {
					$path = self::joinPaths([...$testsDataPath, ...$blocksPath, $name]);
				}
				break;

			case 'block':
			case 'blocks':
			case 'custom':
			case 'component':
			case 'components':
			case 'variation':
			case 'variations':
			case 'wrapper':
			case 'blocksDestinationCustom':
			case 'blocksDestinationComponents':
			case 'blocksDestinationVariations':
			case 'blocksDestinationWrapper':
				$path = self::joinPaths([...$blocksPath, $name]);

				if (\getenv('ES_TEST')) {
					$path = self::joinPaths([$cliOutputPath, ...$blocksPath, $name]);
				}
				break;
		}

		if (!$prefix) {
			$prefix = $internalPrefix;
		}

		$path = self::joinPaths([$prefix, $path, $suffix]);

		return $path;
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

		$paths = \array_filter(
			\array_map(
				static function ($path) use ($sep) {
					return \trim($path, $sep);
				},
				$paths
			)
		);

		$path = \implode($sep, $paths);
		$path = "{$sep}{$path}";

		if (!\pathinfo($path, \PATHINFO_EXTENSION)) {
			$path = "{$path}{$sep}";
		}

		return $path;
	}
}
