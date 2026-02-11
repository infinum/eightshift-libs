<?php

/**
 * Tests for AbstractRoute class
 *
 * @package EightshiftLibs\Tests\Unit\Rest\Routes
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Rest\Routes;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftLibs\Rest\Routes\AbstractRoute;
use EightshiftLibs\Rest\RouteInterface;
use EightshiftLibs\Services\ServiceInterface;
use EightshiftLibs\Tests\BaseTestCase;
use WP_REST_Request;
use WP_REST_Server;

/**
 * AbstractRouteTest class
 */
class AbstractRouteTest extends BaseTestCase
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

		if (!\defined('WP_REST_Server::READABLE')) {
			if (!\class_exists('WP_REST_Server')) {
				// phpcs:ignore
				eval('class WP_REST_Server { const READABLE = "GET"; const CREATABLE = "POST"; }');
			}
		}
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
	 * Test that AbstractRoute implements ServiceInterface
	 *
	 * @return void
	 */
	public function testImplementsServiceInterface(): void
	{
		$route = new ConcreteRoute();

		$this->assertInstanceOf(ServiceInterface::class, $route);
	}

	/**
	 * Test that AbstractRoute implements RouteInterface
	 *
	 * @return void
	 */
	public function testImplementsRouteInterface(): void
	{
		$route = new ConcreteRoute();

		$this->assertInstanceOf(RouteInterface::class, $route);
	}

	/**
	 * Test that register method is callable
	 *
	 * @return void
	 */
	public function testRegisterIsCallable(): void
	{
		$route = new ConcreteRoute();

		$this->assertTrue(\is_callable([$route, 'register']));
	}

	/**
	 * Test that routeRegisterCallback method is callable
	 *
	 * @return void
	 */
	public function testRouteRegisterCallbackIsCallable(): void
	{
		$route = new ConcreteRoute();

		$this->assertTrue(\is_callable([$route, 'routeRegisterCallback']));
	}

	/**
	 * Test status constants
	 *
	 * @return void
	 */
	public function testStatusConstants(): void
	{
		$this->assertEquals('error', AbstractRoute::STATUS_ERROR);
		$this->assertEquals('success', AbstractRoute::STATUS_SUCCESS);
		$this->assertEquals('warning', AbstractRoute::STATUS_WARNING);
	}

	/**
	 * Test API response code constants
	 *
	 * @return void
	 */
	public function testApiResponseCodeConstants(): void
	{
		$this->assertEquals(200, AbstractRoute::API_RESPONSE_CODE_OK);
		$this->assertEquals(201, AbstractRoute::API_RESPONSE_CODE_CREATED);
		$this->assertEquals(400, AbstractRoute::API_RESPONSE_CODE_BAD_REQUEST);
		$this->assertEquals(401, AbstractRoute::API_RESPONSE_CODE_UNAUTHORIZED);
		$this->assertEquals(403, AbstractRoute::API_RESPONSE_CODE_FORBIDDEN);
		$this->assertEquals(404, AbstractRoute::API_RESPONSE_CODE_NOT_FOUND);
		$this->assertEquals(500, AbstractRoute::API_RESPONSE_CODE_INTERNAL_SERVER_ERROR);
	}

	/**
	 * Test RouteInterface constants
	 *
	 * @return void
	 */
	public function testRouteInterfaceConstants(): void
	{
		$this->assertEquals('GET', RouteInterface::READABLE);
		$this->assertEquals('POST', RouteInterface::CREATABLE);
		$this->assertEquals('PATCH', RouteInterface::EDITABLE);
		$this->assertEquals('PUT', RouteInterface::UPDATEABLE);
		$this->assertEquals('DELETE', RouteInterface::DELETABLE);
	}

	/**
	 * Test deprecated API response code constants
	 *
	 * @return void
	 */
	public function testDeprecatedApiResponseCodeConstants(): void
	{
		$this->assertEquals(200, AbstractRoute::API_RESPONSE_CODE_SUCCESS);
		$this->assertEquals(299, AbstractRoute::API_RESPONSE_CODE_SUCCESS_RANGE);
		$this->assertEquals(400, AbstractRoute::API_RESPONSE_CODE_ERROR);
		$this->assertEquals(404, AbstractRoute::API_RESPONSE_CODE_ERROR_MISSING);
		$this->assertEquals(403, AbstractRoute::API_RESPONSE_CODE_ERROR_FORBIDDEN);
		$this->assertEquals(500, AbstractRoute::API_RESPONSE_CODE_ERROR_SERVER);
	}

	/**
	 * Test additional API response code constants
	 *
	 * @return void
	 */
	public function testAdditionalApiResponseCodeConstants(): void
	{
		$this->assertEquals(100, AbstractRoute::API_RESPONSE_CODE_CONTINUE);
		$this->assertEquals(202, AbstractRoute::API_RESPONSE_CODE_ACCEPTED);
		$this->assertEquals(204, AbstractRoute::API_RESPONSE_CODE_NO_CONTENT);
		$this->assertEquals(301, AbstractRoute::API_RESPONSE_CODE_MOVED_PERMANENTLY);
		$this->assertEquals(302, AbstractRoute::API_RESPONSE_CODE_FOUND);
		$this->assertEquals(304, AbstractRoute::API_RESPONSE_CODE_NOT_MODIFIED);
		$this->assertEquals(405, AbstractRoute::API_RESPONSE_CODE_METHOD_NOT_ALLOWED);
		$this->assertEquals(409, AbstractRoute::API_RESPONSE_CODE_CONFLICT);
		$this->assertEquals(422, AbstractRoute::API_RESPONSE_CODE_UNPROCESSABLE_ENTITY);
		$this->assertEquals(429, AbstractRoute::API_RESPONSE_CODE_TOO_MANY_REQUESTS);
		$this->assertEquals(503, AbstractRoute::API_RESPONSE_CODE_SERVICE_UNAVAILABLE);
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

		$route = new ConcreteRoute();
		$route->register();
	}

	/**
	 * Test that routeRegisterCallback calls register_rest_route with correct arguments
	 *
	 * @return void
	 */
	public function testRouteRegisterCallbackCallsRegisterRestRoute(): void
	{
		Functions\expect('register_rest_route')
			->once()
			->with(
				'test-namespace/v1',
				'/test-route',
				\Mockery::type('array'),
				false
			);

		$route = new ConcreteRoute();
		$route->routeRegisterCallback(\Mockery::mock(WP_REST_Server::class));
	}

	/**
	 * Test that overrideRoute returns false by default
	 *
	 * @return void
	 */
	public function testOverrideRouteReturnsFalseByDefault(): void
	{
		$route = new ConcreteRoute();

		$reflection = new \ReflectionMethod($route, 'overrideRoute');

		$this->assertFalse($reflection->invoke($route));
	}

	/**
	 * Test that checkUserPermission returns empty array when user has permission
	 *
	 * @return void
	 */
	public function testCheckUserPermissionReturnsEmptyWhenAllowed(): void
	{
		Functions\when('current_user_can')->justReturn(true);

		$route = new ConcreteRoute();

		$reflection = new \ReflectionMethod($route, 'checkUserPermission');

		$this->assertEquals([], $reflection->invoke($route, 'manage_options'));
	}

	/**
	 * Test that checkUserPermission returns error when user lacks permission
	 *
	 * @return void
	 */
	public function testCheckUserPermissionReturnsErrorWhenDenied(): void
	{
		Functions\when('current_user_can')->justReturn(false);
		Functions\when('__')->returnArg(1);

		$route = new ConcreteRoute();

		$reflection = new \ReflectionMethod($route, 'checkUserPermission');
		$result = $reflection->invoke($route, 'manage_options');

		$this->assertEquals(AbstractRoute::STATUS_ERROR, $result['status']);
		$this->assertEquals(AbstractRoute::API_RESPONSE_CODE_ERROR_FORBIDDEN, $result['code']);
		$this->assertArrayHasKey('message', $result);
		$this->assertArrayHasKey('data', $result);
	}

	/**
	 * Test that getNamespace returns expected value
	 *
	 * @return void
	 */
	public function testGetNamespaceReturnsExpectedValue(): void
	{
		$route = new ConcreteRoute();

		$reflection = new \ReflectionMethod($route, 'getNamespace');

		$this->assertEquals('test-namespace', $reflection->invoke($route));
	}

	/**
	 * Test that getVersion returns expected value
	 *
	 * @return void
	 */
	public function testGetVersionReturnsExpectedValue(): void
	{
		$route = new ConcreteRoute();

		$reflection = new \ReflectionMethod($route, 'getVersion');

		$this->assertEquals('v1', $reflection->invoke($route));
	}

	/**
	 * Test that getRouteName returns expected value
	 *
	 * @return void
	 */
	public function testGetRouteNameReturnsExpectedValue(): void
	{
		$route = new ConcreteRoute();

		$reflection = new \ReflectionMethod($route, 'getRouteName');

		$this->assertEquals('/test-route', $reflection->invoke($route));
	}

	/**
	 * Test that getCallbackArguments returns expected structure
	 *
	 * @return void
	 */
	public function testGetCallbackArgumentsReturnsExpectedStructure(): void
	{
		$route = new ConcreteRoute();

		$reflection = new \ReflectionMethod($route, 'getCallbackArguments');
		$result = $reflection->invoke($route);

		$this->assertArrayHasKey('methods', $result);
		$this->assertArrayHasKey('callback', $result);
		$this->assertEquals(WP_REST_Server::READABLE, $result['methods']);
	}
}

/**
 * Concrete implementation of AbstractRoute for testing
 */
class ConcreteRoute extends AbstractRoute
{
	/**
	 * Get the namespace
	 *
	 * @return string
	 */
	protected function getNamespace(): string
	{
		return 'test-namespace';
	}

	/**
	 * Get the version
	 *
	 * @return string
	 */
	protected function getVersion(): string
	{
		return 'v1';
	}

	/**
	 * Get the route name
	 *
	 * @return string
	 */
	protected function getRouteName(): string
	{
		return '/test-route';
	}

	/**
	 * Get callback arguments
	 *
	 * @return array<string, mixed>
	 */
	protected function getCallbackArguments(): array
	{
		return [
			'methods' => WP_REST_Server::READABLE,
			'callback' => [$this, 'routeCallback'],
		];
	}

	/**
	 * Route callback
	 *
	 * @param WP_REST_Request $request Request object.
	 *
	 * @return array<string, mixed>
	 */
	public function routeCallback(WP_REST_Request $request): array
	{
		return ['status' => 'success'];
	}
}
