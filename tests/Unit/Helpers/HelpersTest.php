<?php

/**
 * Tests for Helpers class.
 *
 * @package EightshiftLibs\Tests\Unit\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Helpers;

use EightshiftLibs\Tests\BaseTestCase;
use EightshiftLibs\Helpers\Helpers;

/**
 * Test case for Helpers class.
 *
 * This tests that the Helpers class properly aggregates all traits
 * and provides access to their methods.
 *
 * @coversDefaultClass EightshiftLibs\Helpers\Helpers
 */
class HelpersTest extends BaseTestCase
{
	protected function setUp(): void
	{
		parent::setUp();
	}

	/**
	 * @covers ::__construct
	 */
	public function testHelpersClassExists(): void
	{
		$this->assertTrue(\class_exists(Helpers::class));
	}

	/**
	 * Test that Helpers uses CacheTrait.
	 */
	public function testHelpersUsesCacheTrait(): void
	{
		$traits = \class_uses(Helpers::class);
		$this->assertArrayHasKey('EightshiftLibs\Helpers\CacheTrait', $traits);
	}

	/**
	 * Test that Helpers uses StoreBlocksTrait.
	 */
	public function testHelpersUsesStoreBlocksTrait(): void
	{
		$traits = \class_uses(Helpers::class);
		$this->assertArrayHasKey('EightshiftLibs\Helpers\StoreBlocksTrait', $traits);
	}

	/**
	 * Test that Helpers uses CssVariablesTrait.
	 */
	public function testHelpersUsesCssVariablesTrait(): void
	{
		$traits = \class_uses(Helpers::class);
		$this->assertArrayHasKey('EightshiftLibs\Helpers\CssVariablesTrait', $traits);
	}

	/**
	 * Test that Helpers uses RenderTrait.
	 */
	public function testHelpersUsesRenderTrait(): void
	{
		$traits = \class_uses(Helpers::class);
		$this->assertArrayHasKey('EightshiftLibs\Helpers\RenderTrait', $traits);
	}

	/**
	 * Test that Helpers uses PathsTrait.
	 */
	public function testHelpersUsesPathsTrait(): void
	{
		$traits = \class_uses(Helpers::class);
		$this->assertArrayHasKey('EightshiftLibs\Helpers\PathsTrait', $traits);
	}

	/**
	 * Test that Helpers uses SelectorsTrait.
	 */
	public function testHelpersUsesSelectorsTrait(): void
	{
		$traits = \class_uses(Helpers::class);
		$this->assertArrayHasKey('EightshiftLibs\Helpers\SelectorsTrait', $traits);
	}

	/**
	 * Test that Helpers uses AttributesTrait.
	 */
	public function testHelpersUsesAttributesTrait(): void
	{
		$traits = \class_uses(Helpers::class);
		$this->assertArrayHasKey('EightshiftLibs\Helpers\AttributesTrait', $traits);
	}

	/**
	 * Test that Helpers uses GeneralTrait.
	 */
	public function testHelpersUsesGeneralTrait(): void
	{
		$traits = \class_uses(Helpers::class);
		$this->assertArrayHasKey('EightshiftLibs\Helpers\GeneralTrait', $traits);
	}

	/**
	 * Test that Helpers uses ShortcodeTrait.
	 */
	public function testHelpersUsesShortcodeTrait(): void
	{
		$traits = \class_uses(Helpers::class);
		$this->assertArrayHasKey('EightshiftLibs\Helpers\ShortcodeTrait', $traits);
	}

	/**
	 * Test that Helpers uses PostTrait.
	 */
	public function testHelpersUsesPostTrait(): void
	{
		$traits = \class_uses(Helpers::class);
		$this->assertArrayHasKey('EightshiftLibs\Helpers\PostTrait', $traits);
	}

	/**
	 * Test that Helpers uses MediaTrait.
	 */
	public function testHelpersUsesMediaTrait(): void
	{
		$traits = \class_uses(Helpers::class);
		$this->assertArrayHasKey('EightshiftLibs\Helpers\MediaTrait', $traits);
	}

	/**
	 * Test that Helpers uses ApiTrait.
	 */
	public function testHelpersUsesApiTrait(): void
	{
		$traits = \class_uses(Helpers::class);
		$this->assertArrayHasKey('EightshiftLibs\Helpers\ApiTrait', $traits);
	}

	/**
	 * Test that Helpers uses ProjectInfoTrait.
	 */
	public function testHelpersUsesProjectInfoTrait(): void
	{
		$traits = \class_uses(Helpers::class);
		$this->assertArrayHasKey('EightshiftLibs\Helpers\ProjectInfoTrait', $traits);
	}

	/**
	 * Test that Helpers uses DeprecatedTrait.
	 */
	public function testHelpersUsesDeprecatedTrait(): void
	{
		$traits = \class_uses(Helpers::class);
		$this->assertArrayHasKey('EightshiftLibs\Helpers\DeprecatedTrait', $traits);
	}

	/**
	 * Test that Helpers uses TailwindTrait.
	 */
	public function testHelpersUsesTailwindTrait(): void
	{
		$traits = \class_uses(Helpers::class);
		$this->assertArrayHasKey('EightshiftLibs\Helpers\TailwindTrait', $traits);
	}

	/**
	 * Test that static method calls work properly.
	 */
	public function testHelpersStaticMethodAccess(): void
	{
		// Test a simple static method from GeneralTrait
		$result = Helpers::clsx(['class1', 'class2', '']);
		$this->assertIsString($result);
	}
}
