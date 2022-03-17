<?php

/**
 * File containing invalid Gutenberg Block exceptions
 *
 * @package EightshiftLibs\Exception
 */

declare(strict_types=1);

namespace EightshiftLibs\Exception;

use InvalidArgumentException;

/**
 * Class InvalidBlock
 */
final class InvalidBlock extends InvalidArgumentException implements GeneralExceptionInterface
{
	/**
	 * Throws error if blocks are missing.
	 *
	 * @return static
	 */
	public static function missingBlocksException(): InvalidBlock
	{
		return new InvalidBlock(\esc_html__('There are no blocks added in your project.', 'eightshift-libs'));
	}

	/**
	 * Throws error if components are missing.
	 *
	 * @return static
	 */
	public static function missingComponentsException(): InvalidBlock
	{
		return new InvalidBlock(\esc_html__('There are no components added in your project.', 'eightshift-libs'));
	}

	/**
	 * Throws error if manifest key blockName is missing.
	 *
	 * @param string $blockPath Full block path for the missing name.
	 *
	 * @return static
	 */
	public static function missingNameException(string $blockPath): InvalidBlock
	{
		return new InvalidBlock(
			\sprintf(
			/* translators: %s will be replaced with the path where the block should be. */
				\esc_html__('Block in this path %s is missing blockName key in its manifest.json.', 'eightshift-libs'),
				$blockPath
			)
		);
	}

	/**
	 * Throws error if manifest key componentName is missing.
	 *
	 * @param string $componmentPath Full component path for the missing name.
	 *
	 * @return static
	 */
	public static function missingComponentNameException(string $componmentPath): InvalidBlock
	{
		return new InvalidBlock(
			\sprintf(
			/* translators: %s will be replaced with the path where the block should be. */
				\esc_html__('Component at %s is missing the "componentName" key in its manifest.json.', 'eightshift-libs'),
				$componmentPath
			)
		);
	}

	/**
	 * Throws error if block view is missing.
	 *
	 * @param string $blockName Block name for the missing view.
	 * @param string $blockPath Full block path for the missing name.
	 *
	 * @return static
	 */
	public static function missingViewException(string $blockName, string $blockPath): InvalidBlock
	{
		return new InvalidBlock(
			\sprintf(
			/* translators: %1$s is going to be replaced with the template name, %2$s with the template path. */
				\esc_html__(
					'Block with this name %1$s is missing view template. Template name should be called %1$s.php, and it should be located in this path %2$s',
					'eightshift-libs'
				),
				$blockName,
				$blockPath
			)
		);
	}

	/**
	 * Throws error if render block view is missing.
	 *
	 * @param string $blockPath Full block path for the missing name.
	 *
	 * @return static
	 */
	public static function missingRenderViewException(string $blockPath): InvalidBlock
	{
		return new InvalidBlock(
			\sprintf(
			/* translators: %s will be replaced with the block path. */
				\esc_html__(
					'Block view is missing in the provided path. Please check if %s is the right path for your block view.',
					'eightshift-libs'
				),
				$blockPath
			)
		);
	}

	/**
	 * Throws error if global settings manifest.json is missing.
	 *
	 * @param string $settingsManifestPath Full path for the missing manifest.json.
	 *
	 * @return static
	 */
	public static function missingSettingsManifestException(string $settingsManifestPath): InvalidBlock
	{
		return new InvalidBlock(
			\sprintf(
			/* translators: %s will be replaced with the location of the manifest for the block. */
				\esc_html__('Global blocks settings manifest.json is missing on this location: %s.', 'eightshift-libs'),
				$settingsManifestPath
			)
		);
	}

	/**
	 * Throws error if wrapper settings manifest.json is missing.
	 *
	 * @param string $settingsManifestPath Full path for the missing manifest.json.
	 *
	 * @return static
	 */
	public static function missingWrapperManifestException(string $settingsManifestPath): InvalidBlock
	{
		return new InvalidBlock(
			\sprintf(
			/* translators: %s will be replaced with the manifest path location. */
				\esc_html__('Wrapper blocks settings manifest.json is missing on this location: %s.', 'eightshift-libs'),
				$settingsManifestPath
			)
		);
	}

