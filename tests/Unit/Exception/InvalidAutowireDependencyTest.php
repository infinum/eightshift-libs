<?php

/**
 * Tests for InvalidAutowireDependency exception class.
 *
 * @package EightshiftLibs\Tests\Unit\Exception
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Exception;

use EightshiftLibs\Tests\BaseTestCase;
use EightshiftLibs\Exception\InvalidAutowireDependency;
use EightshiftLibs\Exception\GeneralExceptionInterface;
use InvalidArgumentException;

/**
 * Test case for InvalidAutowireDependency exception.
 *
 * @coversDefaultClass EightshiftLibs\Exception\InvalidAutowireDependency
 */
class InvalidAutowireDependencyTest extends BaseTestCase
{
	/**
	 * @covers ::throwUnableToFindClass
	 */
	public function testThrowUnableToFindClassMessage(): void
	{
		$className = 'EmailService';
		$interfaceName = 'MailerInterface';
		$exception = InvalidAutowireDependency::throwUnableToFindClass($className, $interfaceName);

		$this->assertInstanceOf(InvalidAutowireDependency::class, $exception);
		$this->assertInstanceOf(InvalidArgumentException::class, $exception);
		$this->assertInstanceOf(GeneralExceptionInterface::class, $exception);

		$message = $exception->getMessage();
		$this->assertStringContainsString('Unable to find "EmailService" class', $message);
		$this->assertStringContainsString('implements MailerInterface', $message);
		$this->assertStringContainsString('looking in $filenameIndex', $message);
		$this->assertStringContainsString('variable name in __construct()', $message);
		$this->assertStringContainsString('https://eightshift.com/docs/basics/autowiring', $message);
	}

	/**
	 * @covers ::throwUnableToFindClass
	 */
	public function testThrowUnableToFindClassWithEmptyValues(): void
	{
		$className = '';
		$interfaceName = '';
		$exception = InvalidAutowireDependency::throwUnableToFindClass($className, $interfaceName);

		$message = $exception->getMessage();
		$this->assertStringContainsString('Unable to find "" class', $message);
		$this->assertStringContainsString('implements ', $message);
	}

	/**
	 * Test that exception inherits from proper parent classes.
	 * @covers ::throwUnableToFindClass
	 */
	public function testExceptionInheritance(): void
	{
		$exception = InvalidAutowireDependency::throwUnableToFindClass('TestClass', 'TestInterface');

		$this->assertInstanceOf(InvalidArgumentException::class, $exception);
		$this->assertInstanceOf(GeneralExceptionInterface::class, $exception);

		// Verify it's throwable
		$this->expectException(InvalidAutowireDependency::class);
		throw $exception;
	}

	/**
	 * Test with real-world interface and class names.
	 * @covers ::throwUnableToFindClass
	 */
	public function testRealWorldScenarios(): void
	{
		$scenarios = [
			['DatabaseService', 'DatabaseInterface'],
			['CacheService', 'CacheInterface'],
			['LoggerService', 'LoggerInterface'],
			['EmailService', 'MailerInterface'],
		];

		foreach ($scenarios as [$className, $interfaceName]) {
			$exception = InvalidAutowireDependency::throwUnableToFindClass($className, $interfaceName);
			$message = $exception->getMessage();

			$this->assertStringContainsString($className, $message);
			$this->assertStringContainsString($interfaceName, $message);
			$this->assertStringContainsString('autowiring', $message);
		}
	}

	/**
	 * @covers ::throwMoreThanOneClassFound
	 */
	public function testThrowMoreThanOneClassFoundMessage(): void
	{
		$className = 'LoggerService';
		$interfaceName = 'LoggerInterface';
		$exception = InvalidAutowireDependency::throwMoreThanOneClassFound($className, $interfaceName);

		$this->assertInstanceOf(InvalidAutowireDependency::class, $exception);
		$this->assertInstanceOf(InvalidArgumentException::class, $exception);
		$this->assertInstanceOf(GeneralExceptionInterface::class, $exception);

		$message = $exception->getMessage();
		$this->assertStringContainsString('Found more than 1 class called "LoggerService"', $message);
		$this->assertStringContainsString('implements LoggerInterface interface', $message);
		$this->assertStringContainsString('manually define dependencies', $message);
		$this->assertStringContainsString('https://eightshift.com/docs/basics/autowiring', $message);
	}

	/**
	 * @covers ::throwMoreThanOneClassFound
	 */
	public function testThrowMoreThanOneClassFoundWithEmptyValues(): void
	{
		$exception = InvalidAutowireDependency::throwMoreThanOneClassFound('', '');

		$message = $exception->getMessage();
		$this->assertStringContainsString('Found more than 1 class called ""', $message);
	}

	/**
	 * @covers ::throwMoreThanOneClassFound
	 */
	public function testThrowMoreThanOneClassFoundIsThrowable(): void
	{
		$exception = InvalidAutowireDependency::throwMoreThanOneClassFound('SomeClass', 'SomeInterface');

		$this->expectException(InvalidAutowireDependency::class);
		throw $exception;
	}

	/**
	 * @covers ::throwPrimitiveDependencyFound
	 */
	public function testThrowPrimitiveDependencyFoundMessage(): void
	{
		$className = 'NotificationService';
		$param = '$apiKey';
		$exception = InvalidAutowireDependency::throwPrimitiveDependencyFound($className, $param);

		$this->assertInstanceOf(InvalidAutowireDependency::class, $exception);
		$this->assertInstanceOf(InvalidArgumentException::class, $exception);
		$this->assertInstanceOf(GeneralExceptionInterface::class, $exception);

		$message = $exception->getMessage();
		$this->assertStringContainsString('primitive dependency for NotificationService', $message);
		$this->assertStringContainsString('param $apiKey', $message);
		$this->assertStringContainsString('Autowire is unable to figure out', $message);
		$this->assertStringContainsString('https://eightshift.com/docs/basics/autowiring', $message);
	}

	/**
	 * @covers ::throwPrimitiveDependencyFound
	 */
	public function testThrowPrimitiveDependencyFoundWithEmptyValues(): void
	{
		$exception = InvalidAutowireDependency::throwPrimitiveDependencyFound('', '');

		$message = $exception->getMessage();
		$this->assertStringContainsString('primitive dependency for ', $message);
		$this->assertStringContainsString('param ', $message);
	}

	/**
	 * @covers ::throwPrimitiveDependencyFound
	 */
	public function testThrowPrimitiveDependencyFoundIsThrowable(): void
	{
		$exception = InvalidAutowireDependency::throwPrimitiveDependencyFound('MyClass', '$myParam');

		$this->expectException(InvalidAutowireDependency::class);
		throw $exception;
	}
}
