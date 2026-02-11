<?php

/**
 * Tests for ApiTrait helper methods.
 *
 * @package EightshiftLibs\Tests\Unit\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Helpers;

use EightshiftLibs\Tests\BaseTestCase;
use EightshiftLibs\Helpers\ApiTrait;
use EightshiftLibs\Rest\Routes\AbstractRoute;
use Brain\Monkey\Functions;
use WP_REST_Response;

/**
 * Wrapper class to test ApiTrait methods without conflicts.
 */
class ApiTraitWrapper
{
	use ApiTrait;
}

/**
 * Test case for ApiTrait utility methods.
 *
 * @coversDefaultClass EightshiftLibs\Helpers\ApiTrait
 */
class ApiTraitTest extends BaseTestCase
{
	private ApiTraitWrapper $wrapper;

	protected function setUp(): void
	{
		parent::setUp();
		$this->wrapper = new ApiTraitWrapper();

		// Mock WordPress functions
		Functions\when('get_current_blog_id')->justReturn(1);
		Functions\when('get_rest_url')->alias(function ($blogId) {
			return "https://example.com/wp-json/";
		});

		// Create mock WP_REST_Response class if it doesn't exist
		if (!class_exists('WP_REST_Response')) {
			$mockClass = new class {
				private $data;
				private $status;

				public function __construct($data = null, $status = 200) {
					$this->data = $data;
					$this->status = $status;
				}

				public function get_data() {
					return $this->data;
				}

				public function get_status() {
					return $this->status;
				}
			};

			// Create the class alias
			eval('class WP_REST_Response {
				private $data;
				private $status;

				public function __construct($data = null, $status = 200) {
					$this->data = $data;
					$this->status = $status;
				}

				public function get_data() {
					return $this->data;
				}

				public function get_status() {
					return $this->status;
				}
			}');
		}
	}

	/**
	 * @covers ::getApiResponse
	 */
	public function testGetApiResponseWithDefaultParameters(): void
	{
		$message = 'Error occurred';
		$result = $this->wrapper::getApiResponse($message);

		$this->assertInstanceOf(WP_REST_Response::class, $result);

		$data = $result->get_data();
		$this->assertEquals(AbstractRoute::STATUS_ERROR, $data['status']);
		$this->assertEquals(AbstractRoute::API_RESPONSE_CODE_BAD_REQUEST, $data['code']);
		$this->assertEquals($message, $data['message']);
		$this->assertArrayNotHasKey('data', $data);
	}

	/**
	 * @covers ::getApiResponse
	 */
	public function testGetApiResponseWithSuccessStatus(): void
	{
		$message = 'Operation successful';
		$result = $this->wrapper::getApiResponse(
			$message,
			AbstractRoute::API_RESPONSE_CODE_SUCCESS,
			AbstractRoute::STATUS_SUCCESS
		);

		$data = $result->get_data();
		$this->assertEquals(AbstractRoute::STATUS_SUCCESS, $data['status']);
		$this->assertEquals(AbstractRoute::API_RESPONSE_CODE_SUCCESS, $data['code']);
		$this->assertEquals($message, $data['message']);
	}

	/**
	 * @covers ::getApiResponse
	 */
	public function testGetApiResponseWithAdditionalData(): void
	{
		$message = 'Data retrieved';
		$additionalData = [
			'user_id' => 123,
			'items' => ['item1', 'item2'],
			'count' => 2
		];

		$result = $this->wrapper::getApiResponse(
			$message,
			AbstractRoute::API_RESPONSE_CODE_SUCCESS,
			AbstractRoute::STATUS_SUCCESS,
			$additionalData
		);

		$data = $result->get_data();
		$this->assertArrayHasKey('data', $data);
		$this->assertEquals($additionalData, $data['data']);
	}

	/**
	 * @covers ::getApiResponse
	 */
	public function testGetApiResponseWithEmptyAdditionalData(): void
	{
		$message = 'Success';
		$result = $this->wrapper::getApiResponse(
			$message,
			AbstractRoute::API_RESPONSE_CODE_SUCCESS,
			AbstractRoute::STATUS_SUCCESS,
			[]
		);

		$data = $result->get_data();
		$this->assertArrayNotHasKey('data', $data);
	}

	/**
	 * @covers ::getApiResponse
	 */
	public function testGetApiResponseStatusCode(): void
	{
		$message = 'Not found';
		$result = $this->wrapper::getApiResponse(
			$message,
			AbstractRoute::API_RESPONSE_CODE_NOT_FOUND,
			AbstractRoute::STATUS_ERROR
		);

		$this->assertEquals(AbstractRoute::API_RESPONSE_CODE_NOT_FOUND, $result->get_status());
	}

	/**
	 * @covers ::getApiRouteUrlData
	 */
	public function testGetApiRouteUrlDataWithValidParameters(): void
	{
		$namespace = 'my-plugin';
		$version = 'v1';
		$path = 'users';

		$result = $this->wrapper::getApiRouteUrlData($namespace, $version, $path);

		$this->assertIsArray($result);
		$this->assertArrayHasKey('prefix', $result);
		$this->assertArrayHasKey('namespace', $result);
		$this->assertArrayHasKey('version', $result);
		$this->assertArrayHasKey('url', $result);
		$this->assertArrayHasKey('pathUrl', $result);

		$this->assertEquals('https://example.com/wp-json', $result['prefix']);
		$this->assertEquals($namespace, $result['namespace']);
		$this->assertEquals($version, $result['version']);
		$this->assertEquals('https://example.com/wp-json/my-plugin/v1/users', $result['url']);
		$this->assertEquals('/my-plugin/v1/users', $result['pathUrl']);
	}

	/**
	 * @covers ::getApiRouteUrlData
	 */
	public function testGetApiRouteUrlDataWithDifferentPaths(): void
	{
		$result = $this->wrapper::getApiRouteUrlData('api', 'v2', 'posts/123');

		$this->assertEquals('https://example.com/wp-json/api/v2/posts/123', $result['url']);
		$this->assertEquals('/api/v2/posts/123', $result['pathUrl']);
	}

	/**
	 * @covers ::getApiRouteUrlData
	 */
	public function testGetApiRouteUrlDataReturnsCorrectStructure(): void
	{
		$result = $this->wrapper::getApiRouteUrlData('test', 'v1', 'endpoint');

		$expected = ['prefix', 'namespace', 'version', 'url', 'pathUrl'];
		$this->assertEquals($expected, array_keys($result));
	}
}
