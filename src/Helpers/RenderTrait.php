<?php

/**
 * Helpers for rendering components and blocks.
 *
 * @package EightshiftLibs\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Helpers;

use EightshiftLibs\Exception\InvalidPath;
use WP_Block;

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
	 * Cached default render path name (resolved from legacy-components config).
	 *
	 * @var string|null
	 */
	private static ?string $defaultPathName = null;

	/**
	 * Cache of resolved render paths already verified to exist on disk.
	 *
	 * @var array<string, true>
	 */
	private static array $fileExistsCache = [];

	/**
	 * Handle components render logic.
	 *
	 * @param string $renderName Render name.
	 * @param string $renderPrefixPath Prefix path.
	 * @param string $componentName Component name.
	 *
	 * @return array{path: string, manifest: array<string, mixed>}
	 */
	private static function handleComponentsRender(string $renderName, string $renderPrefixPath, string $componentName): array
	{
		if ($componentName !== '' && $componentName !== '0') {
			return [
				'path' => Helpers::getProjectPaths('components', [$renderPrefixPath, "{$renderName}.php"]),
				'manifest' => Helpers::getComponent($componentName)
			];
		}

		return [
			'path' => Helpers::getProjectPaths('components', [$renderPrefixPath, $renderName, "{$renderName}.php"]),
			'manifest' => Helpers::getComponent($renderName)
		];
	}

	/**
	 * Handle wrapper render logic.
	 *
	 * @param string $renderName Render name.
	 *
	 * @return array{path: string, manifest: array<string, mixed>}
	 */
	private static function handleWrapperRender(string $renderName): array
	{
		return [
			'path' => Helpers::getProjectPaths('wrapper', ["{$renderName}.php"]),
			'manifest' => Helpers::getWrapper()
		];
	}

	/**
	 * Handle blocks render logic.
	 *
	 * @param string $renderName Render name.
	 * @param string $renderPrefixPath Prefix path.
	 * @param string $componentName Component name.
	 *
	 * @return array{path: string, manifest: array<string, mixed>}
	 */
	private static function handleBlocksRender(string $renderName, string $renderPrefixPath, string $componentName): array
	{
		if ($componentName !== '' && $componentName !== '0') {
			return [
				'path' => Helpers::getProjectPaths('blocks', [$renderPrefixPath, "{$renderName}.php"]),
				'manifest' => Helpers::getBlock($componentName)
			];
		}

		return [
			'path' => Helpers::getProjectPaths('blocks', [$renderPrefixPath, $renderName, "{$renderName}.php"]),
			'manifest' => Helpers::getBlock($renderName)
		];
	}

	/**
	 * Recursively clean inner blocks data to only include necessary properties.
	 *
	 * @param array<int, WP_Block> $innerBlocks The inner blocks data to clean.
	 *
	 * @return array<int, array{name: string, attributes: array<string, mixed>, innerBlocks: array<int, array>}> Cleaned inner blocks data.
	 */
	private static function cleanInnerBlocks(array $innerBlocks): array // @phpstan-ignore-line
	{
		return \array_map(static fn(WP_Block $blockData): array => [
				'name' => $blockData->name,
				'attributes' => $blockData->attributes,
				// phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
				// @phpstan-ignore nullCoalesce.property
				'innerBlocks' => self::cleanInnerBlocks([...($blockData->inner_blocks ?? [])]), // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
			], $innerBlocks);
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
	 * @param WP_Block|null $renderBlock The current WP_Block instance, available as $block in the template.
	 *
	 * @throws InvalidPath If the file is missing.
	 */
	public static function render(
		string $renderName,
		array $renderAttributes = [],
		string $renderPathName = '',
		bool $renderUseComponentDefaults = false,
		string $renderPrefixPath = '',
		string $renderContent = '',
		?WP_Block $renderBlock = null
	): string {
		if (self::$allowedNamesFlipped === null) {
			self::$allowedNamesFlipped = \array_flip(self::PROJECT_RENDER_ALLOWED_NAMES);
		}

		Helpers::initializePathCaches();

		if ($renderPathName === '' || $renderPathName === '0') {
			if (self::$defaultPathName === null) {
				self::$defaultPathName = Helpers::getConfigUseLegacyComponents() ? 'components' : 'blocks';
			}
			$renderPathName = self::$defaultPathName;
		}

		if (!isset(self::$allowedNamesFlipped[$renderPathName])) {
			throw InvalidPath::wrongOrNotAllowedParentPathException($renderPathName, \implode(', ', self::PROJECT_RENDER_ALLOWED_NAMES));
		}

		$componentName = '';
		if ($renderPrefixPath && ($renderPathName === 'components' || $renderPathName === 'blocks')) {
			$separatorPos = \strpos($renderPrefixPath, \DIRECTORY_SEPARATOR);
			$componentName = $separatorPos !== false ? \substr($renderPrefixPath, 0, $separatorPos) : $renderPrefixPath;
		}

		$result = match ($renderPathName) {
			'components' => self::handleComponentsRender($renderName, $renderPrefixPath, $componentName),
			'wrapper' => self::handleWrapperRender($renderName),
			'blocks' => self::handleBlocksRender($renderName, $renderPrefixPath, $componentName),
			default => [
				'path' => Helpers::getProjectPaths('', [$renderPathName, $renderPrefixPath, "{$renderName}.php"]),
				'manifest' => [],
			],
		};

					$renderPath = $result['path'];
					$manifest = $result['manifest'];

		if (!isset(self::$fileExistsCache[$renderPath])) {
			if (!\file_exists($renderPath)) {
				throw InvalidPath::missingFileException($renderPath);
			}
			self::$fileExistsCache[$renderPath] = true;
		}

		if ($renderUseComponentDefaults && !empty($manifest)) {
			$renderAttributes = Helpers::getDefaultRenderAttributes($manifest, $renderAttributes);
		}

					\ob_start();

					$attributes = $renderAttributes;
					$globalManifest = Helpers::getSettings();

					$innerBlockData = null;

		if ($renderPathName === 'blocks') {
            // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps
			// @phpstan-ignore nullCoalesce.property
			$innerBlockData = [...($renderBlock->inner_blocks ?? [])]; // phpcs:ignore Squiz.NamingConventions.ValidVariableName.MemberNotCamelCaps

			if ($innerBlockData !== []) {
				$innerBlockData = self::cleanInnerBlocks($innerBlockData);
			}
		}

		// Strip internal variables so only the intentional set leaks into the included template scope.
					unset(
						$renderName,
						$renderAttributes,
						$renderPathName,
						$renderUseComponentDefaults,
						$renderPrefixPath,
						$componentName,
						$renderBlock,
						$separatorPos,
						$result
					);

		include $renderPath;

		return \trim((string) \ob_get_clean());
	}
}
