<?php

/**
 * Tests for NonPsr4CompliantClass exception class.
 *
 * @package EightshiftLibs\Tests\Unit\Exception
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Exception;

use EightshiftLibs\Tests\BaseTestCase;
use EightshiftLibs\Exception\NonPsr4CompliantClass;
use EightshiftLibs\Exception\GeneralExceptionInterface;
use InvalidArgumentException;

/**
 * Test case for NonPsr4CompliantClass exception.
 *
 * @coversDefaultClass EightshiftLibs\Exception\NonPsr4CompliantClass
 */
class NonPsr4CompliantClassTest extends BaseTestCase
{
	/**
	 * @covers ::throwInvalidNamespace
	 */
	public function testThrowInvalidNamespaceMessage(): void
	{
		$className = 'MyApp\\Controllers\\HomeController';
		$exception = NonPsr4CompliantClass::throwInvalidNamespace($className);

		$this->assertInstanceOf(NonPsr4CompliantClass::class, $exception);
		$this->assertInstanceOf(InvalidArgumentException::class, $exception);
		$this->assertInstanceOf(GeneralExceptionInterface::class, $exception);

		$expectedMessage = 'Unable to autowire MyApp\\Controllers\\HomeController. Please check if the namespace is PSR-4 compliant (i.e. it needs to match the folder structure).
				See: https://www.php-fig.org/psr/psr-4/#3-examples';
		$this->assertEquals($expectedMessage, $exception->getMessage());
	}

	/**
	 * @covers ::throwInvalidNamespace
	 */
	public function testThrowInvalidNamespaceWithEmptyClassName(): void
	{
		$className = '';
		$exception = NonPsr4CompliantClass::throwInvalidNamespace($className);

		$expectedMessage = 'Unable to autowire . Please check if the namespace is PSR-4 compliant (i.e. it needs to match the folder structure).
				See: https://www.php-fig.org/psr/psr-4/#3-examples';
		$this->assertEquals($expectedMessage, $exception->getMessage());
	}

	/**
	 * @covers ::throwInvalidNamespace
	 */
	public function testThrowInvalidNamespaceWithSimpleClassName(): void
	{
		$className = 'SimpleClass';
		$exception = NonPsr4CompliantClass::throwInvalidNamespace($className);

		$expectedMessage = 'Unable to autowire SimpleClass. Please check if the namespace is PSR-4 compliant (i.e. it needs to match the folder structure).
				See: https://www.php-fig.org/psr/psr-4/#3-examples';
		$this->assertEquals($expectedMessage, $exception->getMessage());
	}

	/**
	 * Test that exception inherits from proper parent classes.
	 * @covers ::throwInvalidNamespace
	 */
	public function testExceptionInheritance(): void
	{
		$exception = NonPsr4CompliantClass::throwInvalidNamespace('TestClass');

		$this->assertInstanceOf(InvalidArgumentException::class, $exception);
		$this->assertInstanceOf(GeneralExceptionInterface::class, $exception);

		// Verify it's throwable
		$this->expectException(NonPsr4CompliantClass::class);
		throw $exception;
	}

	/**
	 * Test with real-world class names.
	 * @covers ::throwInvalidNamespace
	 */
	public function testRealWorldClassNames(): void
	{
		$classNames = [
			'App\\Services\\EmailService',
			'MyProject\\Database\\UserRepository',
			'Vendor\\Package\\SubPackage\\UtilityClass',
			'EightshiftLibs\\Helpers\\GeneralTrait',
		];

		foreach ($classNames as $className) {
			$exception = NonPsr4CompliantClass::throwInvalidNamespace($className);
			$this->assertStringContainsString($className, $exception->getMessage());
			$this->assertStringContainsString('PSR-4 compliant', $exception->getMessage());
			$this->assertStringContainsString('https://www.php-fig.org/psr/psr-4/#3-examples', $exception->getMessage());
		}
	}
}
