<?php

/**
 * Helpers that are deprecated and will be removed in the next major release.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

use EightshiftLibs\Exception\InvalidManifest;

/**
 * Class DeprecatedTrait Helper.
 */
trait DeprecatedTrait
{
	/**
	 * Get manifest json by path and name.
	 *
	 * @param string $path Absolute path to.
	 *
	 * @throws InvalidManifest If the manifest is not allowed.
	 *
	 * @deprecated 10.0.0 This method is deprecated and will be removed in the next major release. Every component and block has $manifest variable available by default.
	 *
	 * @return array<string, mixed>
	 */
	public static function getManifestByDir(string $path): array
	{
		$sep = \DIRECTORY_SEPARATOR;
		$root = Helpers::getProjectPaths('src');
		$newPath = \str_replace($root, '', $path);
		$newPath = \array_filter(\explode($sep, $newPath));

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
}
