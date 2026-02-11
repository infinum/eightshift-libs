<?php

/**
 * Tests for TailwindTrait helper methods.
 *
 * @package EightshiftLibs\Tests\Unit\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Helpers;

use EightshiftLibs\Tests\BaseTestCase;
use EightshiftLibs\Helpers\TailwindTrait;

/**
 * Wrapper class to test TailwindTrait methods.
 */
class TailwindTraitWrapper
{
	use TailwindTrait;
}

/**
 * Test case for TailwindTrait utility methods.
 *
 * @coversDefaultClass EightshiftLibs\Helpers\TailwindTrait
 */
class TailwindTraitTest extends BaseTestCase
{
	protected function setUp(): void
	{
		parent::setUp();
	}

	/**
	 * @covers ::getTwBreakpoints
	 */
	public function testGetTwBreakpointsMethodExists(): void
	{
		$this->assertTrue(\method_exists(TailwindTraitWrapper::class, 'getTwBreakpoints'));
	}

	/**
	 * @covers ::getTwPart
	 */
	public function testGetTwPartReturnsEmptyStringForEmptyPart(): void
	{
		$this->assertSame('', TailwindTraitWrapper::getTwPart('', []));
	}

	public function testGetTwPartReturnsEmptyStringForEmptyManifest(): void
	{
		$this->assertSame('', TailwindTraitWrapper::getTwPart('test', []));
	}

	public function testGetTwPartReturnsEmptyStringForMissingTailwindKey(): void
	{
		$this->assertSame('', TailwindTraitWrapper::getTwPart('test', ['key' => 'val']));
	}

	public function testGetTwPartReturnsEmptyStringForEmptyTailwindObject(): void
	{
		$this->assertSame('', TailwindTraitWrapper::getTwPart('test', ['tailwind' => []]));
	}

	/**
	 * @covers ::getTwDynamicPart
	 */
	public function testGetTwDynamicPartReturnsEmptyStringForEmptyPart(): void
	{
		$this->assertSame('', TailwindTraitWrapper::getTwDynamicPart('', [], []));
	}

	public function testGetTwDynamicPartReturnsEmptyStringForEmptyManifest(): void
	{
		$this->assertSame('', TailwindTraitWrapper::getTwDynamicPart('test', [], []));
	}

	public function testGetTwDynamicPartReturnsEmptyStringForMissingTailwindKey(): void
	{
		$this->assertSame('', TailwindTraitWrapper::getTwDynamicPart('test', [], ['key' => 'val']));
	}

	public function testGetTwDynamicPartReturnsEmptyStringForEmptyTailwindObject(): void
	{
		$this->assertSame('', TailwindTraitWrapper::getTwDynamicPart('test', [], ['tailwind' => []]));
	}

	/**
	 * @covers ::getTwClasses
	 */
	public function testGetTwClassesReturnsEmptyStringForEmptyAttributes(): void
	{
		$this->assertSame('', TailwindTraitWrapper::getTwClasses([], []));
	}

	public function testGetTwClassesReturnsEmptyStringForEmptyManifest(): void
	{
		$this->assertSame('', TailwindTraitWrapper::getTwClasses(['attr' => 'val'], []));
	}

	public function testGetTwClassesReturnsEmptyStringForMissingTailwindKey(): void
	{
		$this->assertSame('', TailwindTraitWrapper::getTwClasses(['attr' => 'val'], ['key' => 'val']));
	}

	public function testGetTwClassesReturnsEmptyStringForEmptyTailwindObject(): void
	{
		$this->assertSame('', TailwindTraitWrapper::getTwClasses(['attr' => 'val'], ['tailwind' => []]));
	}

	/**
	 * @covers ::tailwindClasses
	 */
	public function testTailwindClassesReturnsEmptyStringForEmptyPart(): void
	{
		$this->assertSame('', TailwindTraitWrapper::tailwindClasses('', [], []));
	}

	public function testTailwindClassesReturnsEmptyStringForEmptyManifest(): void
	{
		$this->assertSame('', TailwindTraitWrapper::tailwindClasses('test', [], []));
	}

	public function testTailwindClassesReturnsEmptyStringForMissingTailwindKey(): void
	{
		$this->assertSame('', TailwindTraitWrapper::tailwindClasses('test', [], ['key' => 'val']));
	}

	public function testTailwindClassesReturnsEmptyStringForEmptyTailwindObject(): void
	{
		$this->assertSame('', TailwindTraitWrapper::tailwindClasses('test', [], ['tailwind' => []]));
	}

	public function testTailwindClassesThrowsExceptionForInvalidPart(): void
	{
		$manifest = [
			'title' => 'Test Block',
			'tailwind' => [
				'base' => ['twClasses' => 'p-4'],
			],
		];

		$this->expectException(\Exception::class);
		$this->expectExceptionMessage("Part 'nonexistent' is not defined in the manifest.");

		TailwindTraitWrapper::tailwindClasses('nonexistent', [], $manifest);
	}

	public function testTailwindClassesReturnsBaseClassesForBasePart(): void
	{
		$manifest = [
			'title' => 'Test Block',
			'tailwind' => [
				'base' => ['twClasses' => 'p-4 flex'],
			],
		];

		$result = TailwindTraitWrapper::tailwindClasses('base', [], $manifest);

		$this->assertStringContainsString('p-4 flex', $result);
	}

	public function testTailwindClassesReturnsBaseClassesWhenTwClassesIsArray(): void
	{
		$manifest = [
			'title' => 'Test Block',
			'tailwind' => [
				'base' => ['twClasses' => ['p-4', 'flex', 'items-center']],
			],
		];

		$result = TailwindTraitWrapper::tailwindClasses('base', [], $manifest);

		$this->assertStringContainsString('p-4 flex items-center', $result);
	}

	public function testTailwindClassesReturnsPartClassesForDefinedPart(): void
	{
		$manifest = [
			'title' => 'Test Block',
			'tailwind' => [
				'base' => ['twClasses' => 'base-class'],
				'parts' => [
					'icon' => ['twClasses' => 'w-6 h-6'],
				],
			],
		];

		$result = TailwindTraitWrapper::tailwindClasses('icon', [], $manifest);

		$this->assertStringContainsString('w-6 h-6', $result);
	}

	public function testTailwindClassesIncludesDebugPrefixWhenWpDebugActive(): void
	{
		$manifest = [
			'title' => 'Test Block',
			'tailwind' => [
				'base' => ['twClasses' => 'p-4'],
			],
		];

		// WP_DEBUG is defined as true in bootstrap.php.
		$result = TailwindTraitWrapper::tailwindClasses('base', [], $manifest);

		$this->assertStringContainsString('_es__test-block/base', $result);
	}

	public function testTailwindClassesAppendsCustomClasses(): void
	{
		$manifest = [
			'title' => 'Test',
			'tailwind' => [
				'base' => ['twClasses' => 'p-4'],
			],
		];

		$result = TailwindTraitWrapper::tailwindClasses('base', [], $manifest, 'custom-1', 'custom-2');

		$this->assertStringContainsString('custom-1', $result);
		$this->assertStringContainsString('custom-2', $result);
	}
}
