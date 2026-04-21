<?php

/**
 * Tests for DeprecatedTrait helper methods.
 *
 * @package EightshiftLibs\Tests\Unit\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Helpers;

use EightshiftLibs\Tests\BaseTestCase;
use EightshiftLibs\Helpers\DeprecatedTrait;
use EightshiftLibs\Helpers\Helpers;
use EightshiftLibs\Exception\InvalidManifest;
use EightshiftLibs\Rest\Routes\AbstractRoute;
use Brain\Monkey\Functions;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Wrapper class to test DeprecatedTrait methods.
 */
class DeprecatedTraitWrapper
{
	use DeprecatedTrait;
}

/**
 * Test case for DeprecatedTrait utility methods.
 *
 * @coversDefaultClass EightshiftLibs\Helpers\DeprecatedTrait
 */
class DeprecatedTraitTest extends BaseTestCase
{
	private DeprecatedTraitWrapper $wrapper;

	protected function setUp(): void
	{
		parent::setUp();
		$this->wrapper = new DeprecatedTraitWrapper();
	}

	/**
	 * @covers ::classnames
	 */
	public function testClassnames(): void
	{
		$result = DeprecatedTraitWrapper::classnames(['class1', 'class2', 'class3']);
		$this->assertSame('class1 class2 class3', $result);
	}

	/**
	 * @covers ::classnames
	 */
	public function testClassnamesReturnsEmptyStringForEmptyArray(): void
	{
		$this->assertSame('', DeprecatedTraitWrapper::classnames([]));
	}

	/**
	 * @covers ::classnames
	 */
	public function testClassnamesFiltersFalsyValues(): void
	{
		$result = DeprecatedTraitWrapper::classnames(['', 'class1', null, false, 'class2']);
		$this->assertSame('class1 class2', $result);
	}

	/**
	 * @covers ::classnames
	 */
	public function testClassnamesWithSingleClass(): void
	{
		$this->assertSame('my-class', DeprecatedTraitWrapper::classnames(['my-class']));
	}

	/**
	 * @covers ::arrayIsList
	 */
	#[DataProvider('arrayIsListProvider')]
	public function testArrayIsList($input, $expected): void
	{
		$result = DeprecatedTraitWrapper::arrayIsList($input);
		$this->assertSame($expected, $result);
	}

	/**
	 * Data provider for arrayIsList tests.
	 *
	 * @return array<string, array{input: array<mixed>, expected: bool}>
	 */
	public static function arrayIsListProvider(): array
	{
		return [
			'empty array' => [
				'input' => [],
				'expected' => true,
			],
			'sequential array' => [
				'input' => [1, 2, 3, 4],
				'expected' => true,
			],
			'sequential array with strings' => [
				'input' => ['a', 'b', 'c'],
				'expected' => true,
			],
			'associative array' => [
				'input' => ['key1' => 'value1', 'key2' => 'value2'],
				'expected' => false,
			],
			'mixed keys starting from 0' => [
				'input' => [0 => 'a', 1 => 'b', 3 => 'c'],
				'expected' => false,
			],
			'numeric keys not starting from 0' => [
				'input' => [1 => 'a', 2 => 'b', 3 => 'c'],
				'expected' => false,
			],
		];
	}

	/**
	 * @covers ::isJson
	 */
	#[DataProvider('isJsonProvider')]
	public function testIsJson($input, $expected): void
	{
		$result = DeprecatedTraitWrapper::isJson($input);
		$this->assertSame($expected, $result);
	}

	/**
	 * Data provider for isJson tests.
	 *
	 * @return array<string, array{input: string, expected: bool}>
	 */
	public static function isJsonProvider(): array
	{
		return [
			'valid JSON object' => [
				'input' => '{"key":"value"}',
				'expected' => true,
			],
			'valid JSON array' => [
				'input' => '["value1","value2"]',
				'expected' => true,
			],
			'valid JSON empty object' => [
				'input' => '{}',
				'expected' => true,
			],
			'valid JSON empty array' => [
				'input' => '[]',
				'expected' => true,
			],
			'invalid JSON - missing quotes' => [
				'input' => '{key:value}',
				'expected' => false,
			],
			'invalid JSON - plain string' => [
				'input' => 'not json',
				'expected' => false,
			],
			'invalid JSON - incomplete' => [
				'input' => '{"key":"value"',
				'expected' => false,
			],
		];
	}