	/**
	 * Throws error if component manifest.json is missing.
	 *
	 * @param string $settingsManifestPath Full path for the missing manifest.json.
	 *
	 * @return static
	 */
	public static function missingComponentManifestException(string $settingsManifestPath): InvalidBlock
	{
		return new InvalidBlock(
			\sprintf(
			/* translators: %s will be replaced with the manifest path location. */
				\esc_html__('Component manifest.json is missing on this location: %s.', 'eightshift-libs'),
				$settingsManifestPath
			)
		);
	}

	/**
	 * Throws error if block wrapper view is missing.
	 *
	 * @param string $wrapperPath Full wrapper path.
	 *
	 * @return static
	 */
	public static function missingWrapperViewException(string $wrapperPath): InvalidBlock
	{
		return new InvalidBlock(
			\sprintf(
			/* translators: %s will be replaced with the view template path location. */
				\esc_html__('Wrapper view is missing. Template should be located in this path %s', 'eightshift-libs'),
				$wrapperPath
			)
		);
	}

	/**
	 * Throws error if global manifest settings key namespace is missing.
	 *
	 * @return static
	 */
	public static function missingNamespaceException(): InvalidBlock
	{
		return new InvalidBlock(
			\esc_html__(
				'Global Blocks settings manifest.json is missing a key called namespace. This key prefixes all block names.',
				'eightshift-libs'
			)
		);
	}

	/**
	 * Throws error if global manifest settings key is missing.
	 *
	 * @param string $name Block/component name.
	 * @param string $componentName Component name to check.
	 *
	 * @return static
	 */
	public static function wrongComponentNameException(string $name, string $componentName): InvalidBlock
	{
		return new InvalidBlock(
			\sprintf(
				/* translators: %1$s is going to be replaced with the component/block name, %2$s with component name. */
				\esc_html__('Component specified in %1$s manifest doesn\'t exist in your components list.
				Please check if you project has %2$s component.', 'eightshift-libs'),
				$name,
				$componentName
			)
		);
	}

	/**
	 * Throws error if key is not found in the block settings array.
	 *
	 * @param string $key Key to find.
	 * @param string $item Array item to find.
	 *
	 * @return static
	 */
	public static function missingSettingsKeyException(string $key, string $item = ''): InvalidBlock
	{

		if ($key === 'block' || $key === 'component') {
			return new InvalidBlock(
				\sprintf(
					/* translators: %1$s is going to be replaced with the key name. */
					\esc_html__('Block/component %1$s not found in the blocks settings or the output data is empty.
					Please check if the provided key and parent is correct.', 'eightshift-libs'),
					$item
				)
			);
		}

		if ($item) {
			return new InvalidBlock(
				\sprintf(
					/* translators: %1$s is going to be replaced with the key name. */
					\esc_html__('Key %1$s not found in the %2$s array blocks settings or the output data is empty.
					Please check if the provided key and parent is correct.', 'eightshift-libs'),
					$item,
					$key
				)
			);
		}

		return new InvalidBlock(
			\sprintf(
				/* translators: %1$s is going to be replaced with the key name. */
				\esc_html__('Key %1$s not found in the blocks settings or the output data is empty.
				Please check if the provided key is correct.', 'eightshift-libs'),
				$key,
			)
		);
	}

	/**
	 * Throws error if the block file is missing.
	 *
	 * @param string $sourcePath Missing file path.
	 *
	 * @return static
	 */
	public static function missingFileException(string $sourcePath): InvalidBlock
	{
		return new InvalidBlock(
			\sprintf(
				/* translators: %s is going to be replaced with the missing file path. */
				\esc_html__('Failed to open path %s. No such file or directory.', 'eightshift-libs'),
				$sourcePath,
			)
		);
	}

	/**
	 * Throws error if the block global details array is missing.
	 *
	 * @return static
	 */
	public static function missingGlobalBlockDetailsException(): InvalidBlock
	{
		return new InvalidBlock(
			\esc_html__('
				Global variable $esBlocks is missing. Did you hook your callbacks correctly?
				Make sure the global is generated early on in your request lifecycle.
			', 'eightshift-libs'),
		);
	}
}
