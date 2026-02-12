<?php

/**
 * Tests for CssVariablesTrait helper methods.
 *
 * @package EightshiftLibs\Tests\Unit\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Helpers;

use EightshiftLibs\Tests\BaseTestCase;
use EightshiftLibs\Helpers\CssVariablesTrait;
use Brain\Monkey\Functions;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Wrapper class to test CssVariablesTrait methods.
 */
class CssVariablesTraitWrapper
{
	use CssVariablesTrait;
}

/**
 * Test case for CssVariablesTrait utility methods.
 *
 * @coversDefaultClass EightshiftLibs\Helpers\CssVariablesTrait
 */
class CssVariablesTraitTest extends BaseTestCase
{
	private CssVariablesTraitWrapper $wrapper;

	protected function setUp(): void
	{
		parent::setUp();
		$this->wrapper = new CssVariablesTraitWrapper();

		// Mock common WordPress functions
		Functions\when('wp_unique_id')->alias(function ($prefix) {
			static $counter = 0;
			return $prefix . ++$counter;
		});
	}

	/**
	 * @covers ::hexToRgb
	 */
	public function testHexToRgbWithSixCharacterHex(): void
	{
		$result = $this->wrapper::hexToRgb('#FF5733');

		$this->assertEquals('255 87 51', $result);
	}

	/**
	 * @covers ::hexToRgb
	 */
	public function testHexToRgbWithThreeCharacterHex(): void
	{
		$result = $this->wrapper::hexToRgb('#F53');

		// F53 expands to FF5533
		$this->assertEquals('255 85 51', $result);
	}

	/**
	 * @covers ::hexToRgb
	 */
	#[DataProvider('hexColorProvider')]
	public function testHexToRgbWithVariousHexColors(string $hex, string $expected): void
	{
		$result = $this->wrapper::hexToRgb($hex);

		$this->assertEquals($expected, $result);
	}

	/**
	 * @covers ::hexToRgb
	 */
	public function testHexToRgbWithoutHashSymbol(): void
	{
		$result = $this->wrapper::hexToRgb('FF5733');

		$this->assertEquals('255 87 51', $result);
	}

	/**
	 * @covers ::hexToRgb
	 */
	public function testHexToRgbWithInvalidCharacters(): void
	{
		// Invalid characters should be filtered out
		$result = $this->wrapper::hexToRgb('#FF57XY');

		// Only 'FF57' is valid, which is 4 characters - defaults to 0 0 0
		$this->assertEquals('0 0 0', $result);
	}

	/**
	 * @covers ::hexToRgb
	 */
	public function testHexToRgbWithEmptyString(): void
	{
		$result = $this->wrapper::hexToRgb('');

		$this->assertEquals('0 0 0', $result);
	}

	/**
	 * @covers ::hexToRgb
	 */
	public function testHexToRgbWithBlackColor(): void
	{
		$result = $this->wrapper::hexToRgb('#000000');

		$this->assertEquals('0 0 0', $result);
	}

	/**
	 * @covers ::hexToRgb
	 */
	public function testHexToRgbWithWhiteColor(): void
	{
		$result = $this->wrapper::hexToRgb('#FFFFFF');

		$this->assertEquals('255 255 255', $result);
	}

	/**
	 * @covers ::hexToRgb
	 */
	public function testHexToRgbWithLowerCaseHex(): void
	{
		$result = $this->wrapper::hexToRgb('#ff5733');

		$this->assertEquals('255 87 51', $result);
	}

	/**
	 * @covers ::getUnique
	 */
	public function testGetUniqueWithDefaultAttributes(): void
	{
		$result = $this->wrapper::getUnique([]);

		$this->assertStringStartsWith('es-', $result);
	}

	/**
	 * @covers ::getUnique
	 */
	public function testGetUniqueWithEmptyAttributes(): void
	{
		$result1 = $this->wrapper::getUnique([]);
		$result2 = $this->wrapper::getUnique([]);

		// Each call should return a unique ID
		$this->assertNotEquals($result1, $result2);
		$this->assertStringStartsWith('es-', $result1);
		$this->assertStringStartsWith('es-', $result2);
	}

	/**
	 * @covers ::getUnique
	 */
	public function testGetUniqueWithBlockSsrFalse(): void
	{
		$attributes = ['blockSsr' => false];
		$result = $this->wrapper::getUnique($attributes);

		$this->assertStringStartsWith('es-', $result);
	}

	/**
	 * @covers ::getUnique
	 */
	public function testGetUniqueWithBlockSsrTrue(): void
	{
		$attributes = ['blockSsr' => true];
		$result = $this->wrapper::getUnique($attributes);

		// Should return hex string when SSR is enabled
		$this->assertNotEmpty($result);
		$this->assertDoesNotMatchRegularExpression('/^es-/', $result);
		// Hex string from bin2hex of 4 bytes should be 8 characters
		$this->assertEquals(8, strlen($result));
		$this->assertMatchesRegularExpression('/^[0-9a-f]{8}$/', $result);
	}

	/**
	 * @covers ::getUnique
	 */
	public function testGetUniqueWithBlockSsrStringTrue(): void
	{
		// Test with string '1' which should be converted to boolean true
		$attributes = ['blockSsr' => '1'];
		$result = $this->wrapper::getUnique($attributes);

		// Should return hex string
		$this->assertMatchesRegularExpression('/^[0-9a-f]{8}$/', $result);
	}

	/**
	 * @covers ::getUnique
	 */
	public function testGetUniqueGeneratesRandomHexForSsr(): void
	{
		$attributes = ['blockSsr' => true];
		$result1 = $this->wrapper::getUnique($attributes);
		$result2 = $this->wrapper::getUnique($attributes);

		// Each call should generate different hex
		$this->assertNotEquals($result1, $result2);
		$this->assertMatchesRegularExpression('/^[0-9a-f]{8}$/', $result1);
		$this->assertMatchesRegularExpression('/^[0-9a-f]{8}$/', $result2);
	}

	/**
	 * Data providers
	 */
	public static function hexColorProvider(): array
	{
		return [
			'red' => ['#FF0000', '255 0 0'],
			'green' => ['#00FF00', '0 255 0'],
			'blue' => ['#0000FF', '0 0 255'],
			'short red' => ['#F00', '255 0 0'],
			'short green' => ['#0F0', '0 255 0'],
			'short blue' => ['#00F', '0 0 255'],
			'gray' => ['#808080', '128 128 128'],
			'yellow' => ['#FFFF00', '255 255 0'],
			'cyan' => ['#00FFFF', '0 255 255'],
			'magenta' => ['#FF00FF', '255 0 255'],
		];
	}

	/**
	 * Set up Helpers cache with settings config for CSS variable methods.
	 *
	 * @param array<string, mixed> $config Config overrides.
	 */
	private function setupHelpersCache(array $config = []): void
	{
		$cache = [
			'blocks' => [
				'settings' => [
					'config' => \array_merge([
						'outputCssOptimize' => false,
						'outputCssSelectorName' => 'es-css',
					], $config),
				],
			],
		];

		$reflection = new \ReflectionClass(\EightshiftLibs\Helpers\Helpers::class);
		$cacheProperty = $reflection->getProperty('cache');
		$cacheProperty->setAccessible(true);
		$cacheProperty->setValue(null, $cache);
	}

