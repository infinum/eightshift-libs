<?php

/**
 * Tests for AbstractField class
 *
 * @package EightshiftLibs\Tests\Unit\Rest\Fields
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Rest\Fields;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftLibs\Rest\Fields\AbstractField;
use EightshiftLibs\Services\ServiceInterface;
use EightshiftLibs\Tests\BaseTestCase;
use WP_REST_Server;

/**
 * AbstractFieldTest class
 */
class AbstractFieldTest extends BaseTestCase
{
	/**
	 * Set up before each test
	 *
	 * @return void
	 */
	protected function setUp(): void
	{
		parent::setUp();
		Monkey\setUp();
	}

	/**
	 * Tear down after each test
	 *
	 * @return void
	 */
	protected function tearDown(): void
	{
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Test that AbstractField implements ServiceInterface
	 *
	 * @return void
	 */
	public function testImplementsServiceInterface(): void
	{
		$field = new ConcreteField();

		$this->assertInstanceOf(ServiceInterface::class, $field);
	}

	/**
	 * Test that register method is callable
	 *
	 * @return void
	 */
	public function testRegisterIsCallable(): void
	{
		$field = new ConcreteField();

		$this->assertTrue(\is_callable([$field, 'register']));
	}

	/**
	 * Test that fieldRegisterCallback method is callable
	 *
	 * @return void
	 */
	public function testFieldRegisterCallbackIsCallable(): void
	{
		$field = new ConcreteField();

		$this->assertTrue(\is_callable([$field, 'fieldRegisterCallback']));
	}

	/**
	 * Test that register method adds action hook
	 *
	 * @return void
	 */
	public function testRegisterAddsRestApiInitAction(): void
	{
		Functions\expect('add_action')
			->once()
			->with('rest_api_init', \Mockery::type('array'));

		$field = new ConcreteField();
		$field->register();
	}

	/**
	 * Test that fieldRegisterCallback calls register_rest_field with correct arguments
	 *
	 * @return void
	 */
	public function testFieldRegisterCallbackCallsRegisterRestField(): void
	{
		Functions\expect('register_rest_field')
			->once()
			->with(
				'post',
				'test_field',
				\Mockery::type('array')
			);

		$field = new ConcreteField();
		$field->fieldRegisterCallback(\Mockery::mock(WP_REST_Server::class));
	}

	/**
	 * Test that getObjectType returns expected value
	 *
	 * @return void
	 */
	public function testGetObjectTypeReturnsExpectedValue(): void
	{
		$field = new ConcreteField();

		$reflection = new \ReflectionMethod($field, 'getObjectType');

		$this->assertEquals('post', $reflection->invoke($field));
	}

	/**
	 * Test that getFieldName returns expected value
	 *
	 * @return void
	 */
	public function testGetFieldNameReturnsExpectedValue(): void
	{
		$field = new ConcreteField();

		$reflection = new \ReflectionMethod($field, 'getFieldName');

		$this->assertEquals('test_field', $reflection->invoke($field));
	}

	/**
	 * Test that getCallbackArguments returns expected structure
	 *
	 * @return void
	 */
	public function testGetCallbackArgumentsReturnsExpectedStructure(): void
	{
		$field = new ConcreteField();

		$reflection = new \ReflectionMethod($field, 'getCallbackArguments');
		$result = $reflection->invoke($field);

		$this->assertArrayHasKey('get_callback', $result);
		$this->assertArrayHasKey('update_callback', $result);
		$this->assertArrayHasKey('schema', $result);
		$this->assertNull($result['update_callback']);
		$this->assertNull($result['schema']);
	}
}

/**
 * Concrete implementation of AbstractField for testing
 */
class ConcreteField extends AbstractField
{
	/**
	 * Get the object type
	 *
	 * @return string|string[]
	 */
	protected function getObjectType()
	{
		return 'post';
	}

	/**
	 * Get the field name
	 *
	 * @return string
	 */
	protected function getFieldName(): string
	{
		return 'test_field';
	}

	/**
	 * Get callback arguments
	 *
	 * @return array<string, mixed>
	 */
	protected function getCallbackArguments(): array
	{
		return [
			'get_callback' => [$this, 'getCallback'],
			'update_callback' => null,
			'schema' => null,
		];
	}

	/**
	 * Get callback
	 *
	 * @param array<string, mixed> $object The object from the response.
	 *
	 * @return string
	 */
	public function getCallback(array $object): string
	{
		return 'test_value';
	}
}
