<?php

/**
 * Tests for AbstractAcfMeta.
 *
 * @package EightshiftLibs\Tests\Unit\CustomMeta
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\CustomMeta;

use Brain\Monkey\Functions;
use EightshiftLibs\CustomMeta\AbstractAcfMeta;
use EightshiftLibs\Services\ServiceInterface;
use EightshiftLibs\Tests\BaseTestCase;
use Mockery;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;

/**
 * Minimal concrete subclass so the abstract can be instantiated.
 */
class ConcreteAcfMeta extends AbstractAcfMeta
{
	public bool $fieldsWasCalled = false;

	public function fields(): void
	{
		$this->fieldsWasCalled = true;
	}
}

/**
 * @coversDefaultClass \EightshiftLibs\CustomMeta\AbstractAcfMeta
 */
class AbstractAcfMetaTest extends BaseTestCase
{
	public function testImplementsServiceInterface(): void
	{
		$this->assertInstanceOf(ServiceInterface::class, new ConcreteAcfMeta());
	}

	/**
	 * Without ACF installed, register() exits silently.
	 * No mocking needed — the test environment has no 'ACF' class loaded.
	 *
	 * @covers ::register
	 */
	public function testRegisterDoesNothingWhenAcfNotInstalled(): void
	{
		// Precondition: ACF is not loaded in the default test environment.
		$this->assertFalse(\class_exists('ACF', false), 'Test precondition: ACF must not exist.');

		Functions\expect('add_action')->never();

		(new ConcreteAcfMeta())->register();
	}

	/**
	 * When ACF is installed, register() hooks fields() onto acf/init.
	 *
	 * Runs in a separate process so that defining the ACF stub class here
	 * does not leak into other tests (classes cannot be undefined in PHP).
	 *
	 * @covers ::register
	 */
	#[RunInSeparateProcess]
	#[PreserveGlobalState(false)]
	public function testRegisterHooksFieldsOntoAcfInitWhenAcfInstalled(): void
	{
		if (!\class_exists('ACF', false)) {
			eval('class ACF {}'); // phpcs:ignore Squiz.PHP.Eval.Discouraged
		}

		Functions\expect('add_action')
			->once()
			->with('acf/init', Mockery::type('array'));

		(new ConcreteAcfMeta())->register();
	}
}