	/**
	 * Clear Helpers cache after tests that set it.
	 */
	private function clearHelpersCache(): void
	{
		$reflection = new \ReflectionClass(\EightshiftLibs\Helpers\Helpers::class);
		$cacheProperty = $reflection->getProperty('cache');
		$cacheProperty->setAccessible(true);
		$cacheProperty->setValue(null, []);

		// Also clear configCache static in StoreBlocksTrait.
		// getConfig() uses a local static $configCache; we can't clear it directly,
		// but it will be re-initialized on next getConfig() call from fresh cache.
	}

	/**
	 * @covers ::outputCssVariablesGlobalClean
	 * @covers ::globalInner
	 */
	public function testOutputCssVariablesGlobalCleanWithScalarVariables(): void
	{
		$this->setupHelpersCache();

		$globalSettings = [
			'globalVariables' => [
				'maxWidth' => '1200px',
				'baseFontSize' => '16px',
			],
		];

		$result = $this->wrapper::outputCssVariablesGlobalClean($globalSettings);

		$this->assertStringContainsString(':root {', $result);
		$this->assertStringContainsString('--global-max-width: 1200px;', $result);
		$this->assertStringContainsString('--global-base-font-size: 16px;', $result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariablesGlobalClean
	 * @covers ::globalInner
	 */
	public function testOutputCssVariablesGlobalCleanWithColorArray(): void
	{
		$this->setupHelpersCache();

		$globalSettings = [
			'globalVariables' => [
				'colors' => [
					['slug' => 'primary', 'color' => '#FF0000'],
					['slug' => 'secondary', 'color' => '#00FF00'],
				],
			],
		];

		$result = $this->wrapper::outputCssVariablesGlobalClean($globalSettings);

		$this->assertStringContainsString(':root {', $result);
		$this->assertStringContainsString('--global-colors-primary: #FF0000;', $result);
		$this->assertStringContainsString('--global-colors-primary-values: 255 0 0;', $result);
		$this->assertStringContainsString('--global-colors-secondary: #00FF00;', $result);
		$this->assertStringContainsString('--global-colors-secondary-values: 0 255 0;', $result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariablesGlobalClean
	 * @covers ::globalInner
	 */
	public function testOutputCssVariablesGlobalCleanWithGradients(): void
	{
		$this->setupHelpersCache();

		$globalSettings = [
			'globalVariables' => [
				'gradients' => [
					['slug' => 'sunset', 'gradient' => 'linear-gradient(90deg, #FF0000, #FFFF00)'],
				],
			],
		];

		$result = $this->wrapper::outputCssVariablesGlobalClean($globalSettings);

		$this->assertStringContainsString('--global-gradients-sunset: linear-gradient(90deg, #FF0000, #FFFF00);', $result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariablesGlobalClean
	 * @covers ::globalInner
	 */
	public function testOutputCssVariablesGlobalCleanWithFontSizes(): void
	{
		$this->setupHelpersCache();

		$globalSettings = [
			'globalVariables' => [
				'fontSizes' => [
					['slug' => 'small'],
					['slug' => 'large'],
				],
			],
		];

		$result = $this->wrapper::outputCssVariablesGlobalClean($globalSettings);

		$this->assertStringContainsString('--global-font-sizes-small: small;', $result);
		$this->assertStringContainsString('--global-font-sizes-large: large;', $result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariablesGlobalClean
	 * @covers ::globalInner
	 */
	public function testOutputCssVariablesGlobalCleanWithDefaultArrayValues(): void
	{
		$this->setupHelpersCache();

		$globalSettings = [
			'globalVariables' => [
				'spacing' => [
					'small' => '8px',
					'medium' => '16px',
					'large' => '32px',
				],
			],
		];

		$result = $this->wrapper::outputCssVariablesGlobalClean($globalSettings);

		$this->assertStringContainsString('--global-spacing-small: 8px;', $result);
		$this->assertStringContainsString('--global-spacing-medium: 16px;', $result);
		$this->assertStringContainsString('--global-spacing-large: 32px;', $result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariablesGlobalClean
	 */
	public function testOutputCssVariablesGlobalCleanWithMissingGlobalVariablesKey(): void
	{
		$this->setupHelpersCache();

		// Pass globalSettings without globalVariables key.
		$globalSettings = [
			'someOtherKey' => 'value',
		];

		$result = $this->wrapper::outputCssVariablesGlobalClean($globalSettings);

		// Should still produce :root wrapper with no variables.
		$this->assertStringContainsString(':root {', $result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariablesGlobalClean
	 */
	public function testOutputCssVariablesGlobalCleanWithEmptyVariables(): void
	{
		$this->setupHelpersCache();

		$globalSettings = [
			'globalVariables' => [],
		];

		$result = $this->wrapper::outputCssVariablesGlobalClean($globalSettings);

		$this->assertStringContainsString(':root {', $result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariablesGlobalClean
	 */
	public function testOutputCssVariablesGlobalCleanWithMixedTypes(): void
	{
		$this->setupHelpersCache();

		$globalSettings = [
			'globalVariables' => [
				'maxWidth' => '1200px',
				'colors' => [
					['slug' => 'brand', 'color' => '#3366FF'],
				],
				'baseFontSize' => '16px',
			],
		];

		$result = $this->wrapper::outputCssVariablesGlobalClean($globalSettings);

		$this->assertStringContainsString('--global-max-width: 1200px;', $result);
		$this->assertStringContainsString('--global-colors-brand: #3366FF;', $result);
		$this->assertStringContainsString('--global-base-font-size: 16px;', $result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariablesGlobal
	 */
	public function testOutputCssVariablesGlobalWrapsInStyleTag(): void
	{
		$this->setupHelpersCache();

		$globalSettings = [
			'globalVariables' => [
				'maxWidth' => '960px',
			],
		];

		$result = $this->wrapper::outputCssVariablesGlobal($globalSettings);

		$this->assertStringStartsWith("<style id='es-css-global'>", $result);
		$this->assertStringEndsWith('</style>', $result);
		$this->assertStringContainsString(':root {', $result);
		$this->assertStringContainsString('--global-max-width: 960px;', $result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariablesGlobal
	 */
	public function testOutputCssVariablesGlobalContainsCssVariables(): void
	{
		$this->setupHelpersCache();

		$globalSettings = [
			'globalVariables' => [
				'baseFontSize' => '14px',
				'lineHeight' => '1.5',
			],
		];

		$result = $this->wrapper::outputCssVariablesGlobal($globalSettings);

		$this->assertStringContainsString('<style', $result);
		$this->assertStringEndsWith('</style>', $result);
		$this->assertStringContainsString('--global-base-font-size: 14px;', $result);
		$this->assertStringContainsString('--global-line-height: 1.5;', $result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariables
	 */
	public function testOutputCssVariablesBailsOutWhenNoVariablesKey(): void
	{
		// Manifest without 'variables' or 'variablesCustom' key should return empty string.
		$result = $this->wrapper::outputCssVariables(
			['blockClass' => 'my-block'],
			['blockName' => 'test'],
			'unique-1'
		);

		$this->assertSame('', $result);
	}

	/**
	 * Helper to set up WordPress function mocks needed for outputCssVariables.
	 */
	private function mockOutputCssVariablesDependencies(): void
	{
		Functions\when('sanitize_text_field')->returnArg();
		Functions\when('wp_unslash')->returnArg();
		Functions\when('wp_is_json_request')->justReturn(false);
		Functions\when('esc_html')->returnArg();
	}

	/**
	 * Standard global settings with breakpoints for testing.
	 *
	 * @return array<string, mixed>
	 */
	private function getTestGlobalSettings(): array
	{
		return [
			'globalVariables' => [
				'breakpoints' => [
					'tablet' => 768,
					'desktop' => 1200,
				],
			],
		];
	}

	/**
	 * @covers ::outputCssVariables
	 * @covers ::prepareVariableData
	 * @covers ::getDefaultBreakpoints
	 * @covers ::setVariablesToBreakpoints
	 * @covers ::variablesInner
	 * @covers ::getCssVariablesTypeDefault
	 */
	public function testOutputCssVariablesWithSimpleVariable(): void
	{
		$this->setupHelpersCache();
		$this->mockOutputCssVariablesDependencies();

		$manifest = [
			'blockName' => 'my-block',
			'variables' => [
				'myColor' => [
					[
						'variable' => [
							'my-color' => '%value%',
						],
					],
				],
			],
		];

		$attributes = [
			'blockClass' => 'block-my-block',
			'myColor' => '#FF0000',
		];

		$result = $this->wrapper::outputCssVariables($attributes, $manifest, 'unique-1', '', $this->getTestGlobalSettings());

		$this->assertStringContainsString('<style>', $result);
		$this->assertStringContainsString('</style>', $result);
		$this->assertStringContainsString('--my-color: #FF0000;', $result);
		$this->assertStringContainsString('block-my-block', $result);
		$this->assertStringContainsString("data-id='unique-1'", $result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariables
	 * @covers ::getCssVariablesTypeDefault
	 */
	public function testOutputCssVariablesWithComponentClass(): void
	{
		$this->setupHelpersCache();
		$this->mockOutputCssVariablesDependencies();

		$manifest = [
			'componentName' => 'heading',
			'componentClass' => 'comp-heading',
			'variables' => [
				'headingSize' => [
					[
						'variable' => [
							'heading-size' => '%value%',
						],
					],
				],
			],
		];

		$attributes = [
			'headingSize' => '24px',
		];

		$result = $this->wrapper::outputCssVariables($attributes, $manifest, 'unique-2', '', $this->getTestGlobalSettings());

		$this->assertStringContainsString('comp-heading', $result);
		$this->assertStringContainsString('--heading-size: 24px;', $result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariables
	 * @covers ::getCssVariablesTypeDefault
	 */
	public function testOutputCssVariablesWithCustomSelector(): void
	{
		$this->setupHelpersCache();
		$this->mockOutputCssVariablesDependencies();

		$manifest = [
			'blockName' => 'my-block',
			'variables' => [
				'myPadding' => [
					[
						'variable' => [
							'padding' => '%value%',
						],
					],
				],
			],
		];

		$attributes = [
			'blockClass' => 'block-my-block',
			'myPadding' => '20px',
		];

		$result = $this->wrapper::outputCssVariables($attributes, $manifest, 'unique-3', 'custom-selector', $this->getTestGlobalSettings());

		$this->assertStringContainsString('custom-selector', $result);
		$this->assertStringNotContainsString('block-my-block', $result);
		$this->assertStringContainsString('--padding: 20px;', $result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariables
	 * @covers ::getCssVariablesTypeDefault
	 */
	public function testOutputCssVariablesWithVariablesCustomOnly(): void
	{
		$this->setupHelpersCache();
		$this->mockOutputCssVariablesDependencies();

		$manifest = [
			'blockName' => 'my-block',
			'variablesCustom' => [
				'--custom-font: Arial',
				'--custom-size: 14px',
			],
		];

		$attributes = [
			'blockClass' => 'block-my-block',
		];

		$result = $this->wrapper::outputCssVariables($attributes, $manifest, 'unique-4', '', $this->getTestGlobalSettings());

		$this->assertStringContainsString('<style>', $result);
		$this->assertStringContainsString('--custom-font: Arial', $result);
		$this->assertStringContainsString('--custom-size: 14px', $result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariables
	 * @covers ::getCssVariablesTypeDefault
	 * @covers ::variablesInner
	 */
	public function testOutputCssVariablesWithBothVariablesAndCustom(): void
	{
		$this->setupHelpersCache();
		$this->mockOutputCssVariablesDependencies();

		$manifest = [
			'blockName' => 'my-block',
			'variables' => [
				'bgColor' => [
					[
						'variable' => [
							'bg-color' => '%value%',
						],
					],
				],
			],
			'variablesCustom' => [
				'--extra-var: 100%',
			],
		];

		$attributes = [
			'blockClass' => 'block-my-block',
			'bgColor' => 'blue',
		];

		$result = $this->wrapper::outputCssVariables($attributes, $manifest, 'unique-5', '', $this->getTestGlobalSettings());

		$this->assertStringContainsString('--bg-color: blue;', $result);
		$this->assertStringContainsString('--extra-var: 100%', $result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariables
	 * @covers ::setVariablesToBreakpoints
	 */
	public function testOutputCssVariablesWithBooleanAttribute(): void
	{
		$this->setupHelpersCache();
		$this->mockOutputCssVariablesDependencies();

		$manifest = [
			'blockName' => 'test-block',
			'variables' => [
				'isVisible' => [
					'true' => [
						[
							'variable' => [
								'display' => 'block',
							],
						],
					],
					'false' => [
						[
							'variable' => [
								'display' => 'none',
							],
						],
					],
				],
			],
		];

		$attributes = [
			'blockClass' => 'block-test',
			'isVisible' => true,
		];

		$result = $this->wrapper::outputCssVariables($attributes, $manifest, 'unique-6', '', $this->getTestGlobalSettings());

		$this->assertStringContainsString('--display: block;', $result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariables
	 * @covers ::setVariablesToBreakpoints
	 */
	public function testOutputCssVariablesWithBooleanFalseAttribute(): void
	{
		$this->setupHelpersCache();
		$this->mockOutputCssVariablesDependencies();

		$manifest = [
			'blockName' => 'test-block',
			'variables' => [
				'isHidden' => [
					'true' => [
						[
							'variable' => [
								'visibility' => 'hidden',
							],
						],
					],
					'false' => [
						[
							'variable' => [
								'visibility' => 'visible',
							],
						],
					],
				],
			],
		];

		$attributes = [
			'blockClass' => 'block-test',
			'isHidden' => false,
		];

		$result = $this->wrapper::outputCssVariables($attributes, $manifest, 'unique-7', '', $this->getTestGlobalSettings());

		$this->assertStringContainsString('--visibility: visible;', $result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariables
	 * @covers ::getCssVariablesTypeDefault
	 */
	public function testOutputCssVariablesWithEmptyUniqueOmitsDataId(): void
	{
		$this->setupHelpersCache();
		$this->mockOutputCssVariablesDependencies();

		$manifest = [
			'blockName' => 'my-block',
			'variables' => [
				'myWidth' => [
					[
						'variable' => [
							'width' => '%value%',
						],
					],
				],
			],
		];

		$attributes = [
			'blockClass' => 'block-my-block',
			'myWidth' => '100%',
		];

		$result = $this->wrapper::outputCssVariables($attributes, $manifest, '', '', $this->getTestGlobalSettings());

		$this->assertStringNotContainsString('data-id', $result);
		$this->assertStringContainsString('--width: 100%;', $result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariables
	 * @covers ::setVariablesToBreakpoints
	 * @covers ::variablesInner
	 */
	public function testOutputCssVariablesWithMultipleVariables(): void
	{
		$this->setupHelpersCache();
		$this->mockOutputCssVariablesDependencies();

		$manifest = [
			'blockName' => 'card',
			'variables' => [
				'cardColor' => [
					[
						'variable' => [
							'card-color' => '%value%',
						],
					],
				],
				'cardPadding' => [
					[
						'variable' => [
							'card-padding' => '%value%',
						],
					],
				],
			],
		];

		$attributes = [
			'blockClass' => 'block-card',
			'cardColor' => 'red',
			'cardPadding' => '10px',
		];

		$result = $this->wrapper::outputCssVariables($attributes, $manifest, 'card-1', '', $this->getTestGlobalSettings());

		$this->assertStringContainsString('--card-color: red;', $result);
		$this->assertStringContainsString('--card-padding: 10px;', $result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariables
	 * @covers ::setVariablesToBreakpoints
	 */
	public function testOutputCssVariablesWithBreakpointSpecificVariable(): void
	{
		$this->setupHelpersCache();
		$this->mockOutputCssVariablesDependencies();

		$manifest = [
			'blockName' => 'hero',
			'variables' => [
				'heroHeight' => [
					[
						'variable' => [
							'hero-height' => '%value%',
						],
						'breakpoint' => 'desktop',
					],
				],
			],
		];

		$attributes = [
			'blockClass' => 'block-hero',
			'heroHeight' => '500px',
		];

		$result = $this->wrapper::outputCssVariables($attributes, $manifest, 'hero-1', '', $this->getTestGlobalSettings());

		$this->assertStringContainsString('<style>', $result);
		// Desktop breakpoint should produce a media query.
		$this->assertStringContainsString('@media', $result);
		$this->assertStringContainsString('--hero-height: 500px;', $result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariables
	 * @covers ::setVariablesToBreakpoints
	 */
	public function testOutputCssVariablesWithInverseBreakpoint(): void
	{
		$this->setupHelpersCache();
		$this->mockOutputCssVariablesDependencies();

		$manifest = [
			'blockName' => 'banner',
			'variables' => [
				'bannerWidth' => [
					[
						'variable' => [
							'banner-width' => '%value%',
						],
						'inverse' => true,
					],
				],
			],
		];

		$attributes = [
			'blockClass' => 'block-banner',
			'bannerWidth' => '80%',
		];

		$result = $this->wrapper::outputCssVariables($attributes, $manifest, 'banner-1', '', $this->getTestGlobalSettings());

		$this->assertStringContainsString('<style>', $result);
		$this->assertStringContainsString('--banner-width: 80%;', $result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariables
	 * @covers ::setVariablesToBreakpoints
	 * @covers ::variablesInner
	 */
	public function testOutputCssVariablesWithAssociativeValueLookup(): void
	{
		$this->setupHelpersCache();
		$this->mockOutputCssVariablesDependencies();

		$manifest = [
			'blockName' => 'button',
			'variables' => [
				'buttonSize' => [
					'small' => [
						[
							'variable' => [
								'btn-padding' => '4px 8px',
							],
						],
					],
					'large' => [
						[
							'variable' => [
								'btn-padding' => '12px 24px',
							],
						],
					],
				],
			],
		];

		$attributes = [
			'blockClass' => 'block-button',
			'buttonSize' => 'large',
		];

		$result = $this->wrapper::outputCssVariables($attributes, $manifest, 'btn-1', '', $this->getTestGlobalSettings());

		$this->assertStringContainsString('--btn-padding: 12px 24px;', $result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariables
	 * @covers ::getCssVariablesTypeDefault
	 */
	public function testOutputCssVariablesReturnsEmptyWhenAllVariablesEmpty(): void
	{
		$this->setupHelpersCache();
		$this->mockOutputCssVariablesDependencies();

		$manifest = [
			'blockName' => 'empty-block',
			'variables' => [
				'myAttr' => [
					[
						'variable' => [
							'my-var' => '%value%',
						],
					],
				],
			],
		];

		// Attribute value is empty string — setVariablesToBreakpoints skips it.
		$attributes = [
			'blockClass' => 'block-empty',
			'myAttr' => '',
		];

		$result = $this->wrapper::outputCssVariables($attributes, $manifest, 'empty-1', '', $this->getTestGlobalSettings());

		// When no variables are populated and no variablesCustom, output should be empty.
		$this->assertSame('', $result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariables
	 * @covers ::setupResponsiveVariables
	 * @covers ::setBreakpointResponsiveVariables
	 * @covers ::setVariablesToBreakpoints
	 */
	public function testOutputCssVariablesWithResponsiveAttributes(): void
	{
		$this->setupHelpersCache();
		$this->mockOutputCssVariablesDependencies();

		$manifest = [
			'blockName' => 'section',
			'responsiveAttributes' => [
				'sectionWidth' => [
					'large' => 'sectionWidthLarge',
					'desktop' => 'sectionWidthDesktop',
				],
			],
			'variables' => [
				'sectionWidth' => [
					[
						'variable' => [
							'section-width' => '%value%',
						],
					],
				],
			],
		];

		$attributes = [
			'blockClass' => 'block-section',
			'sectionWidthLarge' => '1200px',
			'sectionWidthDesktop' => '960px',
		];

		$result = $this->wrapper::outputCssVariables($attributes, $manifest, 'section-1', '', $this->getTestGlobalSettings());

		$this->assertStringContainsString('<style>', $result);
		$this->assertStringContainsString('--section-width:', $result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariables
	 * @covers ::variablesInner
	 */
	public function testOutputCssVariablesWithMultipleVariableKeys(): void
	{
		$this->setupHelpersCache();
		$this->mockOutputCssVariablesDependencies();

		$manifest = [
			'blockName' => 'box',
			'variables' => [
				'boxSpacing' => [
					[
						'variable' => [
							'box-margin' => '%value%',
							'box-padding' => '%value%',
						],
					],
				],
			],
		];

		$attributes = [
			'blockClass' => 'block-box',
			'boxSpacing' => '16px',
		];

		$result = $this->wrapper::outputCssVariables($attributes, $manifest, 'box-1', '', $this->getTestGlobalSettings());

		$this->assertStringContainsString('--box-margin: 16px;', $result);
		$this->assertStringContainsString('--box-padding: 16px;', $result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariables
	 * @covers ::setVariablesToBreakpoints
	 */
	public function testOutputCssVariablesWithEmptyVariableObjectSkips(): void
	{
		$this->setupHelpersCache();
		$this->mockOutputCssVariablesDependencies();

		$manifest = [
			'blockName' => 'skip-block',
			'variables' => [
				'skipAttr' => [],
			],
		];

		$attributes = [
			'blockClass' => 'block-skip',
			'skipAttr' => 'value',
		];

		$result = $this->wrapper::outputCssVariables($attributes, $manifest, 'skip-1', '', $this->getTestGlobalSettings());

		// Empty variable array should produce no CSS output.
		$this->assertSame('', $result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariables
	 * @covers ::variablesInner
	 */
	public function testOutputCssVariablesVariablesInnerSkipsListVariables(): void
	{
		$this->setupHelpersCache();
		$this->mockOutputCssVariablesDependencies();

		$manifest = [
			'blockName' => 'list-block',
			'variables' => [
				'listAttr' => [
					[
						// When 'variable' is a list (indexed array) instead of associative,
						// variablesInner should return empty output.
						'variable' => ['item1', 'item2'],
					],
				],
			],
		];

		$attributes = [
			'blockClass' => 'block-list',
			'listAttr' => 'something',
		];

		$result = $this->wrapper::outputCssVariables($attributes, $manifest, 'list-1', '', $this->getTestGlobalSettings());

		// List variables produce no CSS output.
		$this->assertSame('', $result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariablesInlineClean
	 */
	public function testOutputCssVariablesInlineCleanReturnsEmptyWhenNotGlobal(): void
	{
		$this->setupHelpersCache();

		Functions\when('sanitize_text_field')->returnArg();
		Functions\when('wp_unslash')->returnArg();
		Functions\when('wp_is_json_request')->justReturn(false);
		Functions\when('esc_html')->returnArg();

		// With getConfigOutputCssGlobally() returning false, should return empty string early.
		$result = $this->wrapper::outputCssVariablesInlineClean();

		$this->assertSame('', $result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariablesInline
	 */
	public function testOutputCssVariablesInlineWrapsInStyleTag(): void
	{
		$this->setupHelpersCache();

		Functions\when('sanitize_text_field')->returnArg();
		Functions\when('wp_unslash')->returnArg();
		Functions\when('wp_is_json_request')->justReturn(false);
		Functions\when('esc_html')->returnArg();

		$result = $this->wrapper::outputCssVariablesInline();

		$this->assertStringContainsString('<style', $result);
		$this->assertStringContainsString('</style>', $result);

		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariablesInline
	 */
	public function testOutputCssVariablesInlineContainsStyleIdAttribute(): void
	{
		$this->setupHelpersCache();

		Functions\when('sanitize_text_field')->returnArg();
		Functions\when('wp_unslash')->returnArg();
		Functions\when('wp_is_json_request')->justReturn(false);
		Functions\when('esc_html')->returnArg();

		$result = $this->wrapper::outputCssVariablesInline();

		// Should have a style tag with an id attribute.
		$this->assertMatchesRegularExpression("/<style id='[^']*'>/", $result);

		$this->clearHelpersCache();
	}

	/**
	 * Helper to call private static getCssVariablesTypeInline via reflection.
	 *
	 * @param string $name Output css selector name.
	 * @param array<mixed> $data Data prepared for checking.
	 * @param array<mixed> $manifest Component/block manifest data.
	 * @param string $unique Unique key.
	 *
	 * @return array<mixed>
	 */
	private function callGetCssVariablesTypeInline(string $name, array $data, array $manifest, string $unique): array
	{
		$method = new \ReflectionMethod(CssVariablesTraitWrapper::class, 'getCssVariablesTypeInline');
		$method->setAccessible(true);

		return $method->invoke(null, $name, $data, $manifest, $unique);
	}

	/**
	 * @covers ::getCssVariablesTypeInline
	 */
	public function testGetCssVariablesTypeInlineReturnsCorrectStructure(): void
	{
		Functions\when('esc_html')->returnArg();

		$data = [
			[
				'type' => 'min',
				'name' => 'default',
				'value' => 0,
				'variable' => ['--my-color: red;'],
			],
			[
				'type' => 'min',
				'name' => 'tablet',
				'value' => 768,
				'variable' => [],
			],
		];

		$manifest = ['blockName' => 'test-block'];

		$result = $this->callGetCssVariablesTypeInline('block-test', $data, $manifest, 'unique-abc');

		$this->assertIsArray($result);
		$this->assertSame('block-test', $result['name']);
		$this->assertSame('unique-abc', $result['unique']);
		$this->assertIsArray($result['variables']);
		$this->assertNotEmpty($result['variables']);

		// First variable should be the default breakpoint.
		$firstVar = $result['variables'][0];
		$this->assertSame('min', $firstVar['type']);
		$this->assertSame(0, $firstVar['value']);
		$this->assertStringContainsString('--my-color: red;', $firstVar['variable']);
	}

	/**
	 * @covers ::getCssVariablesTypeInline
	 */
	public function testGetCssVariablesTypeInlineWithEmptyVariablesReturnsEmpty(): void
	{
		Functions\when('esc_html')->returnArg();

		$data = [
			[
				'type' => 'min',
				'name' => 'default',
				'value' => 0,
				'variable' => [],
			],
		];

		$manifest = ['blockName' => 'test-block'];

		$result = $this->callGetCssVariablesTypeInline('block-test', $data, $manifest, 'unique-1');

		// No variables and no variablesCustom → empty array.
		$this->assertSame([], $result);
	}

	/**
	 * @covers ::getCssVariablesTypeInline
	 */
	public function testGetCssVariablesTypeInlineProcessesVariablesCustom(): void
	{
		Functions\when('esc_html')->returnArg();

		$data = [
			[
				'type' => 'min',
				'name' => 'default',
				'value' => 0,
				'variable' => [],
			],
		];

		$manifest = [
			'blockName' => 'test-block',
			'variablesCustom' => [
				'font-size: 16px',
				'line-height: 1.5',
			],
		];

		$result = $this->callGetCssVariablesTypeInline('block-test', $data, $manifest, 'unique-2');

		$this->assertIsArray($result);
		$this->assertNotEmpty($result['variables']);

		// variablesCustom should appear as a min/0 entry.
		$customVar = \end($result['variables']);
		$this->assertSame('min', $customVar['type']);
		$this->assertSame(0, $customVar['value']);
		$this->assertStringContainsString('font-size: 16px', $customVar['variable']);
		$this->assertStringContainsString('line-height: 1.5', $customVar['variable']);
	}

	/**
	 * @covers ::getCssVariablesTypeInline
	 */
	public function testGetCssVariablesTypeInlineWithMultipleBreakpoints(): void
	{
		Functions\when('esc_html')->returnArg();

		$data = [
			[
				'type' => 'min',
				'name' => 'default',
				'value' => 0,
				'variable' => ['--spacing: 10px;'],
			],
			[
				'type' => 'min',
				'name' => 'tablet',
				'value' => 768,
				'variable' => ['--spacing: 20px;'],
			],
			[
				'type' => 'max',
				'name' => 'default',
				'value' => 0,
				'variable' => [],
			],
			[
				'type' => 'max',
				'name' => 'desktop',
				'value' => 1200,
				'variable' => ['--spacing: 30px;'],
			],
		];

		$manifest = ['blockName' => 'test-block'];

		$result = $this->callGetCssVariablesTypeInline('block-test', $data, $manifest, 'unique-3');

		$this->assertCount(3, $result['variables']);

		// Verify breakpoint types and values.
		$types = \array_column($result['variables'], 'type');
		$this->assertContains('min', $types);
		$this->assertContains('max', $types);
	}

	/**
	 * @covers ::getCssVariablesTypeInline
	 */
	public function testGetCssVariablesTypeInlineWithBothVariablesAndCustom(): void
	{
		Functions\when('esc_html')->returnArg();

		$data = [
			[
				'type' => 'min',
				'name' => 'default',
				'value' => 0,
				'variable' => ['--bg-color: blue;'],
			],
		];

		$manifest = [
			'blockName' => 'test-block',
			'variablesCustom' => [
				'display: flex',
			],
		];

		$result = $this->callGetCssVariablesTypeInline('block-test', $data, $manifest, 'unique-4');

		// Should have both the breakpoint variable and the custom variable.
		$this->assertCount(2, $result['variables']);
		$this->assertStringContainsString('--bg-color: blue;', $result['variables'][0]['variable']);
		$this->assertStringContainsString('display: flex', $result['variables'][1]['variable']);
	}

	/**
	 * Helper to call private static prepareVariableData via reflection.
	 *
	 * @param array<string, mixed> $breakpoints Global breakpoints.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	private function callPrepareVariableData(array $breakpoints): array
	{
		$method = new \ReflectionMethod(CssVariablesTraitWrapper::class, 'prepareVariableData');
		$method->setAccessible(true);

		return $method->invoke(null, $breakpoints);
	}

	/**
	 * @covers ::prepareVariableData
	 */
	public function testPrepareVariableDataCreatesMinAndMaxBreakpoints(): void
	{
		$breakpoints = [
			'tablet' => 768,
			'desktop' => 1200,
		];

		$result = $this->callPrepareVariableData($breakpoints);

		// Should have min entries: default(0), desktop(768) and max entries: default(0), tablet(768).
		$this->assertIsArray($result);

		$minEntries = \array_filter($result, fn($item) => $item['type'] === 'min');
		$maxEntries = \array_filter($result, fn($item) => $item['type'] === 'max');

		$this->assertCount(2, $minEntries);
		$this->assertCount(2, $maxEntries);

		// Verify default min entry.
		$minDefault = \array_values($minEntries)[0];
		$this->assertSame('default', $minDefault['name']);
		$this->assertSame(0, $minDefault['value']);
		$this->assertSame('min', $minDefault['type']);
		$this->assertSame([], $minDefault['variable']);
	}

	/**
	 * @covers ::prepareVariableData
	 */
	public function testPrepareVariableDataWithSingleBreakpoint(): void
	{
		$breakpoints = [
			'mobile' => 480,
		];

		$result = $this->callPrepareVariableData($breakpoints);

		// 1 breakpoint: min has default(0), max has default(0).
		$minEntries = \array_filter($result, fn($item) => $item['type'] === 'min');
		$maxEntries = \array_filter($result, fn($item) => $item['type'] === 'max');

		$this->assertCount(1, $minEntries);
		$this->assertCount(1, $maxEntries);
	}

	/**
	 * @covers ::prepareVariableData
	 */
	public function testPrepareVariableDataWithThreeBreakpoints(): void
	{
		$breakpoints = [
			'mobile' => 480,
			'tablet' => 768,
			'desktop' => 1200,
		];

		$result = $this->callPrepareVariableData($breakpoints);

		$minEntries = \array_filter($result, fn($item) => $item['type'] === 'min');
		$maxEntries = \array_filter($result, fn($item) => $item['type'] === 'max');

		$this->assertCount(3, $minEntries);
		$this->assertCount(3, $maxEntries);

		// min default should have value 0.
		$firstMin = \array_values($minEntries)[0];
		$this->assertSame(0, $firstMin['value']);

		// max default should also have value 0.
		$firstMax = \array_values($maxEntries)[0];
		$this->assertSame(0, $firstMax['value']);
	}

	/**
	 * Helper to set Helpers::$styles array via reflection.
	 *
	 * @param array<mixed> $styles Styles array.
	 */
	private function setHelpersStyles(array $styles): void
	{
		$reflection = new \ReflectionClass(\EightshiftLibs\Helpers\Helpers::class);
		$stylesProperty = $reflection->getProperty('styles');
		$stylesProperty->setValue(null, $styles);
	}

	/**
	 * Helper to get Helpers::$styles array via reflection.
	 *
	 * @return array<mixed>
	 */
	private function getHelpersStyles(): array
	{
		$reflection = new \ReflectionClass(\EightshiftLibs\Helpers\Helpers::class);
		$stylesProperty = $reflection->getProperty('styles');

		return $stylesProperty->getValue(null);
	}

	/**
	 * Helper to clear Helpers::$styles array.
	 */
	private function clearHelpersStyles(): void
	{
		$this->setHelpersStyles([]);
	}

	/**
	 * @covers ::outputCssVariables
	 * @covers ::getCssVariablesTypeInline
	 */
	public function testOutputCssVariablesInlinePathReturnsEmptyAndSetsStyle(): void
	{
		$this->setupHelpersCache();
		$this->clearHelpersStyles();
		$this->mockOutputCssVariablesDependencies();

		// Redefine getConfigOutputCssGlobally to return true (bypasses getConfig static cache).
		\Patchwork\redefine(
			'EightshiftLibs\Helpers\Helpers::getConfigOutputCssGlobally',
			function () {
				return true;
			}
		);

		$manifest = [
			'blockName' => 'my-block',
			'variables' => [
				'myColor' => [
					[
						'variable' => [
							'my-color' => '%value%',
						],
					],
				],
			],
		];

		$attributes = [
			'blockClass' => 'block-my-block',
			'myColor' => '#FF0000',
		];

		$result = $this->wrapper::outputCssVariables($attributes, $manifest, 'unique-1', '', $this->getTestGlobalSettings());

		// Inline path returns empty string.
		$this->assertSame('', $result);

		// But styles should have been populated via Helpers::setStyle.
		$styles = $this->getHelpersStyles();
		$this->assertNotEmpty($styles);
		$this->assertSame('block-my-block', $styles[0]['name']);
		$this->assertSame('unique-1', $styles[0]['unique']);
		$this->assertNotEmpty($styles[0]['variables']);

		$this->clearHelpersStyles();
		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariables
	 * @covers ::getCssVariablesTypeInline
	 */
	public function testOutputCssVariablesInlinePathWithComponentClass(): void
	{
		$this->setupHelpersCache();
		$this->clearHelpersStyles();
		$this->mockOutputCssVariablesDependencies();

		\Patchwork\redefine(
			'EightshiftLibs\Helpers\Helpers::getConfigOutputCssGlobally',
			function () {
				return true;
			}
		);

		$manifest = [
			'componentClass' => 'comp-button',
			'blockName' => 'button',
			'variables' => [
				'btnSize' => [
					[
						'variable' => [
							'btn-size' => '%value%',
						],
					],
				],
			],
		];

		$attributes = [
			'blockClass' => 'block-button',
			'btnSize' => 'large',
		];

		$result = $this->wrapper::outputCssVariables($attributes, $manifest, 'btn-1', '', $this->getTestGlobalSettings());

		$this->assertSame('', $result);

		$styles = $this->getHelpersStyles();
		$this->assertNotEmpty($styles);
		// Should use componentClass as name.
		$this->assertSame('comp-button', $styles[0]['name']);

		$this->clearHelpersStyles();
		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariables
	 * @covers ::getCssVariablesTypeInline
	 */
	public function testOutputCssVariablesInlinePathWithCustomSelector(): void
	{
		$this->setupHelpersCache();
		$this->clearHelpersStyles();
		$this->mockOutputCssVariablesDependencies();

		\Patchwork\redefine(
			'EightshiftLibs\Helpers\Helpers::getConfigOutputCssGlobally',
			function () {
				return true;
			}
		);

		$manifest = [
			'blockName' => 'test-block',
			'variables' => [
				'testVar' => [
					[
						'variable' => [
							'test-var' => '%value%',
						],
					],
				],
			],
		];

		$attributes = [
			'blockClass' => 'block-test',
			'testVar' => '42px',
		];

		$result = $this->wrapper::outputCssVariables($attributes, $manifest, 'uid-1', '.custom-selector', $this->getTestGlobalSettings());

		$this->assertSame('', $result);

		$styles = $this->getHelpersStyles();
		$this->assertSame('.custom-selector', $styles[0]['name']);

		$this->clearHelpersStyles();
		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariables
	 * @covers ::getCssVariablesTypeInline
	 */
	public function testOutputCssVariablesInlinePathWithVariablesCustom(): void
	{
		$this->setupHelpersCache();
		$this->clearHelpersStyles();
		$this->mockOutputCssVariablesDependencies();

		\Patchwork\redefine(
			'EightshiftLibs\Helpers\Helpers::getConfigOutputCssGlobally',
			function () {
				return true;
			}
		);

		$manifest = [
			'blockName' => 'test-block',
			'variablesCustom' => [
				'font-size: 16px',
				'line-height: 1.5',
			],
		];

		$attributes = [
			'blockClass' => 'block-test',
		];

		$result = $this->wrapper::outputCssVariables($attributes, $manifest, 'uid-2', '', $this->getTestGlobalSettings());

		$this->assertSame('', $result);

		$styles = $this->getHelpersStyles();
		$this->assertNotEmpty($styles);

		// variablesCustom should produce a variable entry.
		$variables = $styles[0]['variables'] ?? [];
		$customFound = false;
		foreach ($variables as $var) {
			if (\str_contains($var['variable'], 'font-size: 16px')) {
				$customFound = true;
				break;
			}
		}
		$this->assertTrue($customFound, 'variablesCustom content should be in the style variables');

		$this->clearHelpersStyles();
		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariablesInlineClean
	 */
	public function testOutputCssVariablesInlineCleanProcessesStyles(): void
	{
		$this->setupHelpersCache();
		$this->mockOutputCssVariablesDependencies();

		\Patchwork\redefine(
			'EightshiftLibs\Helpers\Helpers::getConfigOutputCssGlobally',
			function () {
				return true;
			}
		);

		// Populate styles directly.
		$this->setHelpersStyles([
			[
				'name' => 'block-heading',
				'unique' => 'heading-1',
				'variables' => [
					[
						'type' => 'min',
						'variable' => '--heading-color: red;',
						'value' => 0,
					],
				],
			],
		]);

		$result = $this->wrapper::outputCssVariablesInlineClean($this->getTestGlobalSettings());

		$this->assertNotEmpty($result);
		$this->assertStringContainsString('block-heading', $result);
		$this->assertStringContainsString("data-id='heading-1'", $result);
		$this->assertStringContainsString('--heading-color: red;', $result);

		$this->clearHelpersStyles();
		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariablesInlineClean
	 */
	public function testOutputCssVariablesInlineCleanGeneratesMediaQueries(): void
	{
		$this->setupHelpersCache();
		$this->mockOutputCssVariablesDependencies();

		\Patchwork\redefine(
			'EightshiftLibs\Helpers\Helpers::getConfigOutputCssGlobally',
			function () {
				return true;
			}
		);

		$this->setHelpersStyles([
			[
				'name' => 'block-card',
				'unique' => 'card-1',
				'variables' => [
					[
						'type' => 'min',
						'variable' => '--card-width: 100%;',
						'value' => 0,
					],
					[
						'type' => 'min',
						'variable' => '--card-width: 50%;',
						'value' => 768,
					],
				],
			],
		]);

		$result = $this->wrapper::outputCssVariablesInlineClean($this->getTestGlobalSettings());

		$this->assertNotEmpty($result);
		// Default breakpoint (value=0) should have no media query.
		$this->assertStringContainsString('--card-width: 100%;', $result);
		// Tablet breakpoint should have media query.
		$this->assertStringContainsString('@media (min-width:768px)', $result);
		$this->assertStringContainsString('--card-width: 50%;', $result);

		$this->clearHelpersStyles();
		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariablesInlineClean
	 */
	public function testOutputCssVariablesInlineCleanWithMaxBreakpoint(): void
	{
		$this->setupHelpersCache();
		$this->mockOutputCssVariablesDependencies();

		\Patchwork\redefine(
			'EightshiftLibs\Helpers\Helpers::getConfigOutputCssGlobally',
			function () {
				return true;
			}
		);

		$this->setHelpersStyles([
			[
				'name' => 'block-hero',
				'unique' => 'hero-1',
				'variables' => [
					[
						'type' => 'max',
						'variable' => '--hero-height: 300px;',
						'value' => 1200,
					],
				],
			],
		]);

		$result = $this->wrapper::outputCssVariablesInlineClean($this->getTestGlobalSettings());

		$this->assertStringContainsString('@media (max-width:1200px)', $result);
		$this->assertStringContainsString('--hero-height: 300px;', $result);

		$this->clearHelpersStyles();
		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariablesInlineClean
	 */
	public function testOutputCssVariablesInlineCleanWithNoUniqueOmitsDataId(): void
	{
		$this->setupHelpersCache();
		$this->mockOutputCssVariablesDependencies();

		\Patchwork\redefine(
			'EightshiftLibs\Helpers\Helpers::getConfigOutputCssGlobally',
			function () {
				return true;
			}
		);

		$this->setHelpersStyles([
			[
				'name' => 'block-text',
				'unique' => '',
				'variables' => [
					[
						'type' => 'min',
						'variable' => '--text-size: 14px;',
						'value' => 0,
					],
				],
			],
		]);

		$result = $this->wrapper::outputCssVariablesInlineClean($this->getTestGlobalSettings());

		$this->assertStringContainsString('.block-text{', $result);
		$this->assertStringNotContainsString('data-id', $result);

		$this->clearHelpersStyles();
		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariablesInlineClean
	 */
	public function testOutputCssVariablesInlineCleanSkipsEmptyVariables(): void
	{
		$this->setupHelpersCache();
		$this->mockOutputCssVariablesDependencies();

		\Patchwork\redefine(
			'EightshiftLibs\Helpers\Helpers::getConfigOutputCssGlobally',
			function () {
				return true;
			}
		);

		$this->setHelpersStyles([
			[
				'name' => 'block-empty',
				'unique' => 'empty-1',
				'variables' => [],
			],
		]);

		$result = $this->wrapper::outputCssVariablesInlineClean($this->getTestGlobalSettings());

		// Should be empty or just whitespace + additional styles.
		$this->assertStringNotContainsString('block-empty', \trim($result));

		$this->clearHelpersStyles();
		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariablesInlineClean
	 */
	public function testOutputCssVariablesInlineCleanSkipsEmptyVariableEntry(): void
	{
		$this->setupHelpersCache();
		$this->mockOutputCssVariablesDependencies();

		\Patchwork\redefine(
			'EightshiftLibs\Helpers\Helpers::getConfigOutputCssGlobally',
			function () {
				return true;
			}
		);

		$this->setHelpersStyles([
			[
				'name' => 'block-skip',
				'unique' => 'skip-1',
				'variables' => [
					[
						'type' => 'min',
						'variable' => '',
						'value' => 0,
					],
				],
			],
		]);

		$result = $this->wrapper::outputCssVariablesInlineClean($this->getTestGlobalSettings());

		// Empty variable string → skipped.
		$this->assertStringNotContainsString('block-skip', \trim($result));

		$this->clearHelpersStyles();
		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariablesInlineClean
	 */
	public function testOutputCssVariablesInlineCleanWithOptimize(): void
	{
		$this->setupHelpersCache();
		$this->mockOutputCssVariablesDependencies();

		\Patchwork\redefine(
			'EightshiftLibs\Helpers\Helpers::getConfigOutputCssGlobally',
			function () {
				return true;
			}
		);

		\Patchwork\redefine(
			'EightshiftLibs\Helpers\Helpers::getConfigOutputCssOptimize',
			function () {
				return true;
			}
		);

		$this->setHelpersStyles([
			[
				'name' => 'block-opt',
				'unique' => 'opt-1',
				'variables' => [
					[
						'type' => 'min',
						'variable' => "--opt-color: blue;\n--opt-size: 12px;",
						'value' => 0,
					],
				],
			],
		]);

		$result = $this->wrapper::outputCssVariablesInlineClean($this->getTestGlobalSettings());

		// With optimize, newlines should be removed.
		$this->assertStringNotContainsString("\n", $result);
		$this->assertStringContainsString('--opt-color: blue;', $result);

		$this->clearHelpersStyles();
		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariablesInlineClean
	 */
	public function testOutputCssVariablesInlineCleanWithAdditionalStyles(): void
	{
		$this->setupHelpersCache();
		$this->mockOutputCssVariablesDependencies();

		\Patchwork\redefine(
			'EightshiftLibs\Helpers\Helpers::getConfigOutputCssGlobally',
			function () {
				return true;
			}
		);

		\Patchwork\redefine(
			'EightshiftLibs\Helpers\Helpers::getConfigOutputCssGloballyAdditionalStyles',
			function () {
				return ['body { margin: 0 }', '.container { max-width: 1200px }'];
			}
		);

		// Even without styles populated, the additional styles should appear.
		$this->setHelpersStyles([]);

		$result = $this->wrapper::outputCssVariablesInlineClean($this->getTestGlobalSettings());

		$this->assertStringContainsString('body { margin: 0 }', $result);
		$this->assertStringContainsString('.container { max-width: 1200px }', $result);

		$this->clearHelpersStyles();
		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariablesInlineClean
	 */
	public function testOutputCssVariablesInlineCleanWithMultipleStyles(): void
	{
		$this->setupHelpersCache();
		$this->mockOutputCssVariablesDependencies();

		\Patchwork\redefine(
			'EightshiftLibs\Helpers\Helpers::getConfigOutputCssGlobally',
			function () {
				return true;
			}
		);

		$this->setHelpersStyles([
			[
				'name' => 'block-a',
				'unique' => 'a-1',
				'variables' => [
					[
						'type' => 'min',
						'variable' => '--a-color: red;',
						'value' => 0,
					],
				],
			],
			[
				'name' => 'block-b',
				'unique' => 'b-1',
				'variables' => [
					[
						'type' => 'min',
						'variable' => '--b-color: blue;',
						'value' => 0,
					],
				],
			],
		]);

		$result = $this->wrapper::outputCssVariablesInlineClean($this->getTestGlobalSettings());

		$this->assertStringContainsString('block-a', $result);
		$this->assertStringContainsString('block-b', $result);
		$this->assertStringContainsString('--a-color: red;', $result);
		$this->assertStringContainsString('--b-color: blue;', $result);

		$this->clearHelpersStyles();
		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariablesInline
	 * @covers ::outputCssVariablesInlineClean
	 */
	public function testOutputCssVariablesInlineWithActualContent(): void
	{
		$this->setupHelpersCache();
		$this->mockOutputCssVariablesDependencies();

		\Patchwork\redefine(
			'EightshiftLibs\Helpers\Helpers::getConfigOutputCssGlobally',
			function () {
				return true;
			}
		);

		$this->setHelpersStyles([
			[
				'name' => 'block-heading',
				'unique' => 'heading-1',
				'variables' => [
					[
						'type' => 'min',
						'variable' => '--heading-size: 24px;',
						'value' => 0,
					],
				],
			],
		]);

		$result = $this->wrapper::outputCssVariablesInline($this->getTestGlobalSettings());

		// Should wrap in style tag with ID.
		$this->assertStringContainsString('<style', $result);
		$this->assertStringContainsString('</style>', $result);
		// Should contain actual CSS content.
		$this->assertStringContainsString('--heading-size: 24px;', $result);
		$this->assertStringContainsString('block-heading', $result);

		$this->clearHelpersStyles();
		$this->clearHelpersCache();
	}

	/**
	 * @covers ::outputCssVariablesInlineClean
	 */
	public function testOutputCssVariablesInlineCleanInJsonEditContextReturnsEmpty(): void
	{
		$this->setupHelpersCache();
		$this->mockOutputCssVariablesDependencies();

		// Even with globally=true, if JSON request + context=edit, should return empty.
		\Patchwork\redefine(
			'EightshiftLibs\Helpers\Helpers::getConfigOutputCssGlobally',
			function () {
				return true;
			}
		);

		Functions\when('wp_is_json_request')->justReturn(true);

		$_GET['context'] = 'edit';

		$this->setHelpersStyles([
			[
				'name' => 'block-test',
				'unique' => 'test-1',
				'variables' => [
					[
						'type' => 'min',
						'variable' => '--test: value;',
						'value' => 0,
					],
				],
			],
		]);

		$result = $this->wrapper::outputCssVariablesInlineClean($this->getTestGlobalSettings());

		$this->assertSame('', $result);

		unset($_GET['context']);
		$this->clearHelpersStyles();
		$this->clearHelpersCache();
	}

	/**
	 * Tests %attr-key% substitution without prefix attribute.
	 *
	 * @covers ::outputCssVariables
	 * @covers ::variablesInner
	 */
	public function testOutputCssVariablesWithAttrSubstitutionNoPrefix(): void
	{
		$this->setupHelpersCache();
		$this->mockOutputCssVariablesDependencies();

		$manifest = [
			'blockName' => 'my-block',
			'variables' => [
				'mySize' => [
					[
						'variable' => [
							'size' => '%attr-otherAttr%',
						],
					],
				],
			],
		];

		$attributes = [
			'blockClass' => 'block-my-block',
			'mySize' => '16px',
			'otherAttr' => '42px',
		];

		$result = $this->wrapper::outputCssVariables($attributes, $manifest, 'u1', '', $this->getTestGlobalSettings());

		$this->assertStringContainsString('<style>', $result);
		$this->assertStringContainsString('--size: 42px;', $result);

		$this->clearHelpersCache();
	}

	/**
	 * Tests %attr-key% substitution with prefix attribute.
	 *
	 * @covers ::outputCssVariables
	 * @covers ::variablesInner
	 */
	public function testOutputCssVariablesWithAttrSubstitutionWithPrefix(): void
	{
		$this->setupHelpersCache();
		$this->mockOutputCssVariablesDependencies();

		$manifest = [
			'blockName' => 'my-block',
			'variables' => [
				'myBlockSize' => [
					[
						'variable' => [
							'size' => '%attr-myBlockColor%',
						],
					],
				],
			],
		];

		$attributes = [
			'blockClass' => 'block-my-block',
			'prefix' => 'my',
			'myBlockSize' => '16px',
			'myColor' => 'blue',
		];

		$result = $this->wrapper::outputCssVariables($attributes, $manifest, 'u1', '', $this->getTestGlobalSettings());

		$this->assertStringContainsString('<style>', $result);

		$this->clearHelpersCache();
	}

	/**
	 * Tests outputCssVariablesGlobalClean with CSS optimization enabled.
	 *
	 * @covers ::outputCssVariablesGlobalClean
	 */
	public function testOutputCssVariablesGlobalCleanWithOptimize(): void
	{
		$this->setupHelpersCache(['outputCssOptimize' => true]);

		$globalSettings = [
			'globalVariables' => [
				'maxWidth' => '1200px',
				'baseFontSize' => '16px',
			],
		];

		$result = $this->wrapper::outputCssVariablesGlobalClean($globalSettings);

		// With optimization, newlines should be removed.
		$this->assertStringNotContainsString("\n", $result);
		$this->assertStringContainsString('--global-max-width: 1200px;', $result);

		$this->clearHelpersCache();
	}
}
