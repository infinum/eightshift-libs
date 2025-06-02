<?php

/**
 * Comprehensive tests for SelectorsTrait helper methods.
 *
 * @package EightshiftLibs\Tests\Unit\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Helpers;

use EightshiftLibs\Tests\BaseTestCase;
use EightshiftLibs\Helpers\SelectorsTrait;
use EightshiftLibs\Helpers\DeprecatedTrait;
use EightshiftLibs\Exception\ComponentException;
use Brain\Monkey\Functions;
use stdClass;

/**
 * Wrapper class to test SelectorsTrait methods without conflicts.
 */
class SelectorsTraitWrapper
{
	use SelectorsTrait;
	use DeprecatedTrait; // For classnames method
}

/**
 * Comprehensive test case for SelectorsTrait utility methods.
 *
 * @coversDefaultClass EightshiftLibs\Helpers\SelectorsTrait
 */
class SelectorsTraitTest extends BaseTestCase
{
	private SelectorsTraitWrapper $wrapper;

	protected function setUp(): void
	{
		parent::setUp();
		$this->wrapper = new SelectorsTraitWrapper();

		// Mock WordPress functions
		Functions\when('esc_html__')->returnArg(1);
	}

	/**
	 * @covers ::selector
	 */
	public function testSelectorWithTruthyCondition(): void
	{
		$result = $this->wrapper::selector(true, 'block', 'element', 'modifier');
		$this->assertEquals('block__element--modifier', $result);
	}

	/**
	 * @covers ::selector
	 */
	public function testSelectorWithFalsyCondition(): void
	{
		$result = $this->wrapper::selector(false, 'block', 'element', 'modifier');
		$this->assertEquals('', $result);
	}

	/**
	 * @covers ::selector
	 */
	public function testSelectorWithStringCondition(): void
	{
		$result = $this->wrapper::selector('some-value', 'block');
		$this->assertEquals('block', $result);
	}

	/**
	 * @covers ::selector
	 */
	public function testSelectorWithArrayCondition(): void
	{
		$result = $this->wrapper::selector(['item'], 'block', 'element');
		$this->assertEquals('block__element', $result);
	}

	/**
	 * @covers ::selector
	 */
	public function testSelectorWithEmptyStringCondition(): void
	{
		$result = $this->wrapper::selector('', 'block', 'element', 'modifier');
		$this->assertEquals('', $result);
	}

	/**
	 * @covers ::selector
	 */
	public function testSelectorWithZeroCondition(): void
	{
		$result = $this->wrapper::selector(0, 'block', 'element', 'modifier');
		$this->assertEquals('', $result);
	}

	/**
	 * @covers ::selector
	 */
	public function testSelectorWithNullCondition(): void
	{
		$result = $this->wrapper::selector(null, 'block', 'element', 'modifier');
		$this->assertEquals('', $result);
	}

	/**
	 * @covers ::bem
	 */
	public function testBemWithAllParts(): void
	{
		$result = $this->wrapper::bem('block', 'element', 'modifier');
		$this->assertEquals('block__element--modifier', $result);
	}

	/**
	 * @covers ::bem
	 */
	public function testBemWithBlockOnly(): void
	{
		$result = $this->wrapper::bem('block');
		$this->assertEquals('block', $result);
	}

	/**
	 * @covers ::bem
	 */
	public function testBemWithBlockAndElement(): void
	{
		$result = $this->wrapper::bem('block', 'element');
		$this->assertEquals('block__element', $result);
	}

	/**
	 * @covers ::bem
	 */
	public function testBemWithBlockAndModifier(): void
	{
		$result = $this->wrapper::bem('block', '', 'modifier');
		$this->assertEquals('block--modifier', $result);
	}

	/**
	 * @covers ::bem
	 */
	public function testBemWithWhitespace(): void
	{
		$result = $this->wrapper::bem('  block  ', '  element  ', '  modifier  ');
		$this->assertEquals('block__element--modifier', $result);
	}

	/**
	 * @covers ::bem
	 */
	public function testBemWithEmptyElement(): void
	{
		$result = $this->wrapper::bem('block', '', 'modifier');
		$this->assertEquals('block--modifier', $result);
	}

	/**
	 * @covers ::bem
	 */
	public function testBemWithEmptyModifier(): void
	{
		$result = $this->wrapper::bem('block', 'element', '');
		$this->assertEquals('block__element', $result);
	}

	/**
	 * @covers ::bem
	 */
	public function testBemWithAllEmptyExceptBlock(): void
	{
		$result = $this->wrapper::bem('block', '', '');
		$this->assertEquals('block', $result);
	}

	/**
	 * @covers ::responsiveSelectors
	 */
	public function testResponsiveSelectorsWithUsedModifier(): void
	{
		$items = [
			'mobile' => '12',
			'tablet' => '6',
			'desktop' => '4'
		];

		$result = $this->wrapper::responsiveSelectors($items, 'width', 'block-column', true);
		$this->assertEquals('block-column__width-mobile--12 block-column__width-tablet--6 block-column__width-desktop--4', $result);
	}

