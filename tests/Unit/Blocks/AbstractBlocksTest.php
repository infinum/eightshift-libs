<?php

/**
 * Tests for AbstractBlocks.
 *
 * @package EightshiftLibs\Tests\Unit\Blocks
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Blocks;

use EightshiftLibs\Blocks\AbstractBlocks;
use EightshiftLibs\Tests\BaseTestCase;
use Brain\Monkey\Functions;

/**
 * Concrete implementation for testing abstract class.
 */
class ConcreteBlocks extends AbstractBlocks
{
	public function register(): void
	{
		// No registration logic needed for tests
	}
}

/**
 * Test case for AbstractBlocks.
 *
 * These tests verify that the AbstractBlocks class has the expected methods
 * and basic structure. Full integration tests with Helpers dependencies would
 * require more complex mocking or integration test setup.
 *
 * @coversDefaultClass EightshiftLibs\Blocks\AbstractBlocks
 */
class AbstractBlocksTest extends BaseTestCase
{
	private ConcreteBlocks $blocks;

	protected function setUp(): void
	{
		parent::setUp();
		$this->blocks = new ConcreteBlocks();

		// Mock WordPress functions to prevent errors
		Functions\when('add_theme_support')->justReturn(true);
		Functions\when('esc_html__')->returnArg(1);
		Functions\when('register_block_type')->justReturn(true);
	}

	/**
	 * Test that the concrete implementation is instantiable.
	 */
	public function testConcreteBlocksIsInstantiable(): void
	{
		$this->assertInstanceOf(AbstractBlocks::class, $this->blocks);
		$this->assertInstanceOf(ConcreteBlocks::class, $this->blocks);
	}

	/**
	 * Test that changeEditorColorPalette method exists.
	 */
	public function testChangeEditorColorPaletteMethodExists(): void
	{
		$this->assertTrue(method_exists($this->blocks, 'changeEditorColorPalette'));
	}

	/**
	 * Test that addThemeSupport method exists.
	 */
	public function testAddThemeSupportMethodExists(): void
	{
		$this->assertTrue(method_exists($this->blocks, 'addThemeSupport'));
	}

	/**
	 * Test that getAllAllowedBlocksList method exists.
	 */
	public function testGetAllAllowedBlocksListMethodExists(): void
	{
		$this->assertTrue(method_exists($this->blocks, 'getAllAllowedBlocksList'));
	}

	/**
	 * Test that getAllBlocksList method exists.
	 */
	public function testGetAllBlocksListMethodExists(): void
	{
		$this->assertTrue(method_exists($this->blocks, 'getAllBlocksList'));
	}

	/**
	 * Test that registerBlocks method exists.
	 */
	public function testRegisterBlocksMethodExists(): void
	{
		$this->assertTrue(method_exists($this->blocks, 'registerBlocks'));
	}

	/**
	 * Test that render method exists.
	 */
	public function testRenderMethodExists(): void
	{
		$this->assertTrue(method_exists($this->blocks, 'render'));
	}

	/**
	 * Test that getCustomCategory method exists.
	 */
	public function testGetCustomCategoryMethodExists(): void
	{
		$this->assertTrue(method_exists($this->blocks, 'getCustomCategory'));
	}

	/**
	 * Test that filterBlocksContent method exists.
	 */
	public function testFilterBlocksContentMethodExists(): void
	{
		$this->assertTrue(method_exists($this->blocks, 'filterBlocksContent'));
	}

	/**
	 * Test that outputCssVariablesInline method exists.
	 */
	public function testOutputCssVariablesInlineMethodExists(): void
	{
		$this->assertTrue(method_exists($this->blocks, 'outputCssVariablesInline'));
	}

	/**
	 * Test that outputCssVariablesGlobal method exists.
	 */
	public function testOutputCssVariablesGlobalMethodExists(): void
	{
		$this->assertTrue(method_exists($this->blocks, 'outputCssVariablesGlobal'));
	}

	/**
	 * Test that the class implements ServiceInterface.
	 */
	public function testImplementsServiceInterface(): void
	{
		$this->assertInstanceOf(\EightshiftLibs\Services\ServiceInterface::class, $this->blocks);
	}

	/**
	 * Test	 that the class implements RenderableBlockInterface.
	 */
	public function testImplementsRenderableBlockInterface(): void
	{
		$this->assertInstanceOf(\EightshiftLibs\Blocks\RenderableBlockInterface::class, $this->blocks);
	}
}
