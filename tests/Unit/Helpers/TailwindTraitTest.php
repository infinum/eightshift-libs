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

	/**
	 * @covers ::getTwPart
	 */
	public function testGetTwPartReturnsStringClasses(): void
	{
		$manifest = [
			'tailwind' => [
				'parts' => [
					'icon' => ['twClasses' => 'w-6 h-6'],
				],
			],
		];

		$this->assertSame('w-6 h-6', TailwindTraitWrapper::getTwPart('icon', $manifest));
	}

	public function testGetTwPartReturnsArrayClasses(): void
	{
		$manifest = [
			'tailwind' => [
				'parts' => [
					'icon' => ['twClasses' => ['w-6', 'h-6', 'text-red']],
				],
			],
		];

		$this->assertSame('w-6 h-6 text-red', TailwindTraitWrapper::getTwPart('icon', $manifest));
	}

	public function testGetTwPartReturnsClassesWithCustom(): void
	{
		$manifest = [
			'tailwind' => [
				'parts' => [
					'icon' => ['twClasses' => 'w-6 h-6'],
				],
			],
		];

		$this->assertSame('w-6 h-6 extra', TailwindTraitWrapper::getTwPart('icon', $manifest, 'extra'));
	}

	public function testGetTwPartReturnsEmptyForMissingPart(): void
	{
		$manifest = [
			'tailwind' => [
				'parts' => [
					'icon' => ['twClasses' => 'w-6'],
				],
			],
		];

		$this->assertSame('', TailwindTraitWrapper::getTwPart('missing', $manifest));
	}

	public function testGetTwPartReturnsCustomClassesOnEarlyReturn(): void
	{
		$this->assertSame('fallback', TailwindTraitWrapper::getTwPart('', [], 'fallback'));
	}

	/**
	 * @covers ::getTwDynamicPart
	 */
	public function testGetTwDynamicPartReturnsBaseAndOptionClasses(): void
	{
		$manifest = [
			'blockName' => 'test',
			'attributes' => [
				'testSize' => ['type' => 'string', 'default' => 'medium'],
			],
			'tailwind' => [
				'parts' => ['header' => ['twClasses' => 'p-4']],
				'options' => [
					'testSize' => [
						'part' => 'header',
						'twClasses' => [
							'small' => 'text-sm',
							'medium' => 'text-base',
							'large' => 'text-lg',
						],
					],
				],
			],
		];

		$this->assertSame('p-4 text-lg', TailwindTraitWrapper::getTwDynamicPart('header', ['testSize' => 'large'], $manifest));
	}

	public function testGetTwDynamicPartUsesDefaultAttribute(): void
	{
		$manifest = [
			'blockName' => 'test',
			'attributes' => [
				'testSize' => ['type' => 'string', 'default' => 'medium'],
			],
			'tailwind' => [
				'parts' => ['header' => ['twClasses' => 'p-4']],
				'options' => [
					'testSize' => [
						'part' => 'header',
						'twClasses' => [
							'medium' => 'text-base',
						],
					],
				],
			],
		];

		$this->assertSame('p-4 text-base', TailwindTraitWrapper::getTwDynamicPart('header', [], $manifest));
	}

	public function testGetTwDynamicPartWithResponsiveOption(): void
	{
		$manifest = [
			'blockName' => 'test',
			'attributes' => [
				'testSize' => ['type' => 'string', 'default' => 'md'],
			],
			'tailwind' => [
				'parts' => ['header' => ['twClasses' => 'p-4']],
				'options' => [
					'testSize' => [
						'part' => 'header',
						'responsive' => true,
						'twClasses' => [
							'sm' => 'text-sm',
							'lg' => 'text-lg',
						],
					],
				],
			],
		];

		$result = TailwindTraitWrapper::getTwDynamicPart('header', ['testSize' => ['_default' => 'sm', 'md' => 'lg']], $manifest);

		$this->assertStringContainsString('p-4', $result);
		$this->assertStringContainsString('text-sm', $result);
		$this->assertStringContainsString('md:text-lg', $result);
	}

	public function testGetTwDynamicPartReturnsCustomOnEarlyReturn(): void
	{
		$this->assertSame('fallback-class', TailwindTraitWrapper::getTwDynamicPart('', [], [], 'fallback-class'));
	}

	public function testGetTwDynamicPartSkipsOptionForDifferentPart(): void
	{
		$manifest = [
			'blockName' => 'test',
			'attributes' => [
				'testSize' => ['type' => 'string', 'default' => 'sm'],
			],
			'tailwind' => [
				'parts' => ['header' => ['twClasses' => 'p-4']],
				'options' => [
					'testSize' => [
						'part' => 'footer',
						'twClasses' => ['sm' => 'text-sm'],
					],
				],
			],
		];

		$this->assertSame('p-4', TailwindTraitWrapper::getTwDynamicPart('header', ['testSize' => 'sm'], $manifest));
	}

	public function testGetTwDynamicPartWithArrayTwClasses(): void
	{
		$manifest = [
			'blockName' => 'test',
			'attributes' => [
				'testSize' => ['type' => 'string', 'default' => 'sm'],
			],
			'tailwind' => [
				'parts' => ['header' => ['twClasses' => ['p-4', 'flex']]],
				'options' => [
					'testSize' => [
						'part' => 'header',
						'twClasses' => [
							'sm' => ['text-sm', 'leading-tight'],
						],
					],
				],
			],
		];

		$result = TailwindTraitWrapper::getTwDynamicPart('header', ['testSize' => 'sm'], $manifest);

		$this->assertStringContainsString('p-4 flex', $result);
		$this->assertStringContainsString('text-sm leading-tight', $result);
	}

	public function testGetTwDynamicPartHandlesBooleanValue(): void
	{
		$manifest = [
			'blockName' => 'test',
			'attributes' => [
				'testBold' => ['type' => 'boolean', 'default' => false],
			],
			'tailwind' => [
				'parts' => ['header' => ['twClasses' => 'p-4']],
				'options' => [
					'testBold' => [
						'part' => 'header',
						'twClasses' => [
							'true' => 'font-bold',
							'false' => 'font-normal',
						],
					],
				],
			],
		];

		$this->assertStringContainsString('font-bold', TailwindTraitWrapper::getTwDynamicPart('header', ['testBold' => true], $manifest));
	}

	/**
	 * @covers ::getTwClasses
	 */
	public function testGetTwClassesReturnsBaseAndOptionClasses(): void
	{
		$manifest = [
			'blockName' => 'test',
			'attributes' => [
				'testColor' => ['type' => 'string', 'default' => 'red'],
			],
			'tailwind' => [
				'base' => ['twClasses' => 'flex'],
				'options' => [
					'testColor' => [
						'twClasses' => [
							'red' => 'text-red',
							'blue' => 'text-blue',
						],
					],
				],
			],
		];

		$this->assertSame('flex text-blue', TailwindTraitWrapper::getTwClasses(['testColor' => 'blue'], $manifest));
	}

	public function testGetTwClassesUsesDefaultAttribute(): void
	{
		$manifest = [
			'blockName' => 'test',
			'attributes' => [
				'testColor' => ['type' => 'string', 'default' => 'red'],
			],
			'tailwind' => [
				'base' => ['twClasses' => 'flex'],
				'options' => [
					'testColor' => [
						'twClasses' => [
							'red' => 'text-red',
						],
					],
				],
			],
		];

		$this->assertSame('flex text-red', TailwindTraitWrapper::getTwClasses(['x' => 'y'], $manifest));
	}

	public function testGetTwClassesWithArrayBaseAndOptionClasses(): void
	{
		$manifest = [
			'blockName' => 'test',
			'attributes' => [
				'testSize' => ['type' => 'string', 'default' => 'md'],
			],
			'tailwind' => [
				'base' => ['twClasses' => ['flex', 'items-center']],
				'options' => [
					'testSize' => [
						'twClasses' => [
							'sm' => ['text-sm', 'p-1'],
						],
					],
				],
			],
		];

		$this->assertSame('flex items-center text-sm p-1', TailwindTraitWrapper::getTwClasses(['testSize' => 'sm'], $manifest));
	}

	public function testGetTwClassesHandlesBooleanTrue(): void
	{
		$manifest = [
			'blockName' => 'test',
			'attributes' => [
				'testBold' => ['type' => 'boolean', 'default' => false],
			],
			'tailwind' => [
				'base' => ['twClasses' => 'flex'],
				'options' => [
					'testBold' => [
						'twClasses' => [
							'true' => 'font-bold',
							'false' => 'font-normal',
						],
					],
				],
			],
		];

		$this->assertSame('flex font-bold', TailwindTraitWrapper::getTwClasses(['testBold' => true], $manifest));
	}

	public function testGetTwClassesHandlesBooleanFalse(): void
	{
		$manifest = [
			'blockName' => 'test',
			'attributes' => [
				'testBold' => ['type' => 'boolean', 'default' => false],
			],
			'tailwind' => [
				'base' => ['twClasses' => 'flex'],
				'options' => [
					'testBold' => [
						'twClasses' => [
							'true' => 'font-bold',
							'false' => 'font-normal',
						],
					],
				],
			],
		];

		$this->assertSame('flex font-normal', TailwindTraitWrapper::getTwClasses(['testBold' => false], $manifest));
	}

	public function testGetTwClassesSkipsOptionWithPart(): void
	{
		$manifest = [
			'blockName' => 'test',
			'attributes' => [
				'testSize' => ['type' => 'string', 'default' => 'sm'],
			],
			'tailwind' => [
				'base' => ['twClasses' => 'flex'],
				'options' => [
					'testSize' => [
						'part' => 'header',
						'twClasses' => ['sm' => 'text-sm'],
					],
				],
			],
		];

		$this->assertSame('flex', TailwindTraitWrapper::getTwClasses(['testSize' => 'sm'], $manifest));
	}

	public function testGetTwClassesReturnsCustomOnEarlyReturn(): void
	{
		$this->assertSame('fallback', TailwindTraitWrapper::getTwClasses([], [], 'fallback'));
	}

	public function testGetTwClassesWithCombinationMatch(): void
	{
		$manifest = [
			'blockName' => 'test',
			'attributes' => [
				'testBold' => ['type' => 'boolean', 'default' => false],
			],
			'tailwind' => [
				'base' => ['twClasses' => 'flex'],
				'combinations' => [
					[
						'attributes' => ['testBold' => true],
						'twClasses' => 'font-bold',
					],
				],
			],
		];

		$this->assertStringContainsString('font-bold', TailwindTraitWrapper::getTwClasses(['testBold' => true], $manifest));
	}

	public function testGetTwClassesWithCombinationNoMatch(): void
	{
		$manifest = [
			'blockName' => 'test',
			'attributes' => [
				'testBold' => ['type' => 'boolean', 'default' => false],
			],
			'tailwind' => [
				'base' => ['twClasses' => 'flex'],
				'combinations' => [
					[
						'attributes' => ['testBold' => true],
						'twClasses' => 'font-bold',
					],
				],
			],
		];

		$this->assertStringNotContainsString('font-bold', TailwindTraitWrapper::getTwClasses(['testBold' => false], $manifest));
	}

	public function testGetTwClassesWithCombinationArrayCondition(): void
	{
		$manifest = [
			'blockName' => 'test',
			'attributes' => [
				'testSize' => ['type' => 'string', 'default' => 'md'],
			],
			'tailwind' => [
				'base' => ['twClasses' => 'flex'],
				'combinations' => [
					[
						'attributes' => ['testSize' => ['lg', 'xl']],
						'twClasses' => ['font-bold', 'uppercase'],
					],
				],
			],
		];

		$result = TailwindTraitWrapper::getTwClasses(['testSize' => 'lg'], $manifest);
		$this->assertStringContainsString('font-bold uppercase', $result);

		$result2 = TailwindTraitWrapper::getTwClasses(['testSize' => 'sm'], $manifest);
		$this->assertStringNotContainsString('font-bold', $result2);
	}

	/**
	 * @covers ::tailwindClasses
	 * @covers ::processOption
	 */
	public function testTailwindClassesWithNonResponsiveOption(): void
	{
		$manifest = [
			'blockName' => 'test',
			'title' => 'Test Block',
			'attributes' => [
				'testColor' => ['type' => 'string', 'default' => 'red'],
			],
			'tailwind' => [
				'base' => ['twClasses' => 'flex'],
				'options' => [
					'testColor' => [
						'twClasses' => [
							'red' => 'text-red',
							'blue' => 'text-blue',
						],
					],
				],
			],
		];

		$result = TailwindTraitWrapper::tailwindClasses('base', ['testColor' => 'blue'], $manifest);

		$this->assertStringContainsString('text-blue', $result);
	}

	/**
	 * @covers ::tailwindClasses
	 * @covers ::processOption
	 */
	public function testTailwindClassesWithResponsiveOption(): void
	{
		$manifest = [
			'blockName' => 'test',
			'title' => 'Test Block',
			'attributes' => [
				'testSize' => ['type' => 'string', 'default' => 'md'],
			],
			'tailwind' => [
				'base' => ['twClasses' => 'flex'],
				'options' => [
					'testSize' => [
						'responsive' => true,
						'twClasses' => [
							'sm' => 'text-sm p-1',
							'lg' => 'text-lg p-3',
						],
					],
				],
			],
		];

		$result = TailwindTraitWrapper::tailwindClasses('base', ['testSize' => ['_default' => 'sm', 'md' => 'lg', '_desktopFirst' => false]], $manifest);

		$this->assertStringContainsString('text-sm p-1', $result);
		$this->assertStringContainsString('md:text-lg', $result);
		$this->assertStringContainsString('md:p-3', $result);
	}

	/**
	 * @covers ::tailwindClasses
	 * @covers ::processOption
	 */
	public function testTailwindClassesResponsiveWithEmptyBreakpointValue(): void
	{
		$manifest = [
			'blockName' => 'test',
			'title' => 'Test Block',
			'attributes' => [
				'testSize' => ['type' => 'string', 'default' => 'md'],
			],
			'tailwind' => [
				'base' => ['twClasses' => 'flex'],
				'options' => [
					'testSize' => [
						'responsive' => true,
						'twClasses' => [
							'sm' => 'text-sm',
							'md' => 'text-base',
						],
					],
				],
			],
		];

		$result = TailwindTraitWrapper::tailwindClasses('base', ['testSize' => ['_default' => 'sm', 'lg' => '']], $manifest);

		$this->assertStringContainsString('text-sm', $result);
		$this->assertStringNotContainsString('lg:', $result);
	}

	/**
	 * @covers ::tailwindClasses
	 * @covers ::processOption
	 */
	public function testTailwindClassesPartSpecificOption(): void
	{
		$manifest = [
			'blockName' => 'test',
			'title' => 'Test Block',
			'attributes' => [
				'testAlign' => ['type' => 'string', 'default' => 'left'],
			],
			'tailwind' => [
				'base' => ['twClasses' => 'flex'],
				'parts' => [
					'icon' => ['twClasses' => 'w-6'],
				],
				'options' => [
					'testAlign' => [
						'part' => 'icon',
						'twClasses' => [
							'left' => 'ml-0',
							'right' => 'ml-auto',
						],
					],
				],
			],
		];

		$iconResult = TailwindTraitWrapper::tailwindClasses('icon', ['testAlign' => 'right'], $manifest);
		$this->assertStringContainsString('ml-auto', $iconResult);

		$baseResult = TailwindTraitWrapper::tailwindClasses('base', ['testAlign' => 'right'], $manifest);
		$this->assertStringNotContainsString('ml-auto', $baseResult);
	}

	/**
	 * @covers ::tailwindClasses
	 * @covers ::processOption
	 */
	public function testTailwindClassesHandlesBooleanAttributeInOption(): void
	{
		$manifest = [
			'blockName' => 'test',
			'title' => 'Test Block',
			'attributes' => [
				'testBold' => ['type' => 'boolean', 'default' => false],
			],
			'tailwind' => [
				'base' => ['twClasses' => 'flex'],
				'options' => [
					'testBold' => [
						'twClasses' => [
							'true' => 'font-bold',
							'false' => 'font-normal',
						],
					],
				],
			],
		];

		$result = TailwindTraitWrapper::tailwindClasses('base', ['testBold' => true], $manifest);
		$this->assertStringContainsString('font-bold', $result);
	}

	/**
	 * @covers ::tailwindClasses
	 * @covers ::processCombination
	 */
	public function testTailwindClassesWithCombinationMatch(): void
	{
		$manifest = [
			'blockName' => 'test',
			'title' => 'Test Block',
			'attributes' => [
				'testBold' => ['type' => 'boolean', 'default' => false],
			],
			'tailwind' => [
				'base' => ['twClasses' => 'flex'],
				'combinations' => [
					[
						'attributes' => ['testBold' => true],
						'twClasses' => 'font-bold',
					],
				],
			],
		];

		$result = TailwindTraitWrapper::tailwindClasses('base', ['testBold' => true], $manifest);
		$this->assertStringContainsString('font-bold', $result);
	}

	/**
	 * @covers ::tailwindClasses
	 * @covers ::processCombination
	 */
	public function testTailwindClassesWithCombinationNoMatch(): void
	{
		$manifest = [
			'blockName' => 'test',
			'title' => 'Test Block',
			'attributes' => [
				'testBold' => ['type' => 'boolean', 'default' => false],
			],
			'tailwind' => [
				'base' => ['twClasses' => 'flex'],
				'combinations' => [
					[
						'attributes' => ['testBold' => true],
						'twClasses' => 'font-bold',
					],
				],
			],
		];

		$result = TailwindTraitWrapper::tailwindClasses('base', ['testBold' => false], $manifest);
		$this->assertStringNotContainsString('font-bold', $result);
	}

	/**
	 * @covers ::tailwindClasses
	 * @covers ::processCombination
	 */
	public function testTailwindClassesWithCombinationArrayCondition(): void
	{
		$manifest = [
			'blockName' => 'test',
			'title' => 'Test Block',
			'attributes' => [
				'testSize' => ['type' => 'string', 'default' => 'md'],
			],
			'tailwind' => [
				'base' => ['twClasses' => 'flex'],
				'combinations' => [
					[
						'attributes' => ['testSize' => ['lg', 'xl']],
						'twClasses' => ['font-bold', 'uppercase'],
					],
				],
			],
		];

		$result = TailwindTraitWrapper::tailwindClasses('base', ['testSize' => 'lg'], $manifest);
		$this->assertStringContainsString('font-bold uppercase', $result);
	}

	/**
	 * @covers ::tailwindClasses
	 * @covers ::processCombination
	 */
	public function testTailwindClassesWithCombinationPartOutput(): void
	{
		$manifest = [
			'blockName' => 'test',
			'title' => 'Test Block',
			'attributes' => [
				'testBold' => ['type' => 'boolean', 'default' => false],
			],
			'tailwind' => [
				'base' => ['twClasses' => 'flex'],
				'parts' => [
					'icon' => ['twClasses' => 'w-6'],
				],
				'combinations' => [
					[
						'attributes' => ['testBold' => true],
						'output' => [
							'base' => ['twClasses' => 'font-bold'],
							'icon' => ['twClasses' => 'text-red'],
						],
					],
				],
			],
		];

		$baseResult = TailwindTraitWrapper::tailwindClasses('base', ['testBold' => true], $manifest);
		$this->assertStringContainsString('font-bold', $baseResult);

		$iconResult = TailwindTraitWrapper::tailwindClasses('icon', ['testBold' => true], $manifest);
		$this->assertStringContainsString('text-red', $iconResult);
	}

	/**
	 * @covers ::tailwindClasses
	 * @covers ::processCombination
	 */
	public function testTailwindClassesThrowsJsonExceptionForInvalidCombination(): void
	{
		$manifest = [
			'blockName' => 'test',
			'title' => 'Test Block',
			'attributes' => [
				'testBold' => ['type' => 'boolean', 'default' => false],
			],
			'tailwind' => [
				'base' => ['twClasses' => 'flex'],
				'combinations' => [
					[
						'attributes' => ['testBold' => true],
						'twClasses' => ['key' => 'not-a-list'],
					],
				],
			],
		];

		$this->expectException(\JsonException::class);

		TailwindTraitWrapper::tailwindClasses('base', ['testBold' => true], $manifest);
	}
}
