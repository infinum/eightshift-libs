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

	/**
	 * Helper to set up Helpers cache with breakpoints.
	 *
	 * @param array<string, int> $breakpoints Breakpoints to set.
	 */
	private function setupHelpersCache(array $breakpoints = []): void
	{
		$bp = $breakpoints ?: [
			'mobile' => 480,
			'tablet' => 768,
			'desktop' => 1200,
		];

		$cache = [
			'blocks' => [
				'settings' => [
					'config' => [],
					'globalVariables' => [
						'breakpoints' => $bp,
					],
				],
			],
		];

		$reflection = new \ReflectionClass(\EightshiftLibs\Helpers\Helpers::class);
		$cacheProperty = $reflection->getProperty('cache');
		$cacheProperty->setValue(null, $cache);
	}

	/**
	 * Helper to clear Helpers cache.
	 */
	private function clearHelpersCache(): void
	{
		$reflection = new \ReflectionClass(\EightshiftLibs\Helpers\Helpers::class);
		$cacheProperty = $reflection->getProperty('cache');
		$cacheProperty->setValue(null, []);
	}

	/**
	 * @covers ::getTwBreakpoints
	 */
	public function testGetTwBreakpointsMobileFirst(): void
	{
		$this->setupHelpersCache();

		$result = TailwindTraitWrapper::getTwBreakpoints();

		$this->assertIsArray($result);
		$this->assertCount(3, $result);
		// Sorted ascending by value.
		$this->assertSame(['mobile', 'tablet', 'desktop'], $result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::getTwBreakpoints
	 */
	public function testGetTwBreakpointsDesktopFirst(): void
	{
		$this->setupHelpersCache();

		$result = TailwindTraitWrapper::getTwBreakpoints(true);

		$this->assertIsArray($result);
		$this->assertCount(3, $result);
		// Desktop-first should add max- prefix.
		$this->assertContains('max-mobile', $result);
		$this->assertContains('max-tablet', $result);
		$this->assertContains('max-desktop', $result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::getTwBreakpoints
	 */
	public function testGetTwBreakpointsSortsByValue(): void
	{
		// Use Patchwork to bypass the static cache with different breakpoints.
		\Patchwork\redefine(
			'EightshiftLibs\Helpers\Helpers::getSettingsGlobalVariablesBreakpoints',
			function () {
				return [
					'desktop' => 1200,
					'mobile' => 480,
					'wide' => 1440,
					'tablet' => 768,
				];
			}
		);

		$result = TailwindTraitWrapper::getTwBreakpoints();

		// Should be sorted by value, ascending.
		$this->assertSame(['mobile', 'tablet', 'desktop', 'wide'], $result);
	}

	/**
	 * @covers ::getTwClasses
	 */
	public function testGetTwClassesWithResponsiveOption(): void
	{
		$manifest = [
			'blockName' => 'test',
			'attributes' => [
				'testWidth' => ['type' => 'string', 'default' => 'full'],
			],
			'tailwind' => [
				'base' => ['twClasses' => 'flex'],
				'options' => [
					'testWidth' => [
						'responsive' => true,
						'twClasses' => [
							'full' => 'w-full',
							'half' => 'w-1/2',
						],
					],
				],
			],
		];

		$attributes = [
			'testWidth' => [
				'_default' => 'full',
				'md' => 'half',
			],
		];

		$result = TailwindTraitWrapper::getTwClasses($attributes, $manifest);

		$this->assertStringContainsString('flex', $result);
		$this->assertStringContainsString('w-full', $result);
		$this->assertStringContainsString('md:w-1/2', $result);
	}

	/**
	 * @covers ::getTwClasses
	 */
	public function testGetTwClassesWithResponsiveDesktopFirst(): void
	{
		$manifest = [
			'blockName' => 'test',
			'attributes' => [
				'testAlign' => ['type' => 'string', 'default' => 'left'],
			],
			'tailwind' => [
				'base' => ['twClasses' => 'block'],
				'options' => [
					'testAlign' => [
						'responsive' => true,
						'twClasses' => [
							'left' => 'text-left',
							'center' => 'text-center',
						],
					],
				],
			],
		];

		$attributes = [
			'testAlign' => [
				'_default' => 'left',
				'_desktopFirst' => true,
				'sm' => 'center',
			],
		];

		$result = TailwindTraitWrapper::getTwClasses($attributes, $manifest);

		$this->assertStringContainsString('text-left', $result);
		$this->assertStringContainsString('sm:text-center', $result);
		// _desktopFirst key should be filtered out.
		$this->assertStringNotContainsString('_desktopFirst', $result);
	}

	/**
	 * @covers ::getTwClasses
	 */
	public function testGetTwClassesWithResponsiveEmptyBreakpointValue(): void
	{
		$manifest = [
			'blockName' => 'test',
			'attributes' => [
				'testColor' => ['type' => 'string', 'default' => 'red'],
			],
			'tailwind' => [
				'base' => ['twClasses' => 'p-4'],
				'options' => [
					'testColor' => [
						'responsive' => true,
						'twClasses' => [
							'red' => 'text-red',
							'blue' => 'text-blue',
						],
					],
				],
			],
		];

		$attributes = [
			'testColor' => [
				'_default' => 'red',
				'md' => '',
			],
		];

		$result = TailwindTraitWrapper::getTwClasses($attributes, $manifest);

		$this->assertStringContainsString('text-red', $result);
		// Empty breakpoint value should be skipped.
		$this->assertStringNotContainsString('md:', $result);
	}

	/**
	 * @covers ::getTwClasses
	 */
	public function testGetTwClassesCombinationsWithArrayCondition(): void
	{
		$manifest = [
			'blockName' => 'test',
			'attributes' => [
				'testSize' => ['type' => 'string', 'default' => 'md'],
				'testColor' => ['type' => 'string', 'default' => 'red'],
			],
			'tailwind' => [
				'base' => ['twClasses' => 'flex'],
				'combinations' => [
					[
						'attributes' => [
							'testSize' => ['sm', 'md'],
							'testColor' => 'red',
						],
						'twClasses' => 'border-2 border-red',
					],
				],
			],
		];

		$result = TailwindTraitWrapper::getTwClasses(
			['testSize' => 'sm', 'testColor' => 'red'],
			$manifest
		);

		$this->assertStringContainsString('border-2', $result);
		$this->assertStringContainsString('border-red', $result);
	}

	/**
	 * @covers ::getTwClasses
	 */
	public function testGetTwClassesCombinationsNoMatch(): void
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
						'attributes' => ['testSize' => 'xl'],
						'twClasses' => 'extra-large',
					],
				],
			],
		];

		$result = TailwindTraitWrapper::getTwClasses(
			['testSize' => 'md'],
			$manifest
		);

		$this->assertStringContainsString('flex', $result);
		$this->assertStringNotContainsString('extra-large', $result);
	}

	/**
	 * @covers ::getTwDynamicPart
	 */
	public function testGetTwDynamicPartDesktopFirstResponsive(): void
	{
		$manifest = [
			'blockName' => 'test',
			'attributes' => [
				'testPad' => ['type' => 'string', 'default' => 'sm'],
			],
			'tailwind' => [
				'parts' => ['body' => ['twClasses' => 'mt-2']],
				'options' => [
					'testPad' => [
						'part' => 'body',
						'responsive' => true,
						'twClasses' => [
							'sm' => 'p-2',
							'lg' => 'p-6',
						],
					],
				],
			],
		];

		$result = TailwindTraitWrapper::getTwDynamicPart('body', [
			'testPad' => [
				'_default' => 'sm',
				'_desktopFirst' => true,
				'lg' => 'lg',
			],
		], $manifest);

		$this->assertStringContainsString('p-2', $result);
		$this->assertStringContainsString('lg:p-6', $result);
	}

	/**
	 * @covers ::getTwDynamicPart
	 */
	public function testGetTwDynamicPartResponsiveEmptyBreakpoint(): void
	{
		$manifest = [
			'blockName' => 'test',
			'attributes' => [
				'testFont' => ['type' => 'string', 'default' => 'sans'],
			],
			'tailwind' => [
				'parts' => ['title' => ['twClasses' => 'text-xl']],
				'options' => [
					'testFont' => [
						'part' => 'title',
						'responsive' => true,
						'twClasses' => [
							'sans' => 'font-sans',
							'serif' => 'font-serif',
						],
					],
				],
			],
		];

		$result = TailwindTraitWrapper::getTwDynamicPart('title', [
			'testFont' => [
				'_default' => 'sans',
				'sm' => '',
			],
		], $manifest);

		$this->assertStringContainsString('font-sans', $result);
		// Empty sm value should be skipped.
		$this->assertStringNotContainsString('sm:', $result);
	}

	/**
	 * @covers ::tailwindClasses
	 * @covers ::processOption
	 */
	public function testTailwindClassesWithPartSpecificResponsiveOption(): void
	{
		$manifest = [
			'blockName' => 'test',
			'title' => 'Test Block',
			'attributes' => [
				'testMargin' => ['type' => 'string', 'default' => 'sm'],
			],
			'tailwind' => [
				'base' => ['twClasses' => 'flex'],
				'parts' => ['header' => ['twClasses' => 'bg-gray']],
				'options' => [
					'testMargin' => [
						'part' => 'header',
						'responsive' => true,
						'twClasses' => [
							'sm' => 'm-2',
							'lg' => 'm-6',
						],
					],
				],
			],
		];

		$result = TailwindTraitWrapper::tailwindClasses('header', [
			'testMargin' => [
				'_default' => 'sm',
				'md' => 'lg',
			],
		], $manifest);

		$this->assertStringContainsString('bg-gray', $result);
		$this->assertStringContainsString('m-2', $result);
		$this->assertStringContainsString('md:m-6', $result);
	}

	/**
	 * @covers ::tailwindClasses
	 * @covers ::processOption
	 */
	public function testTailwindClassesOptionNotForCurrentPart(): void
	{
		$manifest = [
			'blockName' => 'test',
			'title' => 'Test Block',
			'attributes' => [
				'testPad' => ['type' => 'string', 'default' => 'sm'],
			],
			'tailwind' => [
				'base' => ['twClasses' => 'flex'],
				'parts' => ['footer' => ['twClasses' => 'mt-auto']],
				'options' => [
					'testPad' => [
						'part' => 'footer',
						'twClasses' => [
							'sm' => 'p-2',
							'lg' => 'p-6',
						],
					],
				],
			],
		];

		// Requesting 'base' part, but option is for 'footer'.
		$result = TailwindTraitWrapper::tailwindClasses('base', [
			'testPad' => 'sm',
		], $manifest);

		$this->assertStringContainsString('flex', $result);
		$this->assertStringNotContainsString('p-2', $result);
	}

	/**
	 * @covers ::tailwindClasses
	 * @covers ::processOption
	 */
	public function testTailwindClassesOptionWithDesktopFirstFilter(): void
	{
		$manifest = [
			'blockName' => 'test',
			'title' => 'Test Block',
			'attributes' => [
				'testSize' => ['type' => 'string', 'default' => 'md'],
			],
			'tailwind' => [
				'base' => ['twClasses' => 'block'],
				'options' => [
					'testSize' => [
						'responsive' => true,
						'twClasses' => [
							'md' => 'text-base',
							'lg' => 'text-lg',
						],
					],
				],
			],
		];

		$result = TailwindTraitWrapper::tailwindClasses('base', [
			'testSize' => [
				'_default' => 'md',
				'_desktopFirst' => true,
				'lg' => 'lg',
			],
		], $manifest);

		$this->assertStringContainsString('text-base', $result);
		$this->assertStringContainsString('lg:text-lg', $result);
		$this->assertStringNotContainsString('_desktopFirst', $result);
	}

	/**
	 * @covers ::tailwindClasses
	 * @covers ::processCombination
	 */
	public function testTailwindClassesCombinationPartMismatch(): void
	{
		$manifest = [
			'blockName' => 'test',
			'title' => 'Test Block',
			'attributes' => [
				'testBold' => ['type' => 'boolean', 'default' => false],
			],
			'tailwind' => [
				'base' => ['twClasses' => 'flex'],
				'parts' => ['icon' => ['twClasses' => 'w-4']],
				'combinations' => [
					[
						'attributes' => ['testBold' => true],
						'part' => 'icon',
						'twClasses' => 'font-bold',
					],
				],
			],
		];

		// Request 'base' part, but combination is for 'icon'.
		$result = TailwindTraitWrapper::tailwindClasses('base', ['testBold' => true], $manifest);

		$this->assertStringContainsString('flex', $result);
		// Combination for icon should NOT appear in base.
		$this->assertStringNotContainsString('font-bold', $result);
	}

	/**
	 * @covers ::tailwindClasses
	 * @covers ::processCombination
	 */
	public function testTailwindClassesCombinationArrayConditionNoMatch(): void
	{
		$manifest = [
			'blockName' => 'test',
			'title' => 'Test Block',
			'attributes' => [
				'testAlign' => ['type' => 'string', 'default' => 'left'],
			],
			'tailwind' => [
				'base' => ['twClasses' => 'flex'],
				'combinations' => [
					[
						'attributes' => ['testAlign' => ['center', 'right']],
						'twClasses' => 'justify-end',
					],
				],
			],
		];

		$result = TailwindTraitWrapper::tailwindClasses('base', ['testAlign' => 'left'], $manifest);

		// 'left' not in ['center', 'right'] → no match.
		$this->assertStringNotContainsString('justify-end', $result);
	}

	/**
	 * @covers ::getTwDynamicPart
	 */
	public function testGetTwDynamicPartSkipsOptionWithEmptyTwClasses(): void
	{
		$manifest = [
			'blockName' => 'test',
			'attributes' => [
				'testOpt' => ['type' => 'string', 'default' => 'val'],
			],
			'tailwind' => [
				'parts' => ['body' => ['twClasses' => 'mt-2']],
				'options' => [
					'testOpt' => [
						'part' => 'body',
						'twClasses' => null,
					],
				],
			],
		];

		$result = TailwindTraitWrapper::getTwDynamicPart('body', ['testOpt' => 'val'], $manifest);

		// Base classes only, option skipped due to null twClasses.
		$this->assertSame('mt-2', $result);
	}

	/**
	 * @covers ::getTwDynamicPart
	 */
	public function testGetTwDynamicPartSkipsOptionWithEmptyAttributeValue(): void
	{
		$manifest = [
			'blockName' => 'test',
			'attributes' => [
				'testOpt' => ['type' => 'string'],
			],
			'tailwind' => [
				'parts' => ['body' => ['twClasses' => 'mt-2']],
				'options' => [
					'testOpt' => [
						'part' => 'body',
						'twClasses' => [
							'val' => 'text-sm',
						],
					],
				],
			],
		];

		// Empty attribute value → should skip option.
		$result = TailwindTraitWrapper::getTwDynamicPart('body', ['testOpt' => ''], $manifest);

		$this->assertSame('mt-2', $result);
	}

	/**
	 * @covers ::getTwDynamicPart
	 */
	public function testGetTwDynamicPartSkipsOptionWithMissingTwClassesForValue(): void
	{
		$manifest = [
			'blockName' => 'test',
			'attributes' => [
				'testOpt' => ['type' => 'string', 'default' => 'val'],
			],
			'tailwind' => [
				'parts' => ['body' => ['twClasses' => 'p-4']],
				'options' => [
					'testOpt' => [
						'part' => 'body',
						'twClasses' => [
							'other' => 'text-lg',
						],
					],
				],
			],
		];

		// Value 'val' has no matching twClasses entry → skip.
		$result = TailwindTraitWrapper::getTwDynamicPart('body', ['testOpt' => 'val'], $manifest);

		$this->assertSame('p-4', $result);
	}

	/**
	 * @covers ::getTwDynamicPart
	 */
	public function testGetTwDynamicPartResponsiveArrayClasses(): void
	{
		$manifest = [
			'blockName' => 'test',
			'attributes' => [
				'testSize' => ['type' => 'string', 'default' => 'md'],
			],
			'tailwind' => [
				'parts' => ['title' => ['twClasses' => 'text-xl']],
				'options' => [
					'testSize' => [
						'part' => 'title',
						'responsive' => true,
						'twClasses' => [
							'sm' => ['text-sm', 'leading-tight'],
							'lg' => ['text-lg', 'leading-loose'],
						],
					],
				],
			],
		];

		$result = TailwindTraitWrapper::getTwDynamicPart('title', [
			'testSize' => [
				'_default' => 'sm',
				'md' => 'lg',
			],
		], $manifest);

		$this->assertStringContainsString('text-sm', $result);
		$this->assertStringContainsString('leading-tight', $result);
		$this->assertStringContainsString('md:text-lg', $result);
	}

	/**
	 * @covers ::getTwClasses
	 */
	public function testGetTwClassesSkipsOptionWithEmptyTwClasses(): void
	{
		$manifest = [
			'blockName' => 'test',
			'attributes' => [
				'testOpt' => ['type' => 'string', 'default' => 'val'],
			],
			'tailwind' => [
				'base' => ['twClasses' => 'flex'],
				'options' => [
					'testOpt' => [
						'twClasses' => null,
					],
				],
			],
		];

		$result = TailwindTraitWrapper::getTwClasses(['testOpt' => 'val'], $manifest);

		$this->assertSame('flex', $result);
	}

	/**
	 * @covers ::getTwClasses
	 */
	public function testGetTwClassesSkipsOptionWithEmptyValue(): void
	{
		$manifest = [
			'blockName' => 'test',
			'attributes' => [
				'testOpt' => ['type' => 'string'],
			],
			'tailwind' => [
				'base' => ['twClasses' => 'block'],
				'options' => [
					'testOpt' => [
						'twClasses' => [
							'val' => 'text-sm',
						],
					],
				],
			],
		];

		$result = TailwindTraitWrapper::getTwClasses(['testOpt' => ''], $manifest);

		$this->assertSame('block', $result);
	}

	/**
	 * @covers ::getTwClasses
	 */
	public function testGetTwClassesSkipsOptionWithMissingTwClassesForValue(): void
	{
		$manifest = [
			'blockName' => 'test',
			'attributes' => [
				'testOpt' => ['type' => 'string', 'default' => 'val'],
			],
			'tailwind' => [
				'base' => ['twClasses' => 'grid'],
				'options' => [
					'testOpt' => [
						'twClasses' => [
							'other' => 'text-lg',
						],
					],
				],
			],
		];

		$result = TailwindTraitWrapper::getTwClasses(['testOpt' => 'val'], $manifest);

		$this->assertSame('grid', $result);
	}

	/**
	 * @covers ::getTwClasses
	 */
	public function testGetTwClassesCombinationWithEmptyAttributeValue(): void
	{
		$manifest = [
			'blockName' => 'test',
			'attributes' => [
				'testSize' => ['type' => 'string', 'default' => 'md'],
				'testUnset' => ['type' => 'string'],
			],
			'tailwind' => [
				'base' => ['twClasses' => 'flex'],
				'combinations' => [
					[
						'attributes' => [
							'testUnset' => 'needed',
						],
						'twClasses' => 'special-combo',
					],
				],
			],
		];

		// testUnset not in attributes, manifest has no default, undefinedAllowed returns null.
		$result = TailwindTraitWrapper::getTwClasses(
			['testSize' => 'md'],
			$manifest
		);

		// Combination should not match because testUnset is null/empty.
		$this->assertStringNotContainsString('special-combo', $result);
	}

	/**
	 * @covers ::tailwindClasses
	 * @covers ::processOption
	 */
	public function testTailwindClassesProcessOptionMultiPartMissing(): void
	{
		$manifest = [
			'blockName' => 'test',
			'title' => 'Test Block',
			'attributes' => [
				'testOpt' => ['type' => 'string', 'default' => 'md'],
			],
			'tailwind' => [
				'base' => ['twClasses' => 'flex'],
				'parts' => ['header' => ['twClasses' => 'bg-white']],
				'options' => [
					'testOpt' => [
						// Multi-part option without single twClasses — has part-specific defs.
						'header' => [
							'twClasses' => [
								'md' => 'text-base',
							],
						],
					],
				],
			],
		];

		// Request base part but option is defined for 'header' part only.
		$result = TailwindTraitWrapper::tailwindClasses('base', ['testOpt' => 'md'], $manifest);

		$this->assertStringContainsString('flex', $result);
		// The option should NOT be applied since defs don't have 'base'.
		$this->assertStringNotContainsString('text-base', $result);
	}
}
