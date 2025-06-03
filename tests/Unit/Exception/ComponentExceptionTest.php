<?php

/**
 * Tests for ComponentException exception class.
 *
 * @package EightshiftLibs\Tests\Unit\Exception
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Exception;

use EightshiftLibs\Tests\BaseTestCase;
use EightshiftLibs\Exception\ComponentException;
use EightshiftLibs\Exception\GeneralExceptionInterface;
use InvalidArgumentException;
use Brain\Monkey\Functions;
use stdClass;

/**
 * Test case for ComponentException exception.
 *
 * @coversDefaultClass EightshiftLibs\Exception\ComponentException
 */
class ComponentExceptionTest extends BaseTestCase
{
	protected function setUp(): void
	{
		parent::setUp();

		// Mock WordPress functions
		Functions\when('esc_html__')->returnArg(1);
	}

	/**
	 * @covers ::throwNotStringOrArray
	 */
	public function testThrowNotStringOrArrayWithInteger(): void
	{
		$variable = 123;
		$exception = ComponentException::throwNotStringOrArray($variable);

		$this->assertInstanceOf(ComponentException::class, $exception);
		$this->assertInstanceOf(InvalidArgumentException::class, $exception);
		$this->assertInstanceOf(GeneralExceptionInterface::class, $exception);

		$expectedMessage = '123 variable is not a string or array but rather integer';
		$this->assertEquals($expectedMessage, $exception->getMessage());
	}

	/**
	 * @covers ::throwNotStringOrArray
	 */
	public function testThrowNotStringOrArrayWithFloat(): void
	{
		$variable = 45.67;
		$exception = ComponentException::throwNotStringOrArray($variable);

		$expectedMessage = '45.67 variable is not a string or array but rather double';
		$this->assertEquals($expectedMessage, $exception->getMessage());
	}

	/**
	 * @covers ::throwNotStringOrArray
	 */
	public function testThrowNotStringOrArrayWithBoolean(): void
	{
		$variable = true;
		$exception = ComponentException::throwNotStringOrArray($variable);

		$expectedMessage = '1 variable is not a string or array but rather boolean';
		$this->assertEquals($expectedMessage, $exception->getMessage());
	}

	/**
	 * @covers ::throwNotStringOrArray
	 */
	public function testThrowNotStringOrArrayWithNull(): void
	{
		$variable = null;
		$exception = ComponentException::throwNotStringOrArray($variable);

		$expectedMessage = ' variable is not a string or array but rather NULL';
		$this->assertEquals($expectedMessage, $exception->getMessage());
	}

	/**
	 * @covers ::throwNotStringOrArray
	 */
	public function testThrowNotStringOrArrayWithObject(): void
	{
		$variable = new stdClass();
		$exception = ComponentException::throwNotStringOrArray($variable);

		$this->assertInstanceOf(ComponentException::class, $exception);
		$this->assertInstanceOf(InvalidArgumentException::class, $exception);
		$this->assertInstanceOf(GeneralExceptionInterface::class, $exception);

		$expectedMessage = 'Object couldn\'t be converted to string. Please provide only string or array.';
		$this->assertEquals($expectedMessage, $exception->getMessage());
	}

	/**
	 * @covers ::throwNotStringOrArray
	 */
	public function testThrowNotStringOrArrayWithResource(): void
	{
		$variable = \fopen('php://memory', 'r');
		$exception = ComponentException::throwNotStringOrArray($variable);

		$this->assertStringContainsString('variable is not a string or array but rather resource', $exception->getMessage());

		\fclose($variable);
	}

	/**
	 * @covers ::throwNotStringOrArray
	 */
	public function testThrowNotStringOrArrayWithCallable(): void
	{
		$variable = function () {
			return 'test';
		};
		$exception = ComponentException::throwNotStringOrArray($variable);

		$expectedMessage = 'Object couldn\'t be converted to string. Please provide only string or array.';
		$this->assertEquals($expectedMessage, $exception->getMessage());
	}

	/**
	 * Test that exception inherits from proper parent classes.
	 * @covers ::throwNotStringOrArray
	 */
	public function testExceptionInheritance(): void
	{
		$exception = ComponentException::throwNotStringOrArray(123);

		$this->assertInstanceOf(InvalidArgumentException::class, $exception);
		$this->assertInstanceOf(GeneralExceptionInterface::class, $exception);

		// Verify it's throwable
		$this->expectException(ComponentException::class);
		throw $exception;
	}

	/**
	 * Test that different variable types produce different messages.
	 * @covers ::throwNotStringOrArray
	 */
	public function testDifferentVariableTypesProduceDifferentMessages(): void
	{
		$testCases = [
			[123, 'integer'],
			[45.67, 'double'],
			[true, 'boolean'],
			[false, 'boolean'],
			[null, 'NULL'],
		];

		foreach ($testCases as [$variable, $expectedType]) {
			$exception = ComponentException::throwNotStringOrArray($variable);
			$this->assertStringContainsString($expectedType, $exception->getMessage());
		}
	}

	/**
	 * Test object handling produces consistent message.
	 * @covers ::throwNotStringOrArray
	 */
	public function testObjectHandlingProducesConsistentMessage(): void
	{
		$objects = [
			new stdClass(),
			new \DateTime(),
			function () {
				return 'test';
			},
		];

		$expectedMessage = 'Object couldn\'t be converted to string. Please provide only string or array.';

		foreach ($objects as $object) {
			$exception = ComponentException::throwNotStringOrArray($object);
			$this->assertEquals($expectedMessage, $exception->getMessage());
		}
	}
}
