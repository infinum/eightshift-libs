<?php

/**
 * Tests for AbstractMain class
 *
 * @package EightshiftLibs\Tests\Unit\Main
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Main;

use EightshiftLibs\Main\AbstractMain;
use EightshiftLibs\Services\ServiceInterface;
use PHPUnit\Framework\TestCase;

/**
 * AbstractMainTest class
 */
class AbstractMainTest extends TestCase
{
	/**
	 * Test that AbstractMain implements ServiceInterface
	 *
	 * @return void
	 */
	public function testImplementsServiceInterface(): void
	{
		$main = new ConcreteMain([], 'TestNamespace');

		$this->assertInstanceOf(ServiceInterface::class, $main);
	}

	/**
	 * Test that register method exists and is callable
	 *
	 * @return void
	 */
	public function testRegisterMethodExists(): void
	{
		$main = new ConcreteMain([], 'TestNamespace');

		$this->assertTrue(\is_callable([$main, 'register']));
	}

	/**
	 * Test that namespace property is set correctly via constructor
	 *
	 * @return void
	 */
	public function testNamespacePropertyIsSetCorrectly(): void
	{
		$main = new ConcreteMain([], 'TestNamespace');

		$reflection = new \ReflectionProperty($main, 'namespace');

		$this->assertEquals('TestNamespace', $reflection->getValue($main));
	}

	/**
	 * Test that psr4Prefixes property is set correctly via constructor
	 *
	 * @return void
	 */
	public function testPsr4PrefixesPropertyIsSetCorrectly(): void
	{
		$prefixes = ['App\\' => '/src'];
		$main = new ConcreteMain($prefixes, 'TestNamespace');

		$reflection = new \ReflectionProperty($main, 'psr4Prefixes');

		$this->assertEquals($prefixes, $reflection->getValue($main));
	}

	/**
	 * Test that getServiceClasses returns empty array by default
	 *
	 * @return void
	 */
	public function testGetServiceClassesReturnsEmptyArray(): void
	{
		$main = new ConcreteMain([], 'TestNamespace');

		$reflection = new \ReflectionMethod($main, 'getServiceClasses');

		$this->assertEquals([], $reflection->invoke($main));
	}
}

/**
 * Concrete implementation of AbstractMain for testing
 */
class ConcreteMain extends AbstractMain
{
	/**
	 * Register the service
	 *
	 * @return void
	 */
	public function register(): void
	{
		// No registration needed for testing
	}

	/**
	 * Get the list of services to register
	 *
	 * @return array<class-string, string|string[]>
	 */
	protected function getServiceClasses(): array
	{
		return [];
	}
}
