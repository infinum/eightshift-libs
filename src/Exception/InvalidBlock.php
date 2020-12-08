<?php

/**
 * File containing invalid Gutenberg Block exceptions
 *
 * @package EightshiftLibs\Exception
 */

declare(strict_types=1);

namespace EightshiftLibs\Exception;

/**
 * Class Invalid_Block.
 */
final class InvalidBlock extends \InvalidArgumentException implements GeneralExceptionInterface
{

	/**
	 * Throws error if blocks are missing.
	 *
	 * @return static
	 */
	public static function missingBlocksException()
	{
		return new static(esc_html__('There are no blocks added in your project.', 'eightshift-libs'));
	}

	/**
	 * Throws error if manifest key blockName is missing.
	 *
	 * @param string $blockPath Full block path for the missing name.
	 *
	 * @return static
	 */
	public static function missingNameException(string $blockPath)
	{
		return new static(
			sprintf(
			/* translators: %s will be replaced with the path where the block should be. */
				esc_html__('Block in this path %s is missing blockName key in its manifest.json.', 'eightshift-libs'),
				$blockPath
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
	public static function missingViewException(string $blockName, string $blockPath)
	{
		return new static(
			sprintf(
			/* translators: %1$s is going to be replaced with the template name, %2$s with the template path. */
				esc_html__(
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
	public static function missingRenderViewException(string $blockPath)
	{
		return new static(
			sprintf(
			/* translators: %s will be replaced with the block path. */
				esc_html__(
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
	public static function missingSettingsManifestException(string $settingsManifestPath)
	{
		return new static(
			sprintf(
			/* translators: %s will be replaced with the location of the manifest for the block. */
				esc_html__('Global blocks settings manifest.json is missing on this location: %s.', 'eightshift-libs'),
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
	public static function missingWrapperManifestException(string $settingsManifestPath)
	{
		return new static(
			sprintf(
			/* translators: %s will be replaced with the manifest path location. */
				esc_html__('Wrapper blocks settings manifest.json is missing on this location: %s.', 'eightshift-libs'),
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
	public static function missingComponentManifestException(string $settingsManifestPath)
	{
		return new static(
			sprintf(
			/* translators: %s will be replaced with the manifest path location. */
				esc_html__('Component manifest.json is missing on this location: %s.', 'eightshift-libs'),
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
	public static function missingWrapperViewException(string $wrapperPath)
	{
		return new static(
			sprintf(
			/* translators: %s will be replaced with the view template path location. */
				esc_html__('Wrapper view is missing. Template should be located in this path %s', 'eightshift-libs'),
				$wrapperPath
			)
		);
	}

	/**
	 * Throws error if global manifest settings key namespace is missing.
	 *
	 * @return static
	 */
	public static function missingNamespaceException()
	{
		return new static(
			esc_html__(
				'Global Blocks settings manifest.json is missing a key called namespace. This key prefixes all block names.',
				'eightshift-libs'
			)
		);
	}
}
