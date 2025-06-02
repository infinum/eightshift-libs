<?php

/**
 * Tests for InvalidBlock exception class.
 *
 * @package EightshiftLibs\Tests\Unit\Exception
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Exception;

use EightshiftLibs\Tests\BaseTestCase;
use EightshiftLibs\Exception\InvalidBlock;
use EightshiftLibs\Exception\GeneralExceptionInterface;
use InvalidArgumentException;
use Brain\Monkey\Functions;

/**
 * Test case for InvalidBlock exception.
 *
 * @coversDefaultClass EightshiftLibs\Exception\InvalidBlock
 */
class InvalidBlockTest extends BaseTestCase
{
	protected function setUp(): void
	{
		parent::setUp();

		// Mock WordPress functions
		Functions\when('esc_html__')->returnArg(1);
	}

	/**
	 * @covers ::wrongComponentNameException
	 */
	public function testWrongComponentNameExceptionMessage(): void
	{
		$name = 'MyCustomBlock';
		$componentName = 'NonExistentComponent';
		$exception = InvalidBlock::wrongComponentNameException($name, $componentName);

		$this->assertInstanceOf(InvalidBlock::class, $exception);
		$this->assertInstanceOf(InvalidArgumentException::class, $exception);
		$this->assertInstanceOf(GeneralExceptionInterface::class, $exception);

		$expectedMessage = 'Component specified in MyCustomBlock manifest doesn\'t exist in your components list.
				Please check if you project has NonExistentComponent component.';
		$this->assertEquals($expectedMessage, $exception->getMessage());
	}

	/**
	 * @covers ::wrongComponentNameException
	 */
	public function testWrongComponentNameExceptionWithEmptyNames(): void
	{
		$name = '';
		$componentName = '';
		$exception = InvalidBlock::wrongComponentNameException($name, $componentName);

		$expectedMessage = 'Component specified in  manifest doesn\'t exist in your components list.
				Please check if you project has  component.';
		$this->assertEquals($expectedMessage, $exception->getMessage());
	}

	/**
	 * @covers ::wrongComponentNameException
	 */
	public function testWrongComponentNameExceptionWithSpecialCharacters(): void
	{
		$name = 'Block-With_Special123';
		$componentName = 'Component.Name-Special_123';
		$exception = InvalidBlock::wrongComponentNameException($name, $componentName);

		$expectedMessage = 'Component specified in Block-With_Special123 manifest doesn\'t exist in your components list.
				Please check if you project has Component.Name-Special_123 component.';
		$this->assertEquals($expectedMessage, $exception->getMessage());
	}

	/**
	 * @covers ::missingItemException
	 */
	public function testMissingItemExceptionMessage(): void
	{
		$name = 'ButtonBlock';
		$type = 'component';
		$exception = InvalidBlock::missingItemException($name, $type);

		$this->assertInstanceOf(InvalidBlock::class, $exception);
		$this->assertInstanceOf(InvalidArgumentException::class, $exception);
		$this->assertInstanceOf(GeneralExceptionInterface::class, $exception);

		$expectedMessage = 'Trying to get ButtonBlock component. Please check if it exists in the project.';
		$this->assertEquals($expectedMessage, $exception->getMessage());
	}

	/**
	 * @covers ::missingItemException
	 */
	public function testMissingItemExceptionWithDifferentTypes(): void
	{
		$testCases = [
			['HeaderBlock', 'block'],
			['CardComponent', 'variation'],
			['FooterWrapper', 'wrapper'],
			['CustomElement', 'template'],
		];

		foreach ($testCases as [$name, $type]) {
			$exception = InvalidBlock::missingItemException($name, $type);
			$expectedMessage = "Trying to get {$name} {$type}. Please check if it exists in the project.";
			$this->assertEquals($expectedMessage, $exception->getMessage());
		}
	}

	/**
	 * @covers ::missingItemException
	 */
	public function testMissingItemExceptionWithEmptyValues(): void
	{
		$name = '';
		$type = '';
		$exception = InvalidBlock::missingItemException($name, $type);

		$expectedMessage = 'Trying to get  . Please check if it exists in the project.';
		$this->assertEquals($expectedMessage, $exception->getMessage());
	}

	/**
	 * @covers ::missingItemException
	 */
	public function testMissingItemExceptionWithNumericValues(): void
	{
		$name = '123Block';
		$type = 'type456';
		$exception = InvalidBlock::missingItemException($name, $type);

		$expectedMessage = 'Trying to get 123Block type456. Please check if it exists in the project.';
		$this->assertEquals($expectedMessage, $exception->getMessage());
	}

	/**
	 * Test that exception inherits from proper parent classes.
	 * @covers ::missingItemException
	 */
	public function testExceptionInheritance(): void
	{
		$exception = InvalidBlock::missingItemException('TestBlock', 'block');

		$this->assertInstanceOf(InvalidArgumentException::class, $exception);
		$this->assertInstanceOf(GeneralExceptionInterface::class, $exception);

		// Verify it's throwable
		$this->expectException(InvalidBlock::class);
		throw $exception;
	}

	/**
	 * Test static factory methods return proper instance.
	 * @covers ::wrongComponentNameException
	 * @covers ::missingItemException
	 */
	public function testStaticFactoryMethodsReturnProperInstance(): void
	{
		$wrongComponent = InvalidBlock::wrongComponentNameException('Block1', 'Component1');
		$missingItem = InvalidBlock::missingItemException('Block2', 'component');

		$this->assertInstanceOf(InvalidBlock::class, $wrongComponent);
		$this->assertInstanceOf(InvalidBlock::class, $missingItem);

		// Each should be a different instance
		$this->assertNotSame($wrongComponent, $missingItem);
	}

	/**
	 * Test that messages are properly formatted and contain expected content.
	 * @covers ::wrongComponentNameException
	 * @covers ::missingItemException
	 */
	public function testMessageFormattingContainsExpectedContent(): void
	{
		$wrongComponent = InvalidBlock::wrongComponentNameException('TestBlock', 'TestComponent');
		$missingItem = InvalidBlock::missingItemException('TestBlock', 'block');

		// wrongComponentNameException should contain both names and guidance
		$this->assertStringContainsString('TestBlock', $wrongComponent->getMessage());
		$this->assertStringContainsString('TestComponent', $wrongComponent->getMessage());
		$this->assertStringContainsString('manifest', $wrongComponent->getMessage());
		$this->assertStringContainsString('components list', $wrongComponent->getMessage());

		// missingItemException should contain both name and type
		$this->assertStringContainsString('TestBlock', $missingItem->getMessage());
		$this->assertStringContainsString('block', $missingItem->getMessage());
		$this->assertStringContainsString('Trying to get', $missingItem->getMessage());
		$this->assertStringContainsString('check if it exists', $missingItem->getMessage());
	}

	/**
	 * Test exception messages with real-world WordPress block scenarios.
	 * @covers ::wrongComponentNameException
	 */
	public function testRealWorldBlockScenarios(): void
	{
		// Common WordPress block types
		$blockTypes = ['core/paragraph', 'core/heading', 'acf/custom-field', 'custom/hero-banner'];
		$componentTypes = ['button', 'card', 'hero', 'navigation'];

		foreach ($blockTypes as $blockName) {
			foreach ($componentTypes as $componentName) {
				$exception = InvalidBlock::wrongComponentNameException($blockName, $componentName);
				$this->assertStringContainsString($blockName, $exception->getMessage());
				$this->assertStringContainsString($componentName, $exception->getMessage());
			}
		}
	}
}
