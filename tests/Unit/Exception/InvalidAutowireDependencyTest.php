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
}