	/**
	 * @covers ::responsiveSelectors
	 */
	public function testResponsiveSelectorsWithoutModifier(): void
	{
		$items = [
			'mobile' => true,
			'tablet' => true,
			'desktop' => false
		];

		$result = $this->wrapper::responsiveSelectors($items, 'visibility', 'block-element', false);
		$this->assertEquals('block-element__visibility-mobile block-element__visibility-tablet', $result);
	}

	/**
	 * @covers ::responsiveSelectors
	 */
	public function testResponsiveSelectorsWithEmptyItems(): void
	{
		$result = $this->wrapper::responsiveSelectors([], 'width', 'block-column');
		$this->assertEquals('', $result);
	}

	/**
	 * @covers ::responsiveSelectors
	 */
	public function testResponsiveSelectorsWithFilteredValues(): void
	{
		$items = [
			'mobile' => '12',
			'tablet' => '',      // Should be filtered out
			'desktop' => false,  // Should be filtered out
			'large' => ['array'], // Should be filtered out
			'xlarge' => '3'
		];

		$result = $this->wrapper::responsiveSelectors($items, 'width', 'block-column');
		$this->assertEquals('block-column__width-mobile--12 block-column__width-xlarge--3', $result);
	}

	/**
	 * @covers ::responsiveSelectors
	 */
	public function testResponsiveSelectorsWithNumericKeys(): void
	{
		$items = [
			0 => 'first',
			1 => 'second',
			2 => 'third'
		];

		$result = $this->wrapper::responsiveSelectors($items, 'order', 'block-item');
		$this->assertEquals('block-item__order-0--first block-item__order-1--second block-item__order-2--third', $result);
	}

	/**
	 * @covers ::ensureString
	 */
	public function testEnsureStringWithString(): void
	{
		$result = $this->wrapper::ensureString('test string');
		$this->assertEquals('test string', $result);
	}

	/**
	 * @covers ::ensureString
	 */
	public function testEnsureStringWithEmptyString(): void
	{
		$result = $this->wrapper::ensureString('');
		$this->assertEquals('', $result);
	}

	/**
	 * @covers ::ensureString
	 */
	public function testEnsureStringWithEmptyArray(): void
	{
		$result = $this->wrapper::ensureString([]);
		$this->assertEquals('', $result);
	}

	/**
	 * @covers ::ensureString
	 */
	public function testEnsureStringWithSequentialArray(): void
	{
		$result = $this->wrapper::ensureString(['first', 'second', 'third']);
		$this->assertEquals('firstsecondthird', $result);
	}

	/**
	 * @covers ::ensureString
	 */
	public function testEnsureStringWithAssociativeArray(): void
	{
		$input = [
			'data-id' => '123',
			'data-name' => 'test'
		];

		$result = $this->wrapper::ensureString($input);
		$this->assertEquals('data-id="123" data-name="test"', $result);
	}

	/**
	 * @covers ::ensureString
	 */
	public function testEnsureStringWithAssociativeArraySpecialChars(): void
	{
		$input = [
			'data-content' => 'Test "quotes" & <tags>',
			'data-value' => "Single'quote"
		];

		$result = $this->wrapper::ensureString($input);
		$this->assertStringContainsString('data-content="Test &quot;quotes&quot; &amp; &lt;tags&gt;"', $result);
		$this->assertStringContainsString('data-value="Single&#039;quote"', $result);
	}

	/**
	 * @covers ::ensureString
	 */
	public function testEnsureStringWithInteger(): void
	{
		$this->expectException(ComponentException::class);
		$this->expectExceptionMessage('123 variable is not a string or array but rather integer');
		$this->wrapper::ensureString(123);
	}

	/**
	 * @covers ::ensureString
	 */
	public function testEnsureStringWithFloat(): void
	{
		$this->expectException(ComponentException::class);
		$this->expectExceptionMessage('45.67 variable is not a string or array but rather double');
		$this->wrapper::ensureString(45.67);
	}

	/**
	 * @covers ::ensureString
	 */
	public function testEnsureStringWithBoolean(): void
	{
		$this->expectException(ComponentException::class);
		$this->expectExceptionMessage('1 variable is not a string or array but rather boolean');
		$this->wrapper::ensureString(true);
	}

	/**
	 * @covers ::ensureString
	 */
	public function testEnsureStringWithNull(): void
	{
		$this->expectException(ComponentException::class);
		$this->expectExceptionMessage(' variable is not a string or array but rather NULL');
		$this->wrapper::ensureString(null);
	}

	/**
	 * @covers ::ensureString
	 */
	public function testEnsureStringWithObject(): void
	{
		$this->expectException(ComponentException::class);
		$this->expectExceptionMessage('Object couldn\'t be converted to string. Please provide only string or array.');
		$this->wrapper::ensureString(new stdClass());
	}