	/**
	 * @covers ::getApiSuccessPublicOutput
	 */
	public function testGetApiSuccessPublicOutput(): void
	{
		$msg = 'Success message';
		$result = DeprecatedTraitWrapper::getApiSuccessPublicOutput($msg);

		$this->assertIsArray($result);
		$this->assertSame(AbstractRoute::STATUS_SUCCESS, $result['status']);
		$this->assertSame(AbstractRoute::API_RESPONSE_CODE_OK, $result['code']);
		$this->assertSame($msg, $result['message']);
		$this->assertArrayNotHasKey('data', $result);
	}

	/**
	 * @covers ::getApiSuccessPublicOutput
	 */
	public function testGetApiSuccessPublicOutputWithAdditionalData(): void
	{
		$msg = 'Success message';
		$additional = ['key' => 'value', 'number' => 123];
		$result = DeprecatedTraitWrapper::getApiSuccessPublicOutput($msg, $additional);

		$this->assertIsArray($result);
		$this->assertSame(AbstractRoute::STATUS_SUCCESS, $result['status']);
		$this->assertSame(AbstractRoute::API_RESPONSE_CODE_OK, $result['code']);
		$this->assertSame($msg, $result['message']);
		$this->assertArrayHasKey('data', $result);
		$this->assertSame($additional, $result['data']);
	}

	/**
	 * @covers ::getApiWarningPublicOutput
	 */
	public function testGetApiWarningPublicOutput(): void
	{
		$msg = 'Warning message';
		$result = DeprecatedTraitWrapper::getApiWarningPublicOutput($msg);

		$this->assertIsArray($result);
		$this->assertSame(AbstractRoute::STATUS_WARNING, $result['status']);
		$this->assertSame(AbstractRoute::API_RESPONSE_CODE_OK, $result['code']);
		$this->assertSame($msg, $result['message']);
		$this->assertArrayNotHasKey('data', $result);
	}

	/**
	 * @covers ::getApiWarningPublicOutput
	 */
	public function testGetApiWarningPublicOutputWithAdditionalData(): void
	{
		$msg = 'Warning message';
		$additional = ['warning' => 'details'];
		$result = DeprecatedTraitWrapper::getApiWarningPublicOutput($msg, $additional);

		$this->assertIsArray($result);
		$this->assertSame(AbstractRoute::STATUS_WARNING, $result['status']);
		$this->assertSame(AbstractRoute::API_RESPONSE_CODE_OK, $result['code']);
		$this->assertSame($msg, $result['message']);
		$this->assertArrayHasKey('data', $result);
		$this->assertSame($additional, $result['data']);
	}

	/**
	 * @covers ::getApiErrorPublicOutput
	 */
	public function testGetApiErrorPublicOutput(): void
	{
		$msg = 'Error message';
		$result = DeprecatedTraitWrapper::getApiErrorPublicOutput($msg);

		$this->assertIsArray($result);
		$this->assertSame(AbstractRoute::STATUS_ERROR, $result['status']);
		$this->assertSame(AbstractRoute::API_RESPONSE_CODE_BAD_REQUEST, $result['code']);
		$this->assertSame($msg, $result['message']);
		$this->assertArrayNotHasKey('data', $result);
	}

	/**
	 * @covers ::getApiErrorPublicOutput
	 */
	public function testGetApiErrorPublicOutputWithAdditionalData(): void
	{
		$msg = 'Error message';
		$additional = ['error' => 'details', 'code' => 500];
		$result = DeprecatedTraitWrapper::getApiErrorPublicOutput($msg, $additional);

		$this->assertIsArray($result);
		$this->assertSame(AbstractRoute::STATUS_ERROR, $result['status']);
		$this->assertSame(AbstractRoute::API_RESPONSE_CODE_BAD_REQUEST, $result['code']);
		$this->assertSame($msg, $result['message']);
		$this->assertArrayHasKey('data', $result);
		$this->assertSame($additional, $result['data']);
	}

	/**
	 * Set up Helpers path caches via reflection so getProjectPaths('src') returns a known path.
	 */
	private function setupPathsCache(): void
	{
		$reflection = new \ReflectionClass(Helpers::class);

		$basePaths = $reflection->getProperty('basePaths');
		$basePaths->setAccessible(true);
		$basePaths->setValue(null, [
			'root' => '/test',
			'projectRoot' => '/test',
			'src' => '/test/src',
			'public' => '/test/public',
			'blocksRoot' => '/test/src/Blocks',
		]);

		$pathConfigs = $reflection->getProperty('pathConfigs');
		$pathConfigs->setAccessible(true);
		$pathConfigs->setValue(null, [
			'src' => ['/test', 'src'],
		]);
	}

