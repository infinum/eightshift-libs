<?php

/**
 * Tests for StoreBlocksTrait helper methods.
 *
 * @package EightshiftLibs\Tests\Unit\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Helpers;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftLibs\Tests\BaseTestCase;
use EightshiftLibs\Helpers\Helpers;
use EightshiftLibs\Helpers\StoreBlocksTrait;
use EightshiftLibs\Exception\InvalidBlock;
use EightshiftLibs\Exception\InvalidManifest;
use ReflectionClass;

/**
 * Wrapper class to test StoreBlocksTrait methods.
 */
class StoreBlocksTraitWrapper
{
	use StoreBlocksTrait;
}

/**
 * Test case for StoreBlocksTrait utility methods.
 *
 * @coversDefaultClass EightshiftLibs\Helpers\StoreBlocksTrait
 */
class StoreBlocksTraitTest extends BaseTestCase
{
	private StoreBlocksTraitWrapper $wrapper;

	protected function setUp(): void
	{
		parent::setUp();
		Monkey\setUp();
		$this->wrapper = new StoreBlocksTraitWrapper();
		$this->clearStaticCache();

		Functions\when('esc_html__')->returnArg(1);
	}

	protected function tearDown(): void
	{
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Clear static cache properties.
	 */
	private function clearStaticCache(): void
	{
		$reflection = new ReflectionClass(StoreBlocksTraitWrapper::class);
		$stylesProperty = $reflection->getProperty('styles');
		$stylesProperty->setAccessible(true);
		$stylesProperty->setValue(null, []);
	}

	/**
	 * Set up Helpers cache with test data.
	 */
	private function setupHelpersCache(array $overrides = []): void
	{
		$defaults = [
			'blocks' => [
				'blocks' => [
					'button' => ['blockName' => 'button', 'attributes' => []],
					'card' => ['blockName' => 'card'],
				],
				'components' => [
					'heading' => ['componentName' => 'heading'],
					'paragraph' => ['componentName' => 'paragraph'],
				],
				'variations' => [
					'default' => ['variationName' => 'default'],
				],
				'wrapper' => ['componentName' => 'wrapper', 'attributes' => []],
				'settings' => [
					'config' => [
						'outputCssGlobally' => true,
						'outputCssOptimize' => false,
						'outputCssSelectorName' => 'es-css',
						'outputCssGloballyAdditionalStyles' => ['style1', 'style2'],
						'useWrapper' => true,
						'useLegacyComponents' => false,
					],
					'namespace' => 'eightshift-boilerplate',
					'globalVariables' => [
						'breakpoints' => ['sm' => 640, 'md' => 768, 'lg' => 1024],
						'colors' => ['primary' => '#000', 'secondary' => '#fff'],
					],
				],
			],
			'assets' => [
				'assets' => [
					'app.js' => '/dist/app.js',
					'style.css' => '/dist/style.css',
				],
			],
			'geolocation' => [
				'countries' => [
					['Code' => 'us', 'Name' => 'United States'],
					['Code' => 'de', 'Name' => 'Germany'],
				],
			],
		];

		$cache = \array_replace_recursive($defaults, $overrides);

		$reflection = new ReflectionClass(Helpers::class);
		$cacheProperty = $reflection->getProperty('cache');
		$cacheProperty->setAccessible(true);
		$cacheProperty->setValue(null, $cache);
	}

	/**
	 * Clear Helpers cache.
	 */
	private function clearHelpersCache(): void
	{
		$reflection = new ReflectionClass(Helpers::class);
		$cacheProperty = $reflection->getProperty('cache');
		$cacheProperty->setAccessible(true);
		$cacheProperty->setValue(null, []);
	}

	/**
	 * Test that methods exist and are callable.
	 * These tests verify the trait structure without requiring full Helpers integration.
	 */
	public function testGetBlocksMethodExists(): void
	{
		$this->assertTrue(\method_exists(StoreBlocksTraitWrapper::class, 'getBlocks'));
	}

	public function testGetComponentsMethodExists(): void
	{
		$this->assertTrue(\method_exists(StoreBlocksTraitWrapper::class, 'getComponents'));
	}

	public function testGetVariationsMethodExists(): void
	{
		$this->assertTrue(\method_exists(StoreBlocksTraitWrapper::class, 'getVariations'));
	}

	public function testGetWrapperMethodExists(): void
	{
		$this->assertTrue(\method_exists(StoreBlocksTraitWrapper::class, 'getWrapper'));
	}

	public function testGetConfigMethodExists(): void
	{
		$this->assertTrue(\method_exists(StoreBlocksTraitWrapper::class, 'getConfig'));
	}

	public function testGetSettingsMethodExists(): void
	{
		$this->assertTrue(\method_exists(StoreBlocksTraitWrapper::class, 'getSettings'));
	}

	public function testGetAssetMethodExists(): void
	{
		$this->assertTrue(\method_exists(StoreBlocksTraitWrapper::class, 'getAsset'));
	}

	public function testGetGeolocationCountriesMethodExists(): void
	{
		$this->assertTrue(\method_exists(StoreBlocksTraitWrapper::class, 'getGeolocationCountries'));
	}

	/**
	 * @covers ::getConfigOutputCssGlobally
	 */
	public function testGetConfigOutputCssGloballyMethodExists(): void
	{
		$this->assertTrue(\method_exists(StoreBlocksTraitWrapper::class, 'getConfigOutputCssGlobally'));
	}

	public function testGetConfigOutputCssOptimizeMethodExists(): void
	{
		$this->assertTrue(\method_exists(StoreBlocksTraitWrapper::class, 'getConfigOutputCssOptimize'));
	}

	public function testGetConfigOutputCssSelectorNameMethodExists(): void
	{
		$this->assertTrue(\method_exists(StoreBlocksTraitWrapper::class, 'getConfigOutputCssSelectorName'));
	}

	public function testGetConfigOutputCssGloballyAdditionalStylesMethodExists(): void
	{
		$this->assertTrue(\method_exists(StoreBlocksTraitWrapper::class, 'getConfigOutputCssGloballyAdditionalStyles'));
	}

	public function testGetConfigUseWrapperMethodExists(): void
	{
		$this->assertTrue(\method_exists(StoreBlocksTraitWrapper::class, 'getConfigUseWrapper'));
	}

	public function testGetConfigUseLegacyComponentsMethodExists(): void
	{
		$this->assertTrue(\method_exists(StoreBlocksTraitWrapper::class, 'getConfigUseLegacyComponents'));
	}

	/**
	 * @covers ::getSettingsNamespace
	 */
	public function testGetSettingsNamespaceMethodExists(): void
	{
		$this->assertTrue(\method_exists(StoreBlocksTraitWrapper::class, 'getSettingsNamespace'));
	}

	public function testGetSettingsGlobalVariablesMethodExists(): void
	{
		$this->assertTrue(\method_exists(StoreBlocksTraitWrapper::class, 'getSettingsGlobalVariables'));
	}

	public function testGetSettingsGlobalVariablesBreakpointsMethodExists(): void
	{
		$this->assertTrue(\method_exists(StoreBlocksTraitWrapper::class, 'getSettingsGlobalVariablesBreakpoints'));
	}

	public function testGetSettingsGlobalVariablesColorsMethodExists(): void
	{
		$this->assertTrue(\method_exists(StoreBlocksTraitWrapper::class, 'getSettingsGlobalVariablesColors'));
	}

	/**
	 * @covers ::getBlock
	 */
	public function testGetBlockThrowsExceptionForEmptyString(): void
	{
		$this->expectException(InvalidBlock::class);

		StoreBlocksTraitWrapper::getBlock('');
	}

	/**
	 * @covers ::getComponent
	 */
	public function testGetComponentThrowsExceptionForEmptyString(): void
	{
		$this->expectException(InvalidBlock::class);

		StoreBlocksTraitWrapper::getComponent('');
	}

	/**
	 * @covers ::getVariation
	 */
	public function testGetVariationThrowsExceptionForEmptyString(): void
	{
		$this->expectException(InvalidBlock::class);

		StoreBlocksTraitWrapper::getVariation('');
	}

	/**
	 * @covers ::getAsset
	 */
	public function testGetAssetThrowsExceptionForEmptyString(): void
	{
		$this->expectException(InvalidBlock::class);

		StoreBlocksTraitWrapper::getAsset('');
	}

	/**
	 * @covers ::setStyle
	 * @covers ::getStyles
	 */
	public function testSetAndGetStyles(): void
	{
		$style1 = ['selector' => '.test', 'css' => 'color: red;'];
		$style2 = ['selector' => '.another', 'css' => 'color: blue;'];

		StoreBlocksTraitWrapper::setStyle($style1);
		StoreBlocksTraitWrapper::setStyle($style2);

		$styles = StoreBlocksTraitWrapper::getStyles();

		$this->assertIsArray($styles);
		$this->assertCount(2, $styles);
		$this->assertSame($style1, $styles[0]);
		$this->assertSame($style2, $styles[1]);
	}

	/**
	 * @covers ::setStyle
	 */
	public function testSetStyleIgnoresEmptyArray(): void
	{
		StoreBlocksTraitWrapper::setStyle([]);

		$styles = StoreBlocksTraitWrapper::getStyles();
		$this->assertCount(0, $styles);
	}

	/**
	 * @covers ::getStyles
	 */
	public function testGetStylesReturnsEmptyArrayInitially(): void
	{
		$this->assertSame([], StoreBlocksTraitWrapper::getStyles());
	}

	/**
	 * @covers ::setStyle
	 * @covers ::getStyles
	 */
	public function testMultipleStylesAccumulateInOrder(): void
	{
		for ($i = 0; $i < 5; $i++) {
			StoreBlocksTraitWrapper::setStyle(['index' => $i]);
		}

		$styles = StoreBlocksTraitWrapper::getStyles();
		$this->assertCount(5, $styles);
		$this->assertSame(0, $styles[0]['index']);
		$this->assertSame(4, $styles[4]['index']);
	}

	/**
	 * @covers ::$styles
	 */
	public function testStylesPropertyIsPublicStaticArray(): void
	{
		$reflection = new ReflectionClass(StoreBlocksTraitWrapper::class);
		$property = $reflection->getProperty('styles');

		$this->assertTrue($property->isStatic());
		$this->assertTrue($property->isPublic());
		$this->assertIsArray($property->getValue());
	}

	/**
	 * @covers ::getBlocks
	 * @covers ::getCachedData
	 */
	public function testGetBlocksReturnsBlocksFromCache(): void
	{
		$this->setupHelpersCache();

		$blocks = StoreBlocksTraitWrapper::getBlocks();

		$this->assertIsArray($blocks);
		$this->assertArrayHasKey('button', $blocks);
		$this->assertArrayHasKey('card', $blocks);
		$this->assertEquals('button', $blocks['button']['blockName']);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::getBlocks
	 */
	public function testGetBlocksReturnsEmptyArrayWhenCacheEmpty(): void
	{
		$this->clearHelpersCache();

		$blocks = StoreBlocksTraitWrapper::getBlocks();

		$this->assertSame([], $blocks);
	}

	/**
	 * @covers ::getBlock
	 * @covers ::getCachedData
	 */
	public function testGetBlockReturnsBlockData(): void
	{
		$this->setupHelpersCache();

		$block = StoreBlocksTraitWrapper::getBlock('button');

		$this->assertIsArray($block);
		$this->assertEquals('button', $block['blockName']);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::getBlock
	 */
	public function testGetBlockThrowsForMissingBlock(): void
	{
		$this->setupHelpersCache();

		$this->expectException(InvalidBlock::class);

		try {
			StoreBlocksTraitWrapper::getBlock('nonexistent');
		} finally {
			$this->clearHelpersCache();
		}
	}

	/**
	 * @covers ::getComponents
	 * @covers ::getCachedData
	 */
	public function testGetComponentsReturnsComponentsFromCache(): void
	{
		$this->setupHelpersCache();

		$components = StoreBlocksTraitWrapper::getComponents();

		$this->assertIsArray($components);
		$this->assertArrayHasKey('heading', $components);
		$this->assertArrayHasKey('paragraph', $components);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::getComponent
	 * @covers ::getCachedData
	 */
	public function testGetComponentReturnsComponentData(): void
	{
		$this->setupHelpersCache();

		$component = StoreBlocksTraitWrapper::getComponent('heading');

		$this->assertIsArray($component);
		$this->assertEquals('heading', $component['componentName']);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::getComponent
	 */
	public function testGetComponentThrowsForMissingComponent(): void
	{
		$this->setupHelpersCache();

		$this->expectException(InvalidBlock::class);

		try {
			StoreBlocksTraitWrapper::getComponent('nonexistent');
		} finally {
			$this->clearHelpersCache();
		}
	}

	/**
	 * @covers ::getVariations
	 * @covers ::getCachedData
	 */
	public function testGetVariationsReturnsVariationsFromCache(): void
	{
		$this->setupHelpersCache();

		$variations = StoreBlocksTraitWrapper::getVariations();

		$this->assertIsArray($variations);
		$this->assertArrayHasKey('default', $variations);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::getVariation
	 * @covers ::getCachedData
	 */
	public function testGetVariationReturnsVariationData(): void
	{
		$this->setupHelpersCache();

		$variation = StoreBlocksTraitWrapper::getVariation('default');

		$this->assertIsArray($variation);
		$this->assertEquals('default', $variation['variationName']);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::getVariation
	 */
	public function testGetVariationThrowsForMissingVariation(): void
	{
		$this->setupHelpersCache();

		$this->expectException(InvalidBlock::class);

		try {
			StoreBlocksTraitWrapper::getVariation('nonexistent');
		} finally {
			$this->clearHelpersCache();
		}
	}

	/**
	 * @covers ::getWrapper
	 * @covers ::getCachedData
	 */
	public function testGetWrapperReturnsWrapperFromCache(): void
	{
		$this->setupHelpersCache();

		$wrapper = StoreBlocksTraitWrapper::getWrapper();

		$this->assertIsArray($wrapper);
		$this->assertEquals('wrapper', $wrapper['componentName']);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::getSettings
	 * @covers ::getCachedData
	 */
	public function testGetSettingsReturnsSettingsFromCache(): void
	{
		$this->setupHelpersCache();

		$settings = StoreBlocksTraitWrapper::getSettings();

		$this->assertIsArray($settings);
		$this->assertArrayHasKey('config', $settings);
		$this->assertArrayHasKey('namespace', $settings);
		$this->assertArrayHasKey('globalVariables', $settings);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::getSettings
	 */
	public function testGetSettingsThrowsWhenEmpty(): void
	{
		$this->clearHelpersCache();

		$this->expectException(InvalidBlock::class);

		StoreBlocksTraitWrapper::getSettings();
	}

	/**
	 * @covers ::getConfig
	 */
	public function testGetConfigReturnsConfigArray(): void
	{
		$this->setupHelpersCache();

		$config = StoreBlocksTraitWrapper::getConfig();

		$this->assertIsArray($config);
		// getConfig() has function-level static cache; just verify it returns an array.
		// The actual values depend on whether this is the first getConfig call in the run.

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::getConfigOutputCssGlobally
	 */
	public function testGetConfigOutputCssGloballyReturnsBool(): void
	{
		$this->setupHelpersCache();

		$result = StoreBlocksTraitWrapper::getConfigOutputCssGlobally();

		$this->assertIsBool($result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::getConfigOutputCssOptimize
	 */
	public function testGetConfigOutputCssOptimizeReturnsBool(): void
	{
		$this->setupHelpersCache();

		$result = StoreBlocksTraitWrapper::getConfigOutputCssOptimize();

		$this->assertIsBool($result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::getConfigOutputCssSelectorName
	 */
	public function testGetConfigOutputCssSelectorNameReturnsString(): void
	{
		$this->setupHelpersCache();

		$result = StoreBlocksTraitWrapper::getConfigOutputCssSelectorName();

		$this->assertIsString($result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::getConfigOutputCssGloballyAdditionalStyles
	 */
	public function testGetConfigOutputCssGloballyAdditionalStylesReturnsArray(): void
	{
		$this->setupHelpersCache();

		$result = StoreBlocksTraitWrapper::getConfigOutputCssGloballyAdditionalStyles();

		$this->assertIsArray($result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::getConfigUseWrapper
	 */
	public function testGetConfigUseWrapperReturnsBool(): void
	{
		$this->setupHelpersCache();

		$result = StoreBlocksTraitWrapper::getConfigUseWrapper();

		$this->assertIsBool($result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::getConfigUseLegacyComponents
	 */
	public function testGetConfigUseLegacyComponentsReturnsBool(): void
	{
		$this->setupHelpersCache();

		$result = StoreBlocksTraitWrapper::getConfigUseLegacyComponents();

		$this->assertIsBool($result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::getSettingsNamespace
	 */
	public function testGetSettingsNamespaceReturnsString(): void
	{
		$this->setupHelpersCache();

		$result = StoreBlocksTraitWrapper::getSettingsNamespace();

		$this->assertIsString($result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::getSettingsGlobalVariables
	 */
	public function testGetSettingsGlobalVariablesReturnsArray(): void
	{
		$this->setupHelpersCache();

		$result = StoreBlocksTraitWrapper::getSettingsGlobalVariables();

		$this->assertIsArray($result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::getSettingsGlobalVariablesBreakpoints
	 */
	public function testGetSettingsGlobalVariablesBreakpointsReturnsArray(): void
	{
		$this->setupHelpersCache();

		$result = StoreBlocksTraitWrapper::getSettingsGlobalVariablesBreakpoints();

		$this->assertIsArray($result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::getSettingsGlobalVariablesColors
	 */
	public function testGetSettingsGlobalVariablesColorsReturnsArray(): void
	{
		$this->setupHelpersCache();

		$result = StoreBlocksTraitWrapper::getSettingsGlobalVariablesColors();

		$this->assertIsArray($result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::getAsset
	 * @covers ::getCachedData
	 */
	public function testGetAssetReturnsAssetPath(): void
	{
		$this->setupHelpersCache();

		$result = StoreBlocksTraitWrapper::getAsset('app.js');

		$this->assertIsString($result);
		$this->assertEquals('/dist/app.js', $result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::getAsset
	 */
	public function testGetAssetThrowsForMissingAsset(): void
	{
		$this->setupHelpersCache();

		$this->expectException(InvalidBlock::class);

		try {
			StoreBlocksTraitWrapper::getAsset('nonexistent.js');
		} finally {
			$this->clearHelpersCache();
		}
	}

	/**
	 * @covers ::getGeolocationCountries
	 * @covers ::getCachedData
	 */
	public function testGetGeolocationCountriesReturnsCountries(): void
	{
		$this->setupHelpersCache();

		$result = StoreBlocksTraitWrapper::getGeolocationCountries();

		$this->assertIsArray($result);
		$this->assertCount(2, $result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::getGeolocationCountries
	 */
	public function testGetGeolocationCountriesThrowsWhenEmpty(): void
	{
		// Set cache with empty geolocation countries directly (can't use setupHelpersCache
		// because array_replace_recursive doesn't override non-empty with empty).
		$reflection = new ReflectionClass(Helpers::class);
		$cacheProperty = $reflection->getProperty('cache');
		$cacheProperty->setAccessible(true);
		$cacheProperty->setValue(null, [
			'geolocation' => ['countries' => []],
		]);

		$this->expectException(InvalidManifest::class);

		try {
			StoreBlocksTraitWrapper::getGeolocationCountries();
		} finally {
			$this->clearHelpersCache();
		}
	}

	/**
	 * @covers ::getSettingsNamespace
	 */
	public function testGetSettingsNamespaceCacheHit(): void
	{
		$this->setupHelpersCache();

		// First call populates the function-level static cache.
		$result1 = StoreBlocksTraitWrapper::getSettingsNamespace();
		// Second call returns from cache (line 298).
		$result2 = StoreBlocksTraitWrapper::getSettingsNamespace();

		$this->assertSame($result1, $result2);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::getSettingsGlobalVariablesBreakpoints
	 */
	public function testGetSettingsGlobalVariablesBreakpointsCacheHit(): void
	{
		$this->setupHelpersCache();

		$result1 = StoreBlocksTraitWrapper::getSettingsGlobalVariablesBreakpoints();
		$result2 = StoreBlocksTraitWrapper::getSettingsGlobalVariablesBreakpoints();

		$this->assertSame($result1, $result2);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::getSettingsGlobalVariablesColors
	 */
	public function testGetSettingsGlobalVariablesColorsCacheHit(): void
	{
		$this->setupHelpersCache();

		$result1 = StoreBlocksTraitWrapper::getSettingsGlobalVariablesColors();
		$result2 = StoreBlocksTraitWrapper::getSettingsGlobalVariablesColors();

		$this->assertSame($result1, $result2);

		$this->clearHelpersCache();
	}
}