	/**
	 * @covers ::clsx
	 */
	public function testClsxWithValidClasses(): void
	{
		$classes = ['class1', 'class2', 'class3'];
		$result = $this->wrapper::clsx($classes);
		$this->assertEquals('class1 class2 class3', $result);
	}

	/**
	 * @covers ::clsx
	 */
	public function testClsxWithEmptyArray(): void
	{
		$result = $this->wrapper::clsx([]);
		$this->assertEquals('', $result);
	}

	/**
	 * @covers ::clsx
	 */
	public function testClsxWithFilteredValues(): void
	{
		$classes = ['class1', '', 'class2', null, 'class3', false, 'class4'];
		$result = $this->wrapper::clsx($classes);
		$this->assertEquals('class1 class2 class3 class4', $result);
	}

	/**
	 * @covers ::clsx
	 */
	public function testClsxWithOnlyEmptyValues(): void
	{
		$classes = ['', null, false];
		$result = $this->wrapper::clsx($classes);
		$this->assertEquals('', $result);
	}

	/**
	 * @covers ::clsx
	 */
	public function testClsxWithWhitespaceClasses(): void
	{
		$classes = ['  class1  ', 'class2', '  class3  '];
		$result = $this->wrapper::clsx($classes);
		$this->assertEquals('  class1   class2   class3  ', $result);
	}

	/**
	 * Test bem method with complex real-world scenarios.
	 * @covers ::bem
	 */
	public function testBemRealWorldScenarios(): void
	{
		// Button component
		$this->assertEquals('btn__icon--large', $this->wrapper::bem('btn', 'icon', 'large'));

		// Card component
		$this->assertEquals('card__header--featured', $this->wrapper::bem('card', 'header', 'featured'));

		// Navigation component
		$this->assertEquals('nav__item--active', $this->wrapper::bem('nav', 'item', 'active'));

		// Form component
		$this->assertEquals('form__input--error', $this->wrapper::bem('form', 'input', 'error'));
	}

	/**
	 * Test selector method with various condition types.
	 * @covers ::selector
	 */
	public function testSelectorWithDifferentConditionTypes(): void
	{
		// String conditions
		$this->assertEquals('block', $this->wrapper::selector('active', 'block'));
		$this->assertEquals('', $this->wrapper::selector('', 'block'));

		// Numeric conditions
		$this->assertEquals('block', $this->wrapper::selector(1, 'block'));
		$this->assertEquals('', $this->wrapper::selector(0, 'block'));

		// Array conditions
		$this->assertEquals('block', $this->wrapper::selector(['item'], 'block'));
		$this->assertEquals('', $this->wrapper::selector([], 'block'));

		// Boolean conditions
		$this->assertEquals('block', $this->wrapper::selector(true, 'block'));
		$this->assertEquals('', $this->wrapper::selector(false, 'block'));
	}

	/**
	 * Test responsiveSelectors with edge cases.
	 * @covers ::responsiveSelectors
	 */
	public function testResponsiveSelectorsEdgeCases(): void
	{
		// All values filtered out
		$items = ['mobile' => '', 'tablet' => false, 'desktop' => []];
		$result = $this->wrapper::responsiveSelectors($items, 'width', 'block');
		$this->assertEquals('', $result);

		// Mixed valid and invalid values
		$items = ['mobile' => 'auto', 'tablet' => '', 'desktop' => '50%'];
		$result = $this->wrapper::responsiveSelectors($items, 'width', 'block');
		$this->assertEquals('block__width-mobile--auto block__width-desktop--50%', $result);

		// Zero values (should be included)
		$items = ['mobile' => '0', 'tablet' => 0];
		$result = $this->wrapper::responsiveSelectors($items, 'margin', 'block');
		$this->assertEquals('block__margin-mobile--0 block__margin-tablet--0', $result);
	}

	/**
	 * Test ensureString with various array types.
	 * @covers ::ensureString
	 */
	public function testEnsureStringArrayTypes(): void
	{
		// Numeric indexed array (sequential)
		$result = $this->wrapper::ensureString(['a', 'b', 'c']);
		$this->assertEquals('abc', $result);

		// String indexed array (associative)
		$result = $this->wrapper::ensureString(['key' => 'value']);
		$this->assertEquals('key="value"', $result);

		// Mixed array (associative due to string keys)
		$result = $this->wrapper::ensureString([0 => 'first', 'key' => 'second']);
		$this->assertStringContainsString('0="first"', $result);
		$this->assertStringContainsString('key="second"', $result);

		// Non-sequential numeric keys (associative)
		$result = $this->wrapper::ensureString([1 => 'first', 3 => 'second']);
		$this->assertStringContainsString('1="first"', $result);
		$this->assertStringContainsString('3="second"', $result);
	}
}
