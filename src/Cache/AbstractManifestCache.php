<?php

/**
 * File containing an abstract class for holding Manifest Cache functionality.
 *
 * It is used to provide manifest.json file location stored in the transient cache.
 *
 * @package EightshiftLibs\Cache
 */

declare(strict_types=1);

namespace EightshiftLibs\Cache;

use EightshiftLibs\Helpers\Helpers;

/**
 * Abstract class AbstractManifestCache class.
 */
abstract class AbstractManifestCache implements ManifestCacheInterface
{
	/**
	 * Transient name prefix.
	 *
	 * @var string
	 */
	public const TRANSIENT_PREFIX_NAME = 'eightshift_manifest_cache';

	// Cache keys.
	public const VERSION_KEY = 'version';
	public const BLOCKS_KEY = 'blocks';
	public const COMPONENTS_KEY = 'components';
	public const VARIATIONS_KEY = 'variations';
	public const WRAPPER_KEY = 'wrapper';
	public const SETTINGS_KEY = 'settings';
	public const ASSETS_KEY = 'assets';
	public const COUNTRIES_KEY = 'countries';


	// Cache types.
	public const TYPE_BLOCKS = 'blocks';
	public const TYPE_ASSETS = 'assets';
	public const TYPE_GEOLOCATION = 'geolocation';

	/**
	 * Get cache name.
	 *
	 * @return string Cache name.
	 */
	abstract public function getCacheName(): string;

	/**
	 * Get cache version.
	 *
	 * @return string Cache version.
	 */
	abstract public function getVersion(): string;

	/**
	 * Set all cache.
	 *
	 * @return void
	 */
	public function setAllCache(): void
	{
		Helpers::setCacheDetails(
			$this->getCacheBuilder(),
			$this->getCacheName(),
			$this->getVersion(),
		);
	}

	/**
	 * Get cache for geolocation
	 *
	 * @return bool
	 */
	public function useGeolocation(): bool
	{
		return false;
	}

	/**
	 * Get cache builder.
	 *
	 * @return array<string, array<mixed>> Array of cache builder.
	 */
	protected function getCacheBuilder(): array
	{
		$output = [
			self::TYPE_BLOCKS => [
				self::SETTINGS_KEY => [
					'path' => 'blocksRoot',
					'multiple' => false,
					'validation' => [
						'$schema',
						'namespace',
						'background',
						'foreground',
					],
					'autoset' => [
						[
							'key' => 'outputCssGlobally',
							'value' => true,
							'parent' => 'config',
						],
						[
							'key' => 'useLegacyComponents',
							'value' => true,
							'parent' => 'config',
						],
						[
							'key' => 'outputCssOptimize',
							'value' => true,
							'parent' => 'config',
						],
						[
							'key' => 'useWrapper',
							'value' => true,
							'parent' => 'config',
						],
						[
							'key' => 'outputCssSelectorName',
							'value' => 'esCssVariables',
							'parent' => 'config',
						],
						[
							'key' => 'outputCssGloballyAdditionalStyles',
							'value' => [],
							'parent' => 'config',
						],
					],
				],
				self::BLOCKS_KEY => [
					'path' => 'blocks',
					'id' => 'blockName',
					'multiple' => true,
					'autoset' => [
						[
							'key' => 'classes',
							'value' => [],
						],
						[
							'key' => 'attributes',
							'value' => [],
						],
						[
							'key' => 'hasInnerBlocks',
							'value' => false,
						],
					],
					'validation' => [
						'$schema',
						'title',
						'description',
						'namespace',
						'blockName',
						'blockFullName',
						'keywords',
						'icon',
						'category',
					],
				],
				self::COMPONENTS_KEY => [
					'path' => 'components',
					'multiple' => true,
					'id' => 'componentName',
					'validation' => [
						'$schema',
						'title',
						'componentName',
					],
				],
				self::VARIATIONS_KEY => [
					'path' => 'variations',
					'id' => 'name',
					'multiple' => true,
					'validation' => [
						'$schema',
						'title',
						'description',
						'icon',
						'name',
						'parentName',
					],
				],
				self::WRAPPER_KEY => [
					'path' => 'wrapper',
					'validation' => [
						'$schema',
						'title',
					],
				],
			],
			self::TYPE_ASSETS => [
				self::ASSETS_KEY => [
					'path' => 'public',
				],
			],
		];

		if ($this->useGeolocation()) {
			$output[self::TYPE_GEOLOCATION] = [
				self::COUNTRIES_KEY => [
					'path' => 'libsPrefixedGeolocation',
				],
			];
		}

		return $output;
	}
}
