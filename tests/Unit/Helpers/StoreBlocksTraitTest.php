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
use EightshiftLibs\Helpers\StoreBlocksTrait;
use EightshiftLibs\Exception\InvalidBlock;
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
}
