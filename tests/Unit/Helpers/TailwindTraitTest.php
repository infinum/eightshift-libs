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
	private TailwindTraitWrapper $wrapper;

	protected function setUp(): void
	{
		parent::setUp();
		$this->wrapper = new TailwindTraitWrapper();
	}

	/**
	 * Test that methods exist and are callable.
	 * These tests verify the trait structure.
	 */
	public function testGetTwBreakpointsMethodExists(): void
	{
		$this->assertTrue(method_exists(TailwindTraitWrapper::class, 'getTwBreakpoints'));
	}

	public function testGetTwPartMethodExists(): void
	{
		$this->assertTrue(method_exists(TailwindTraitWrapper::class, 'getTwPart'));
	}

	public function testGetTwDynamicPartMethodExists(): void
	{
		$this->assertTrue(method_exists(TailwindTraitWrapper::class, 'getTwDynamicPart'));
	}

	public function testGetTwClassesMethodExists(): void
	{
		$this->assertTrue(method_exists(TailwindTraitWrapper::class, 'getTwClasses'));
	}

	public function testTailwindClassesMethodExists(): void
	{
		$this->assertTrue(method_exists(TailwindTraitWrapper::class, 'tailwindClasses'));
	}
}