	/**
	 * Clear Helpers path caches and block cache.
	 */
	private function clearPathsAndCache(): void
	{
		$reflection = new \ReflectionClass(Helpers::class);

		$basePaths = $reflection->getProperty('basePaths');
		$basePaths->setAccessible(true);
		$basePaths->setValue(null, null);

		$pathConfigs = $reflection->getProperty('pathConfigs');
		$pathConfigs->setAccessible(true);
		$pathConfigs->setValue(null, null);

		$cacheProperty = $reflection->getProperty('cache');
		$cacheProperty->setAccessible(true);
		$cacheProperty->setValue(null, []);
	}

	/**
	 * Set up Helpers cache with block data for getManifestByDir tests.
	 */
	private function setupBlockCache(): void
	{
		$cache = [
			'blocks' => [
				'wrapper' => ['componentName' => 'wrapper', 'blockName' => 'wrapper'],
				'components' => [
					'heading' => ['componentName' => 'heading'],
				],
				'blocks' => [
					'my-block' => ['blockName' => 'my-block'],
				],
			],
		];

		$reflection = new \ReflectionClass(Helpers::class);
		$cacheProperty = $reflection->getProperty('cache');
		$cacheProperty->setAccessible(true);
		$cacheProperty->setValue(null, $cache);
	}

	/**
	 * @covers ::getManifestByDir
	 */
	public function testGetManifestByDirReturnsWrapperData(): void
	{
		$this->setupPathsCache();
		$this->setupBlockCache();

		$result = DeprecatedTraitWrapper::getManifestByDir('/test/src/Blocks/wrapper');

		$this->assertIsArray($result);
		$this->assertSame('wrapper', $result['componentName']);

		$this->clearPathsAndCache();
	}

	/**
	 * @covers ::getManifestByDir
	 */
	public function testGetManifestByDirReturnsComponentData(): void
	{
		$this->setupPathsCache();
		$this->setupBlockCache();

		$result = DeprecatedTraitWrapper::getManifestByDir('/test/src/Blocks/components/heading');

		$this->assertIsArray($result);
		$this->assertSame('heading', $result['componentName']);

		$this->clearPathsAndCache();
	}

	/**
	 * @covers ::getManifestByDir
	 */
	public function testGetManifestByDirReturnsBlockData(): void
	{
		$this->setupPathsCache();
		$this->setupBlockCache();

		$result = DeprecatedTraitWrapper::getManifestByDir('/test/src/Blocks/custom/my-block');

		$this->assertIsArray($result);
		$this->assertSame('my-block', $result['blockName']);

		$this->clearPathsAndCache();
	}

	/**
	 * @covers ::getManifestByDir
	 */
	public function testGetManifestByDirThrowsForUnknownPathType(): void
	{
		$this->setupPathsCache();
		$this->setupBlockCache();

		Functions\when('esc_html__')->returnArg();

		$this->expectException(InvalidManifest::class);

		try {
			DeprecatedTraitWrapper::getManifestByDir('/test/src/Blocks/unknown/something');
		} finally {
			$this->clearPathsAndCache();
		}
	}

	/**
	 * @covers ::getManifestByDir
	 */
	public function testGetManifestByDirThrowsWhenMissingSecondSegment(): void
	{
		$this->setupPathsCache();
		$this->setupBlockCache();

		Functions\when('esc_html__')->returnArg();

		$this->expectException(InvalidManifest::class);

		try {
			DeprecatedTraitWrapper::getManifestByDir('/test/src/Blocks');
		} finally {
			$this->clearPathsAndCache();
		}
	}

	/**
	 * @covers ::arrayIsList
	 */
	public function testArrayIsListFallbackWhenNativeFunctionMissing(): void
	{
		Functions\when('function_exists')->alias(function ($name) {
			if ($name === 'array_is_list') {
				return false;
			}
			return \function_exists($name);
		});

		$this->assertTrue(DeprecatedTraitWrapper::arrayIsList([1, 2, 3]));
		$this->assertFalse(DeprecatedTraitWrapper::arrayIsList(['a' => 1]));
		$this->assertFalse(DeprecatedTraitWrapper::arrayIsList([1 => 'a', 2 => 'b']));
	}
}
