<?php

/**
 * Comprehensive tests for ApiTrait helper methods.
 *
 * @package EightshiftLibs\Tests\Unit\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Helpers;

use EightshiftLibs\Tests\BaseTestCase;
use EightshiftLibs\Helpers\ApiTrait;
use EightshiftLibs\Rest\Routes\AbstractRoute;
use Brain\Monkey\Functions;

/**
 * Wrapper class to test ApiTrait methods without conflicts.
 */
class ApiTraitWrapper
{
	use ApiTrait;
}

/**
 * Comprehensive test case for ApiTrait utility methods.
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
	}

	/**
	 * @covers ::getApiSuccessPublicOutput
	 */
	public function testGetApiSuccessPublicOutputWithoutAdditionalData(): void
	{
		$message = 'Operation completed successfully';
		$result = $this->wrapper::getApiSuccessPublicOutput($message);

		$this->assertEquals(AbstractRoute::STATUS_SUCCESS, $result['status']);
		$this->assertEquals(AbstractRoute::API_RESPONSE_CODE_SUCCESS, $result['code']);
		$this->assertEquals($message, $result['message']);
		$this->assertArrayNotHasKey('data', $result);
	}

	/**
	 * @covers ::getApiSuccessPublicOutput
	 */
	public function testGetApiSuccessPublicOutputWithAdditionalData(): void
	{
		$message = 'Data retrieved successfully';
		$additionalData = [
			'user_id' => 123,
			'items' => ['item1', 'item2'],
			'count' => 2
		];

		$result = $this->wrapper::getApiSuccessPublicOutput($message, $additionalData);

		$this->assertEquals(AbstractRoute::STATUS_SUCCESS, $result['status']);
		$this->assertEquals(AbstractRoute::API_RESPONSE_CODE_SUCCESS, $result['code']);
		$this->assertEquals($message, $result['message']);
		$this->assertArrayHasKey('data', $result);
		$this->assertEquals($additionalData, $result['data']);
	}

	/**
	 * @covers ::getApiSuccessPublicOutput
	 */
	public function testGetApiSuccessPublicOutputWithEmptyMessage(): void
	{
		$result = $this->wrapper::getApiSuccessPublicOutput('');

		$this->assertEquals(AbstractRoute::STATUS_SUCCESS, $result['status']);
		$this->assertEquals(AbstractRoute::API_RESPONSE_CODE_SUCCESS, $result['code']);
		$this->assertEquals('', $result['message']);
		$this->assertArrayNotHasKey('data', $result);
	}

	/**
	 * @covers ::getApiSuccessPublicOutput
	 */
	public function testGetApiSuccessPublicOutputWithEmptyAdditionalData(): void
	{
		$message = 'Success with empty data';
		$result = $this->wrapper::getApiSuccessPublicOutput($message, []);

		$this->assertEquals(AbstractRoute::STATUS_SUCCESS, $result['status']);
		$this->assertEquals(AbstractRoute::API_RESPONSE_CODE_SUCCESS, $result['code']);
		$this->assertEquals($message, $result['message']);
		$this->assertArrayNotHasKey('data', $result);
	}

	/**
	 * @covers ::getApiWarningPublicOutput
	 */
	public function testGetApiWarningPublicOutputWithoutAdditionalData(): void
	{
		$message = 'Operation completed with warnings';
		$result = $this->wrapper::getApiWarningPublicOutput($message);

		$this->assertEquals(AbstractRoute::STATUS_WARNING, $result['status']);
		$this->assertEquals(AbstractRoute::API_RESPONSE_CODE_SUCCESS, $result['code']);
		$this->assertEquals($message, $result['message']);
		$this->assertArrayNotHasKey('data', $result);
	}

	/**
	 * @covers ::getApiWarningPublicOutput
	 */
	public function testGetApiWarningPublicOutputWithAdditionalData(): void
	{
		$message = 'Some fields could not be processed';
		$additionalData = [
			'processed' => 5,
			'skipped' => 2,
			'warnings' => ['Field X is deprecated', 'Field Y has invalid format']
		];

		$result = $this->wrapper::getApiWarningPublicOutput($message, $additionalData);

		$this->assertEquals(AbstractRoute::STATUS_WARNING, $result['status']);
		$this->assertEquals(AbstractRoute::API_RESPONSE_CODE_SUCCESS, $result['code']);
		$this->assertEquals($message, $result['message']);
		$this->assertArrayHasKey('data', $result);
		$this->assertEquals($additionalData, $result['data']);
	}

	/**
	 * @covers ::getApiWarningPublicOutput
	 */
	public function testGetApiWarningPublicOutputWithEmptyAdditionalData(): void
	{
		$message = 'Warning with empty data';
		$result = $this->wrapper::getApiWarningPublicOutput($message, []);

		$this->assertEquals(AbstractRoute::STATUS_WARNING, $result['status']);
		$this->assertEquals(AbstractRoute::API_RESPONSE_CODE_SUCCESS, $result['code']);
		$this->assertEquals($message, $result['message']);
		$this->assertArrayNotHasKey('data', $result);
	}

	/**
	 * @covers ::getApiErrorPublicOutput
	 */
	public function testGetApiErrorPublicOutputWithoutAdditionalData(): void
	{
		$message = 'An error occurred while processing the request';
		$result = $this->wrapper::getApiErrorPublicOutput($message);

		$this->assertEquals(AbstractRoute::STATUS_ERROR, $result['status']);
		$this->assertEquals(AbstractRoute::API_RESPONSE_CODE_ERROR, $result['code']);
		$this->assertEquals($message, $result['message']);
		$this->assertArrayNotHasKey('data', $result);
	}

	/**
	 * @covers ::getApiErrorPublicOutput
	 */
	public function testGetApiErrorPublicOutputWithAdditionalData(): void
	{
		$message = 'Validation failed';
		$additionalData = [
			'errors' => [
				'email' => 'Invalid email format',
				'password' => 'Password too short'
			],
			'error_code' => 'VALIDATION_FAILED',
			'timestamp' => '2023-01-01T12:00:00Z'
		];

		$result = $this->wrapper::getApiErrorPublicOutput($message, $additionalData);

		$this->assertEquals(AbstractRoute::STATUS_ERROR, $result['status']);
		$this->assertEquals(AbstractRoute::API_RESPONSE_CODE_ERROR, $result['code']);
		$this->assertEquals($message, $result['message']);
		$this->assertArrayHasKey('data', $result);
		$this->assertEquals($additionalData, $result['data']);
	}

	/**
	 * @covers ::getApiErrorPublicOutput
	 */
	public function testGetApiErrorPublicOutputWithEmptyAdditionalData(): void
	{
		$message = 'Error with empty data';
		$result = $this->wrapper::getApiErrorPublicOutput($message, []);

		$this->assertEquals(AbstractRoute::STATUS_ERROR, $result['status']);
		$this->assertEquals(AbstractRoute::API_RESPONSE_CODE_ERROR, $result['code']);
		$this->assertEquals($message, $result['message']);
		$this->assertArrayNotHasKey('data', $result);
	}

	/**
	 * @covers ::getApiRouteUrlData
	 */
	public function testGetApiRouteUrlData(): void
	{
		$namespace = 'myapp';
		$version = 'v1';
		$path = 'users/profile';

		// Mock get_rest_url to return a known URL
		Functions\when('get_rest_url')->justReturn('https://example.com/wp-json/');

		$result = $this->wrapper::getApiRouteUrlData($namespace, $version, $path);

		$this->assertArrayHasKey('prefix', $result);
		$this->assertArrayHasKey('namespace', $result);
		$this->assertArrayHasKey('version', $result);
		$this->assertArrayHasKey('url', $result);

		$this->assertEquals('https://example.com/wp-json', $result['prefix']);
		$this->assertEquals($namespace, $result['namespace']);
		$this->assertEquals($version, $result['version']);
		$this->assertEquals('https://example.com/wp-json/myapp/v1/users/profile', $result['url']);
	}

	/**
	 * @covers ::getApiRouteUrlData
	 */
	public function testGetApiRouteUrlDataWithTrailingSlashInRestUrl(): void
	{
		$namespace = 'api';
		$version = 'v2';
		$path = 'posts';

		// Mock get_rest_url to return a URL with trailing slash
		Functions\when('get_rest_url')->justReturn('https://example.com/wp-json/');

		$result = $this->wrapper::getApiRouteUrlData($namespace, $version, $path);

		// Should strip trailing slash from prefix
		$this->assertEquals('https://example.com/wp-json', $result['prefix']);
		$this->assertEquals('https://example.com/wp-json/api/v2/posts', $result['url']);
	}

	/**
	 * @covers ::getApiRouteUrlData
	 */
	public function testGetApiRouteUrlDataWithoutTrailingSlashInRestUrl(): void
	{
		$namespace = 'custom';
		$version = 'v1';
		$path = 'data';

		// Mock get_rest_url to return a URL without trailing slash
		Functions\when('get_rest_url')->justReturn('https://example.com/wp-json');

		$result = $this->wrapper::getApiRouteUrlData($namespace, $version, $path);

		$this->assertEquals('https://example.com/wp-json', $result['prefix']);
		$this->assertEquals('https://example.com/wp-json/custom/v1/data', $result['url']);
	}

	/**
	 * @covers ::getApiRouteUrlData
	 */
	public function testGetApiRouteUrlDataWithEmptyStrings(): void
	{
		$namespace = '';
		$version = '';
		$path = '';

		Functions\when('get_rest_url')->justReturn('https://example.com/wp-json/');

		$result = $this->wrapper::getApiRouteUrlData($namespace, $version, $path);

		$this->assertEquals('https://example.com/wp-json', $result['prefix']);
		$this->assertEquals('', $result['namespace']);
		$this->assertEquals('', $result['version']);
		$this->assertEquals('https://example.com/wp-json///', $result['url']);
	}

	/**
	 * @covers ::getApiRouteUrlData
	 */
	public function testGetApiRouteUrlDataWithComplexPath(): void
	{
		$namespace = 'eightshift';
		$version = 'v1';
		$path = 'forms/contact/submit';

		Functions\when('get_rest_url')->justReturn('https://site.example.com/subdirectory/wp-json/');

		$result = $this->wrapper::getApiRouteUrlData($namespace, $version, $path);

		$this->assertEquals('https://site.example.com/subdirectory/wp-json', $result['prefix']);
		$this->assertEquals($namespace, $result['namespace']);
		$this->assertEquals($version, $result['version']);
		$this->assertEquals('https://site.example.com/subdirectory/wp-json/eightshift/v1/forms/contact/submit', $result['url']);
	}

	/**
	 * @covers ::getApiRouteUrlData
	 */
	public function testGetApiRouteUrlDataWithSpecialCharacters(): void
	{
		$namespace = 'test-app';
		$version = 'v1.0';
		$path = 'user_data/special-chars';

		Functions\when('get_rest_url')->justReturn('https://example.com/wp-json/');

		$result = $this->wrapper::getApiRouteUrlData($namespace, $version, $path);

		$this->assertEquals('test-app', $result['namespace']);
		$this->assertEquals('v1.0', $result['version']);
		$this->assertEquals('https://example.com/wp-json/test-app/v1.0/user_data/special-chars', $result['url']);
	}

	/**
	 * Test that all response structures are consistent.
	 *
	 * @covers ::getApiSuccessPublicOutput
	 * @covers ::getApiWarningPublicOutput
	 * @covers ::getApiErrorPublicOutput
	 */
	public function testAllResponseStructuresAreConsistent(): void
	{
		$message = 'Test message';
		$data = ['test' => 'data'];

		$success = $this->wrapper::getApiSuccessPublicOutput($message, $data);
		$warning = $this->wrapper::getApiWarningPublicOutput($message, $data);
		$error = $this->wrapper::getApiErrorPublicOutput($message, $data);

		// All should have the same keys
		$expectedKeys = ['status', 'code', 'message', 'data'];

		$this->assertEquals($expectedKeys, array_keys($success));
		$this->assertEquals($expectedKeys, array_keys($warning));
		$this->assertEquals($expectedKeys, array_keys($error));

		// All should have the same message and data
		$this->assertEquals($message, $success['message']);
		$this->assertEquals($message, $warning['message']);
		$this->assertEquals($message, $error['message']);

		$this->assertEquals($data, $success['data']);
		$this->assertEquals($data, $warning['data']);
		$this->assertEquals($data, $error['data']);

		// Status should be different
		$this->assertEquals(AbstractRoute::STATUS_SUCCESS, $success['status']);
		$this->assertEquals(AbstractRoute::STATUS_WARNING, $warning['status']);
		$this->assertEquals(AbstractRoute::STATUS_ERROR, $error['status']);

		// Codes should be appropriate
		$this->assertEquals(AbstractRoute::API_RESPONSE_CODE_SUCCESS, $success['code']);
		$this->assertEquals(AbstractRoute::API_RESPONSE_CODE_SUCCESS, $warning['code']);
		$this->assertEquals(AbstractRoute::API_RESPONSE_CODE_ERROR, $error['code']);
	}

	/**
	 * Test response structure without additional data.
	 *
	 * @covers ::getApiSuccessPublicOutput
	 * @covers ::getApiWarningPublicOutput
	 * @covers ::getApiErrorPublicOutput
	 */
	public function testResponseStructureWithoutAdditionalData(): void
	{
		$message = 'Test message';

		$success = $this->wrapper::getApiSuccessPublicOutput($message);
		$warning = $this->wrapper::getApiWarningPublicOutput($message);
		$error = $this->wrapper::getApiErrorPublicOutput($message);

		// All should have the same keys (without data)
		$expectedKeys = ['status', 'code', 'message'];

		$this->assertEquals($expectedKeys, array_keys($success));
		$this->assertEquals($expectedKeys, array_keys($warning));
		$this->assertEquals($expectedKeys, array_keys($error));

		// None should have data key
		$this->assertArrayNotHasKey('data', $success);
		$this->assertArrayNotHasKey('data', $warning);
		$this->assertArrayNotHasKey('data', $error);
	}
}
