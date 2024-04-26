<?php

/**
 * Helpers that are deprecated and will be removed in the next major release.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

use EightshiftLibs\Exception\InvalidPath;

/**
 * Class DeprecatedTrait Helper.
 */
trait DeprecatedTrait
{
	/**
	 * Render component/block partial.
	 *
	 * @param string $type Type of content block, component, variable, etc.
	 * @param string $parent Parent block/component name.
	 * @param string $name Name of the partial. It can be without extension so .php is used.
	 * @param array<string, mixed> $attributes Attributes that will be passed to partial.
	 * @param string $partialFolderName Partial folder name.
	 *
	 * @throws InvalidPath If the file is missing.
	 *
	 * @deprecated 8.0.0 Use Components::render() instead. This method will be removed in the next major release.
	 *
	 * @return string Partial html.
	 */
	public static function renderPartial(
		string $type,
		string $parent,
		string $name,
		array $attributes = [],
		string $partialFolderName = 'partials'
	): string {
		$sep = \DIRECTORY_SEPARATOR;

		// If no extension is provided use php.
		if (\strpos($name, '.php') === false) {
			$name = "{$name}.php";
		}

		$partialPath = "{$parent}{$sep}{$partialFolderName}{$sep}{$name}";

		// Detect folder based on the name.
		switch ($type) {
			case 'block':
			case 'blocks':
			case 'custom':
				$path = Helpers::getProjectPaths('blocksDestinationCustom', $partialPath);
				break;
			case 'component':
			case 'components':
				$path = Helpers::getProjectPaths('blocksDestinationComponents', $partialPath);
				break;
			case 'variation':
			case 'variations':
				$path = Helpers::getProjectPaths('blocksDestinationVariations', $partialPath);
				break;
			case 'wrapper':
				$path = Helpers::getProjectPaths('blocksDestinationWrapper', $partialPath);
				break;
			default:
				$path = Helpers::getProjectPaths('root', $partialPath);
				break;
		}

		// Bailout if file is missing.
		if (!\file_exists($path)) {
			throw InvalidPath::missingFileException($path);
		}

		\ob_start();

		require $path;

		return \trim((string) \ob_get_clean());
	}

	/**
	 * Get manifest json by path and name (old method).
	 *
	 * @param string $path Absolute path.
	 *
	 * @deprecated 8.0.0 Use Components::getManifestByDir() instead. This method will be removed in the next major release.
	 *
	 * @return array<string, mixed>
	 */
	public static function getManifest(string $path): array
	{
		return self::getManifestByDir($path);
	}
}
