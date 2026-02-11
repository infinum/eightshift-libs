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
}
