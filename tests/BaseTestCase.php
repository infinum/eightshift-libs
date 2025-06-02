<?php

/**
 * Base test case for all EightshiftLibs tests.
 *
 * @package EightshiftLibs\Tests
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests;

use PHPUnit\Framework\TestCase;
use Brain\Monkey;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

/**
 * Base test case class.
 *
 * Provides common setup and teardown functionality for all tests.
 */
abstract class BaseTestCase extends TestCase
{
	use MockeryPHPUnitIntegration;

	/**
	 * Set up test environment before each test.
	 *
	 * @return void
	 */
	protected function setUp(): void
	{
		parent::setUp();
		Monkey\setUp();
	}

	/**
	 * Clean up test environment after each test.
	 *
	 * @return void
	 */
	protected function tearDown(): void
	{
		Monkey\tearDown();
		parent::tearDown();
	}
}
