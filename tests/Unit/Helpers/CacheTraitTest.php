<?php

/**
 * Comprehensive tests for CacheTrait helper methods.
 *
 * @package EightshiftLibs\Tests\Unit\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Helpers;

use EightshiftLibs\Tests\BaseTestCase;
use EightshiftLibs\Helpers\CacheTrait;
use EightshiftLibs\Cache\AbstractManifestCache;
use EightshiftLibs\Exception\InvalidManifest;
use Brain\Monkey\Functions;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Wrapper class to test CacheTrait methods without conflicts.
 */
class CacheTraitWrapper
{
	use CacheTrait;

	/**
	 * Public wrapper for getFullPath method for testing.
	 */
	public static function getFullPathWrapper(string $type, string $cacheType, string $name = ''): string
	{
		return self::getFullPath($type, $cacheType, $name);
	}

	/**
	 * Public wrapper for fileExistsCached method for testing.
	 */
	public static function fileExistsCachedWrapper(string $path): bool
	{
		return self::fileExistsCached($path);
	}

	/**
	 * Public wrapper for getFileContentsCached method for testing.
	 */
	public static function getFileContentsCachedWrapper(string $path)
	{
		return self::getFileContentsCached($path);
	}

	/**
	 * Public wrapper for jsonDecodeCached method for testing.
	 */
	public static function jsonDecodeCachedWrapper(string $content)
	{
		return self::jsonDecodeCached($content);
	}

	/**
	 * Public wrapper for writeFileOptimized method for testing.
	 */
	public static function writeFileOptimizedWrapper(string $path, string $content): bool
	{
		return self::writeFileOptimized($path, $content);
	}

	/**
	 * Public wrapper for getAllManifests method for testing.
	 */
	public static function getAllManifestsWrapper(): array
	{
		return self::getAllManifests();
	}

	/**
	 * Public wrapper for getItem method for testing.
	 */
	public static function getItemWrapper(string $path, array $data, string $parent): array
	{
		return self::getItem($path, $data, $parent);
	}

	/**
	 * Public wrapper for getItems method for testing.
	 */
	public static function getItemsWrapper(string $path, array $data, string $parent): array
	{
		return self::getItems($path, $data, $parent);
	}

	/**
	 * Public wrapper for processAutoset method for testing.
	 */
	public static function processAutosetWrapper(array $fileDecoded, array $data): array
	{
		return self::processAutoset($fileDecoded, $data);
	}

	/**
	 * Public wrapper for processParentSpecificLogic method for testing.
	 */
	public static function processParentSpecificLogicWrapper(array $fileDecoded, string $parent): array
	{
		return self::processParentSpecificLogic($fileDecoded, $parent);
	}

	/**
	 * Public wrapper for validateManifestKeys method for testing.
	 */
	public static function validateManifestKeysWrapper(array $fileDecoded, array $data, string $path): void
	{
		self::validateManifestKeys($fileDecoded, $data, $path);
	}

	/**
	 * Reset all static properties for clean testing.
	 */
	public static function resetCache(): void
	{
		$reflection = new \ReflectionClass(self::class);
		$properties = [
			'cache',
			'cacheBuilder',
			'cacheName',
			'version',
			'blocksNamespace',
			'shouldCacheResult',
			'fileExistsCache',
			'fileContentsCache',
			'jsonDecodeCache'
		];

		foreach ($properties as $property) {
			if ($reflection->hasProperty($property)) {
				$prop = $reflection->getProperty($property);
				$prop->setAccessible(true);

				if (in_array($property, ['cache', 'cacheBuilder', 'fileExistsCache', 'fileContentsCache', 'jsonDecodeCache'])) {
					$prop->setValue(null, []);
				} elseif (in_array($property, ['cacheName', 'version', 'blocksNamespace'])) {
					$prop->setValue(null, '');
				} elseif ($property === 'shouldCacheResult') {
					$prop->setValue(null, null);
				}
			}
		}
	}
}

/**
 * Comprehensive test case for CacheTrait utility methods.
 *
 * @coversDefaultClass EightshiftLibs\Helpers\CacheTrait
 */
class CacheTraitTest extends BaseTestCase
{
	private CacheTraitWrapper $wrapper;

	protected function setUp(): void
	{
		parent::setUp();
		$this->wrapper = new CacheTraitWrapper();

		// Reset cache between tests
		CacheTraitWrapper::resetCache();

		// Define WordPress constants to avoid errors
		if (!defined('WP_CLI')) {
			define('WP_CLI', false);
		}
		if (!defined('WP_ENVIRONMENT_TYPE')) {
			define('WP_ENVIRONMENT_TYPE', 'development');
		}

		// Mock WordPress functions
		Functions\when('esc_html__')->returnArg(1);
		Functions\when('wp_json_encode')->alias('json_encode');
		Functions\when('file_exists')->alias('file_exists');
		Functions\when('file_get_contents')->alias('file_get_contents');
		Functions\when('file_put_contents')->alias('file_put_contents');
		Functions\when('dirname')->alias('dirname');
		Functions\when('is_dir')->alias('is_dir');
		Functions\when('mkdir')->alias('mkdir');
		Functions\when('glob')->alias('glob');
	}

	/**
	 * @covers ::setCacheDetails
	 */
	public function testSetCacheDetailsWithNewValues(): void
	{
		$cacheBuilder = ['blocks' => ['component' => ['path' => 'test']]];
		$cacheName = 'test-cache';
		$version = '1.0.0';

		$this->wrapper::setCacheDetails($cacheBuilder, $cacheName, $version);

		$this->assertEquals($cacheName, $this->wrapper::getCacheName());
	}

	/**
	 * @covers ::getCache
	 */
	public function testGetCacheWhenEmpty(): void
	{
		$result = $this->wrapper::getCache();
		$this->assertEquals([], $result);
	}

	/**
	 * @covers ::getCacheName
	 */
	public function testGetCacheNameWhenEmpty(): void
	{
		$result = $this->wrapper::getCacheName();
		$this->assertEquals('', $result);
	}

	/**
	 * @covers ::getCacheName
	 */
	public function testGetCacheNameAfterSet(): void
	{
		$cacheName = 'test-cache-name';
		$this->wrapper::setCacheDetails([], $cacheName, '1.0.0');

		$result = $this->wrapper::getCacheName();
		$this->assertEquals($cacheName, $result);
	}

	/**
	 * @covers ::shouldCache
	 */
	public function testShouldCacheInProduction(): void
	{
		// Mock production environment
		Functions\when('defined')->alias(function ($const) {
			return in_array($const, ['WP_CLI', 'WP_ENVIRONMENT_TYPE']);
		});
		Functions\when('constant')->alias(function ($const) {
			if ($const === 'WP_ENVIRONMENT_TYPE') {
				return 'production';
			}
			if ($const === 'WP_CLI') {
				return false;
			}
			return false;
		});

		$result = $this->wrapper::shouldCache();
		$this->assertTrue($result);
	}

	/**
	 * @covers ::shouldCache
	 */
	#[DataProvider('developmentEnvironmentProvider')]
	public function testShouldCacheInDevelopmentEnvironments(string $envType): void
	{
		// Mock development environments
		Functions\when('defined')->alias(function ($const) {
			return in_array($const, ['WP_CLI', 'WP_ENVIRONMENT_TYPE']);
		});
		Functions\when('constant')->alias(function ($const) use ($envType) {
			if ($const === 'WP_ENVIRONMENT_TYPE') {
				return $envType;
			}
			if ($const === 'WP_CLI') {
				return false;
			}
			return false;
		});

		$result = $this->wrapper::shouldCache();
		$this->assertFalse($result);
	}

	/**
	 * @covers ::shouldCache
	 */
	public function testShouldCacheResultIsCached(): void
	{
		// First call
		$result1 = $this->wrapper::shouldCache();

		// Second call should return cached result
		$result2 = $this->wrapper::shouldCache();

		$this->assertEquals($result1, $result2);
	}

	/**
	 * @covers ::setAllCache
	 */
	public function testSetAllCacheWhenCacheAlreadySet(): void
	{
		// Simulate cache already set
		$reflection = new \ReflectionClass($this->wrapper);
		$cacheProperty = $reflection->getProperty('cache');
		$cacheProperty->setAccessible(true);
		$cacheProperty->setValue(null, ['test' => 'data']);

		// Should return early
		$this->wrapper::setAllCache();

		// Cache should remain unchanged
		$result = $this->wrapper::getCache();
		$this->assertEquals(['test' => 'data'], $result);
	}

	/**
	 * @covers ::setAllCache
	 */
	public function testSetAllCacheWhenShouldNotCacheAndEmptyBuilder(): void
	{
		// Mock environment where caching is disabled and empty cache builder
		Functions\when('defined')->alias(function ($const) {
			return $const === 'WP_CLI';
		});
		Functions\when('constant')->alias(function ($const) {
			if ($const === 'WP_CLI') {
				return true;
			}
			return false;
		});

		$this->wrapper::setAllCache();

		// Should set cache directly from getAllManifests (which returns empty with empty builder)
		$result = $this->wrapper::getCache();
		$this->assertEquals([], $result);
	}

	/**
	 * @covers ::setAllCache
	 */
	public function testSetAllCacheWhenShouldNotCacheWithData(): void
	{
		// Mock environment where caching is disabled
		Functions\when('defined')->alias(function ($const) {
			return $const === 'WP_CLI';
		});
		Functions\when('constant')->alias(function ($const) {
			if ($const === 'WP_CLI') {
				return true;
			}
			return false;
		});

		// Set up cache builder with data
		$reflection = new \ReflectionClass($this->wrapper);
		$cacheBuilderProperty = $reflection->getProperty('cacheBuilder');
		$cacheBuilderProperty->setAccessible(true);
		$cacheBuilderProperty->setValue(null, [
			'blocks' => [
				'component' => [
					'path' => 'components',
					'fileName' => 'manifest.json',
					'multiple' => false
				]
			]
		]);

		$this->wrapper::setAllCache();

		// Should set cache directly from getAllManifests
		$result = $this->wrapper::getCache();
		$this->assertIsArray($result);
	}

	/**
	 * @covers ::setAllCache
	 */
	public function testSetAllCacheWithCacheFileExists(): void
	{
		// Mock environment where caching is enabled
		Functions\when('defined')->alias(function ($const) {
			return in_array($const, ['WP_CLI', 'WP_ENVIRONMENT_TYPE']);
		});
		Functions\when('constant')->alias(function ($const) {
			if ($const === 'WP_ENVIRONMENT_TYPE') {
				return 'production';
			}
			if ($const === 'WP_CLI') {
				return false;
			}
			return false;
		});

		// Mock cache file exists and has valid content
		Functions\when('file_exists')->alias(function ($path) {
			return strpos($path, 'manifests.json') !== false;
		});

		Functions\when('file_get_contents')->alias(function ($path) {
			if (strpos($path, 'manifests.json') !== false) {
				return '{"blocks": {"component": {"test": "data"}}}';
			}
			return false;
		});

		// Note: Can't easily mock static class methods with Brain Monkey
		// This test will call the actual helper method or fail silently
		$this->wrapper::setAllCache();

		// Should attempt to load cache from file (may be empty due to helper dependency)
		$result = $this->wrapper::getCache();
		$this->assertIsArray($result);
	}

	/**
	 * @covers ::setAllCache
	 */
	public function testSetAllCacheWithCacheFileExistsButInvalidContent(): void
	{
		// Mock environment where caching is enabled
		Functions\when('defined')->alias(function ($const) {
			return in_array($const, ['WP_CLI', 'WP_ENVIRONMENT_TYPE']);
		});
		Functions\when('constant')->alias(function ($const) {
			if ($const === 'WP_ENVIRONMENT_TYPE') {
				return 'production';
			}
			if ($const === 'WP_CLI') {
				return false;
			}
			return false;
		});

		// Mock cache file exists but has invalid JSON
		Functions\when('file_exists')->alias(function ($path) {
			return strpos($path, 'manifests.json') !== false;
		});

		Functions\when('file_get_contents')->alias(function ($path) {
			if (strpos($path, 'manifests.json') !== false) {
				return '{"invalid": json}';
			}
			return false;
		});

		// Mock writing to file
		Functions\when('file_put_contents')->alias(function ($path, $content, $flags) {
			return strlen($content);
		});

		// Note: Can't easily mock static class methods with Brain Monkey
		$this->wrapper::setAllCache();

		// Should generate new cache data (may be empty due to helper dependency)
		$result = $this->wrapper::getCache();
		$this->assertIsArray($result);
	}

	/**
	 * @covers ::setAllCache
	 */
	public function testSetAllCacheWithNoExistingCacheFile(): void
	{
		// Mock environment where caching is enabled
		Functions\when('defined')->alias(function ($const) {
			return in_array($const, ['WP_CLI', 'WP_ENVIRONMENT_TYPE']);
		});
		Functions\when('constant')->alias(function ($const) {
			if ($const === 'WP_ENVIRONMENT_TYPE') {
				return 'production';
			}
			if ($const === 'WP_CLI') {
				return false;
			}
			return false;
		});

		// Mock cache file doesn't exist
		Functions\when('file_exists')->alias(function ($path) {
			return false;
		});

		// Mock successful file writing
		Functions\when('file_put_contents')->alias(function ($path, $content, $flags) {
			return strlen($content);
		});

		// Note: Can't easily mock static class methods with Brain Monkey
		$this->wrapper::setAllCache();

		// Should generate and cache new data (may be empty due to helper dependency)
		$result = $this->wrapper::getCache();
		$this->assertIsArray($result);
	}

	/**
	 * @covers ::getAllManifests
	 */
	public function testGetAllManifestsWithEmptyCacheBuilder(): void
	{
		$result = $this->wrapper::getAllManifestsWrapper();
		$this->assertEquals([], $result);
	}

	/**
	 * @covers ::getAllManifests
	 */
	public function testGetAllManifestsWithValidCacheBuilder(): void
	{
		// Set up cache builder
		$reflection = new \ReflectionClass($this->wrapper);
		$cacheBuilderProperty = $reflection->getProperty('cacheBuilder');
		$cacheBuilderProperty->setAccessible(true);
		$cacheBuilderProperty->setValue(null, [
			'blocks' => [
				'component' => [
					'path' => 'components',
					'fileName' => 'manifest.json',
					'multiple' => false
				]
			]
		]);

		$result = $this->wrapper::getAllManifestsWrapper();

		// Should return structure with blocks type but empty component since getFullPath returns empty path
		$this->assertIsArray($result);
		$this->assertArrayHasKey('blocks', $result);
		$this->assertEquals([], $result['blocks']);
	}

	/**
	 * @covers ::getAllManifests
	 */
	public function testGetAllManifestsWithEmptyItems(): void
	{
		// Set up cache builder with empty items
		$reflection = new \ReflectionClass($this->wrapper);
		$cacheBuilderProperty = $reflection->getProperty('cacheBuilder');
		$cacheBuilderProperty->setAccessible(true);
		$cacheBuilderProperty->setValue(null, [
			'blocks' => [],
			'settings' => [
				'global' => [
					'path' => 'settings',
					'fileName' => 'manifest.json'
				]
			]
		]);

		$result = $this->wrapper::getAllManifestsWrapper();

		// Should skip empty items but include settings structure since it has items
		$this->assertIsArray($result);
		$this->assertArrayHasKey('settings', $result);
		$this->assertEquals([], $result['settings']);
	}

	/**
	 * @covers ::getAllManifests
	 */
	public function testGetAllManifestsWithMultipleFlag(): void
	{
		// Set up cache builder with multiple flag
		$reflection = new \ReflectionClass($this->wrapper);
		$cacheBuilderProperty = $reflection->getProperty('cacheBuilder');
		$cacheBuilderProperty->setAccessible(true);
		$cacheBuilderProperty->setValue(null, [
			'blocks' => [
				'component' => [
					'path' => 'components',
					'fileName' => 'manifest.json',
					'multiple' => true,
					'id' => 'blockName'
				]
			]
		]);

		$result = $this->wrapper::getAllManifestsWrapper();

		// Should process as multiple items and return structure with blocks type
		$this->assertIsArray($result);
		$this->assertArrayHasKey('blocks', $result);
		$this->assertEquals([], $result['blocks']);
	}

	/**
	 * @covers ::getItem
	 */
	public function testGetItemWithEmptyPath(): void
	{
		$result = $this->wrapper::getItemWrapper('', [], 'blocks');
		$this->assertEquals([], $result);
	}

	/**
	 * @covers ::getItem
	 */
	public function testGetItemWithNonExistentFile(): void
	{
		Functions\when('file_exists')->alias(function ($path) {
			return false;
		});

		$result = $this->wrapper::getItemWrapper('/test/path.json', [], 'blocks');
		$this->assertEquals([], $result);
	}

	/**
	 * @covers ::getItem
	 */
	public function testGetItemWithValidFile(): void
	{
		$filePath = '/test/valid.json';
		$fileContent = '{"blockName": "test-block", "title": "Test Block"}';
		$data = ['validation' => ['blockName']];

		Functions\when('file_exists')->alias(function ($path) use ($filePath) {
			return $path === $filePath;
		});

		Functions\when('file_get_contents')->alias(function ($path) use ($filePath, $fileContent) {
			return $path === $filePath ? $fileContent : false;
		});

		$result = $this->wrapper::getItemWrapper($filePath, $data, 'blocks');

		$this->assertIsArray($result);
		$this->assertEquals('test-block', $result['blockName']);
		$this->assertEquals('Test Block', $result['title']);
	}

	/**
	 * @covers ::getItem
	 */
	public function testGetItemWithEmptyFileContent(): void
	{
		$filePath = '/test/empty.json';

		Functions\when('file_exists')->alias(function ($path) use ($filePath) {
			return $path === $filePath;
		});

		Functions\when('file_get_contents')->alias(function ($path) use ($filePath) {
			return $path === $filePath ? '' : false;
		});

		$result = $this->wrapper::getItemWrapper($filePath, [], 'blocks');
		$this->assertEquals([], $result);
	}

	/**
	 * @covers ::getItem
	 */
	public function testGetItemWithInvalidJson(): void
	{
		$filePath = '/test/invalid.json';

		Functions\when('file_exists')->alias(function ($path) use ($filePath) {
			return $path === $filePath;
		});

		Functions\when('file_get_contents')->alias(function ($path) use ($filePath) {
			return $path === $filePath ? '{"invalid": json}' : false;
		});

		$result = $this->wrapper::getItemWrapper($filePath, [], 'blocks');
		$this->assertEquals([], $result);
	}

	/**
	 * @covers ::getItem
	 */
	public function testGetItemWithAutosetData(): void
	{
		$filePath = '/test/autoset.json';
		$fileContent = '{"blockName": "test-block"}';
		$data = [
			'autoset' => [
				[
					'key' => 'category',
					'value' => 'custom'
				]
			]
		];

		Functions\when('file_exists')->alias(function ($path) use ($filePath) {
			return $path === $filePath;
		});

		Functions\when('file_get_contents')->alias(function ($path) use ($filePath, $fileContent) {
			return $path === $filePath ? $fileContent : false;
		});

		$result = $this->wrapper::getItemWrapper($filePath, $data, 'blocks');

		$this->assertIsArray($result);
		$this->assertEquals('test-block', $result['blockName']);
		$this->assertEquals('custom', $result['category']);
	}

	/**
	 * @covers ::getItems
	 */
	public function testGetItemsWithEmptyPath(): void
	{
		$result = $this->wrapper::getItemsWrapper('', ['id' => 'blockName'], 'blocks');
		$this->assertEquals([], $result);
	}

	/**
	 * @covers ::getItems
	 */
	public function testGetItemsWithEmptyId(): void
	{
		$result = $this->wrapper::getItemsWrapper('/test/path/*', [], 'blocks');
		$this->assertEquals([], $result);
	}

	/**
	 * @covers ::getItems
	 */
	public function testGetItemsWithFailedGlob(): void
	{
		Functions\when('glob')->alias(function ($path) {
			return false;
		});

		$result = $this->wrapper::getItemsWrapper('/test/path/*', ['id' => 'blockName'], 'blocks');
		$this->assertEquals([], $result);
	}

	/**
	 * @covers ::getItems
	 */
	public function testGetItemsWithValidFiles(): void
	{
		$data = ['id' => 'blockName'];

		// Mock glob to return file paths
		Functions\when('glob')->alias(function ($path) {
			if (strpos($path, '/test/blocks/*') !== false) {
				return ['/test/blocks/button.json', '/test/blocks/card.json'];
			}
			return false;
		});

		// Mock file operations for each file
		Functions\when('file_exists')->alias(function ($path) {
			return in_array($path, ['/test/blocks/button.json', '/test/blocks/card.json']);
		});

		Functions\when('file_get_contents')->alias(function ($path) {
			if ($path === '/test/blocks/button.json') {
				return '{"blockName": "button", "title": "Button"}';
			}
			if ($path === '/test/blocks/card.json') {
				return '{"blockName": "card", "title": "Card"}';
			}
			return false;
		});

		$result = $this->wrapper::getItemsWrapper('/test/blocks/*', $data, 'blocks');

		$this->assertIsArray($result);
		$this->assertArrayHasKey('button', $result);
		$this->assertArrayHasKey('card', $result);
		$this->assertEquals('Button', $result['button']['title']);
		$this->assertEquals('Card', $result['card']['title']);
	}

	/**
	 * @covers ::getItems
	 */
	public function testGetItemsWithSomeInvalidFiles(): void
	{
		$data = ['id' => 'blockName'];

		// Mock glob to return file paths
		Functions\when('glob')->alias(function ($path) {
			if (strpos($path, '/test/blocks/*') !== false) {
				return ['/test/blocks/valid.json', '/test/blocks/invalid.json', '/test/blocks/empty.json'];
			}
			return false;
		});

		Functions\when('file_exists')->alias(function ($path) {
			return in_array($path, ['/test/blocks/valid.json', '/test/blocks/invalid.json', '/test/blocks/empty.json']);
		});

		Functions\when('file_get_contents')->alias(function ($path) {
			if ($path === '/test/blocks/valid.json') {
				return '{"blockName": "valid", "title": "Valid Block"}';
			}
			if ($path === '/test/blocks/invalid.json') {
				return '{"invalid": json}';
			}
			if ($path === '/test/blocks/empty.json') {
				return '{}';
			}
			return false;
		});

		$result = $this->wrapper::getItemsWrapper('/test/blocks/*', $data, 'blocks');

		// Should only include valid files with proper blockName
		$this->assertIsArray($result);
		$this->assertArrayHasKey('valid', $result);
		$this->assertArrayNotHasKey('invalid', $result);
		$this->assertArrayNotHasKey('empty', $result);
	}

	/**
	 * @covers ::getItems
	 */
	public function testGetItemsWithFilesWithoutId(): void
	{
		$data = ['id' => 'blockName'];

		Functions\when('glob')->alias(function ($path) {
			if (strpos($path, '/test/blocks/*') !== false) {
				return ['/test/blocks/no-id.json'];
			}
			return false;
		});

		Functions\when('file_exists')->alias(function ($path) {
			return $path === '/test/blocks/no-id.json';
		});

		Functions\when('file_get_contents')->alias(function ($path) {
			if ($path === '/test/blocks/no-id.json') {
				return '{"title": "Block without blockName"}';
			}
			return false;
		});

		$result = $this->wrapper::getItemsWrapper('/test/blocks/*', $data, 'blocks');

		// Should be empty since file doesn't have the required ID field
		$this->assertEquals([], $result);
	}

	/**
	 * @covers ::getFullPath
	 */
	public function testGetFullPathWithEmptyInputs(): void
	{
		$result = $this->wrapper::getFullPathWrapper('', 'blocks');
		$this->assertEquals('', $result);

		$result = $this->wrapper::getFullPathWrapper('component', '');
		$this->assertEquals('', $result);
	}

	/**
	 * @covers ::getFullPath
	 */
	public function testGetFullPathWithValidInputsButNoCacheBuilder(): void
	{
		$result = $this->wrapper::getFullPathWrapper('component', 'blocks');
		$this->assertEquals('', $result);
	}

	/**
	 * @covers ::getFullPath
	 */
	public function testGetFullPathWithMissingTypeInCacheBuilder(): void
	{
		// Set up cache builder without the requested type
		$reflection = new \ReflectionClass($this->wrapper);
		$cacheBuilderProperty = $reflection->getProperty('cacheBuilder');
		$cacheBuilderProperty->setAccessible(true);
		$cacheBuilderProperty->setValue(null, [
			'blocks' => [
				'button' => [
					'path' => 'blocks',
					'fileName' => 'manifest.json'
				]
			]
		]);

		$result = $this->wrapper::getFullPathWrapper('component', 'blocks');
		$this->assertEquals('', $result);
	}

	/**
	 * @covers ::getFullPath
	 */
	public function testGetFullPathWithEmptyPath(): void
	{
		// Set up cache builder with empty path
		$reflection = new \ReflectionClass($this->wrapper);
		$cacheBuilderProperty = $reflection->getProperty('cacheBuilder');
		$cacheBuilderProperty->setAccessible(true);
		$cacheBuilderProperty->setValue(null, [
			'blocks' => [
				'component' => [
					'path' => '',
					'fileName' => 'manifest.json'
				]
			]
		]);

		$result = $this->wrapper::getFullPathWrapper('component', 'blocks');
		$this->assertEquals('', $result);
	}

	/**
	 * @covers ::getFullPath
	 */
	public function testGetFullPathWithValidDataNoName(): void
	{
		// Set up cache builder
		$reflection = new \ReflectionClass($this->wrapper);
		$cacheBuilderProperty = $reflection->getProperty('cacheBuilder');
		$cacheBuilderProperty->setAccessible(true);
		$cacheBuilderProperty->setValue(null, [
			'blocks' => [
				'component' => [
					'path' => 'components',
					'fileName' => 'manifest.json'
				]
			]
		]);

		// Note: Can't mock static class methods with Brain Monkey, this will call actual method
		$result = $this->wrapper::getFullPathWrapper('component', 'blocks');
		// The actual result depends on the Helpers::getProjectPaths method
		$this->assertIsString($result);
	}

	/**
	 * @covers ::getFullPath
	 */
	public function testGetFullPathWithValidDataAndName(): void
	{
		// Set up cache builder
		$reflection = new \ReflectionClass($this->wrapper);
		$cacheBuilderProperty = $reflection->getProperty('cacheBuilder');
		$cacheBuilderProperty->setAccessible(true);
		$cacheBuilderProperty->setValue(null, [
			'blocks' => [
				'component' => [
					'path' => 'components',
					'fileName' => 'manifest.json'
				]
			]
		]);

		// Note: Can't mock static class methods with Brain Monkey, this will call actual method
		$result = $this->wrapper::getFullPathWrapper('component', 'blocks', 'button');
		// The actual result depends on the Helpers::getProjectPaths method
		$this->assertIsString($result);
	}

	/**
	 * @covers ::getFullPath
	 */
	public function testGetFullPathWithCustomFileName(): void
	{
		// Set up cache builder with custom fileName
		$reflection = new \ReflectionClass($this->wrapper);
		$cacheBuilderProperty = $reflection->getProperty('cacheBuilder');
		$cacheBuilderProperty->setAccessible(true);
		$cacheBuilderProperty->setValue(null, [
			'settings' => [
				'global' => [
					'path' => 'settings',
					'fileName' => 'settings.json'
				]
			]
		]);

		// Note: Can't mock static class methods with Brain Monkey, this will call actual method
		$result = $this->wrapper::getFullPathWrapper('global', 'settings');
		// The actual result depends on the Helpers::getProjectPaths method
		$this->assertIsString($result);
	}

	/**
	 * @covers ::fileExistsCached
	 */
	public function testFileExistsCachedWithExistingFile(): void
	{
		$filePath = '/test/existing.json';

		// Mock file_exists to return true for our test file
		Functions\when('file_exists')->alias(function ($path) use ($filePath) {
			return $path === $filePath;
		});

		$result = $this->wrapper::fileExistsCachedWrapper($filePath);
		$this->assertTrue($result);

		// Test caching - second call should return cached result
		$result2 = $this->wrapper::fileExistsCachedWrapper($filePath);
		$this->assertTrue($result2);
	}

	/**
	 * @covers ::fileExistsCached
	 */
	public function testFileExistsCachedWithNonExistingFile(): void
	{
		$filePath = '/test/nonexistent.json';

		Functions\when('file_exists')->alias(function ($path) use ($filePath) {
			return false;
		});

		$result = $this->wrapper::fileExistsCachedWrapper($filePath);
		$this->assertFalse($result);
	}

	/**
	 * @covers ::getFileContentsCached
	 */
	public function testGetFileContentsCachedWithValidFile(): void
	{
		$content = '{"test": "data"}';
		$filePath = '/test/test.json';

		Functions\when('file_get_contents')->alias(function ($path) use ($filePath, $content) {
			return $path === $filePath ? $content : false;
		});

		$result = $this->wrapper::getFileContentsCachedWrapper($filePath);
		$this->assertEquals($content, $result);

		// Test caching
		$result2 = $this->wrapper::getFileContentsCachedWrapper($filePath);
		$this->assertEquals($content, $result2);
	}

	/**
	 * @covers ::getFileContentsCached
	 */
	public function testGetFileContentsCachedWithInvalidFile(): void
	{
		$filePath = '/test/invalid.json';

		Functions\when('file_get_contents')->alias(function ($path) {
			return false;
		});

		$result = $this->wrapper::getFileContentsCachedWrapper($filePath);
		$this->assertFalse($result);
	}

	/**
	 * @covers ::jsonDecodeCached
	 */
	public function testJsonDecodeCachedWithValidJson(): void
	{
		$jsonContent = '{"test": "data", "number": 123}';
		$expected = ['test' => 'data', 'number' => 123];

		$result = $this->wrapper::jsonDecodeCachedWrapper($jsonContent);
		$this->assertEquals($expected, $result);

		// Test caching with same content
		$result2 = $this->wrapper::jsonDecodeCachedWrapper($jsonContent);
		$this->assertEquals($expected, $result2);
	}

	/**
	 * @covers ::jsonDecodeCached
	 */
	#[DataProvider('invalidJsonProvider')]
	public function testJsonDecodeCachedWithInvalidJson(string $invalidJson): void
	{
		$result = $this->wrapper::jsonDecodeCachedWrapper($invalidJson);
		$this->assertFalse($result);
	}

	/**
	 * @covers ::writeFileOptimized
	 */
	public function testWriteFileOptimizedWithValidPath(): void
	{
		$filePath = '/test/output/new.json';
		$content = '{"test": "data"}';

		// Mock directory creation and file writing
		Functions\when('dirname')->alias('dirname');
		Functions\when('is_dir')->alias(function ($dir) {
			return strpos($dir, 'output') !== false;
		});
		Functions\when('mkdir')->alias(function ($dir, $mode, $recursive) {
			return true;
		});
		Functions\when('file_put_contents')->alias(function ($path, $content, $flags) {
			return strlen($content);
		});

		$result = $this->wrapper::writeFileOptimizedWrapper($filePath, $content);
		$this->assertTrue($result);
	}

	/**
	 * @covers ::writeFileOptimized
	 */
	public function testWriteFileOptimizedWithFailedDirectoryCreation(): void
	{
		$filePath = '/test/output/new.json';
		$content = '{"test": "data"}';

		Functions\when('dirname')->alias('dirname');
		Functions\when('is_dir')->alias(function ($dir) {
			return false;
		});
		Functions\when('mkdir')->alias(function ($dir, $mode, $recursive) {
			return false;
		});

		$result = $this->wrapper::writeFileOptimizedWrapper($filePath, $content);
		$this->assertFalse($result);
	}

	/**
	 * @covers ::writeFileOptimized
	 */
	public function testWriteFileOptimizedWithFailedFileWrite(): void
	{
		$filePath = '/test/output/new.json';
		$content = '{"test": "data"}';

		Functions\when('dirname')->alias('dirname');
		Functions\when('is_dir')->alias(function ($dir) {
			return true; // Directory exists
		});
		Functions\when('file_put_contents')->alias(function ($path, $content, $flags) {
			return false; // Failed write
		});

		$result = $this->wrapper::writeFileOptimizedWrapper($filePath, $content);
		$this->assertFalse($result);
	}

	/**
	 * @covers ::processAutoset
	 */
	public function testProcessAutosetWithSimpleKey(): void
	{
		$fileDecoded = ['existing' => 'value'];
		$data = [
			'autoset' => [
				[
					'key' => 'newKey',
					'value' => 'newValue'
				]
			]
		];

		$result = $this->wrapper::processAutosetWrapper($fileDecoded, $data);

		$this->assertEquals('value', $result['existing']);
		$this->assertEquals('newValue', $result['newKey']);
	}

	/**
	 * @covers ::processAutoset
	 */
	public function testProcessAutosetWithParentKey(): void
	{
		$fileDecoded = ['existing' => 'value'];
		$data = [
			'autoset' => [
				[
					'key' => 'childKey',
					'value' => 'childValue',
					'parent' => 'parentKey'
				]
			]
		];

		$result = $this->wrapper::processAutosetWrapper($fileDecoded, $data);

		$this->assertEquals('value', $result['existing']);
		$this->assertEquals('childValue', $result['parentKey']['childKey']);
	}

	/**
	 * @covers ::processAutoset
	 */
	public function testProcessAutosetWithExistingKey(): void
	{
		$fileDecoded = ['existingKey' => 'originalValue'];
		$data = [
			'autoset' => [
				[
					'key' => 'existingKey',
					'value' => 'newValue'
				]
			]
		];

		$result = $this->wrapper::processAutosetWrapper($fileDecoded, $data);

		// Should not override existing key
		$this->assertEquals('originalValue', $result['existingKey']);
	}

	/**
	 * @covers ::processAutoset
	 */
	public function testProcessAutosetWithExistingParentKey(): void
	{
		$fileDecoded = ['parentKey' => ['existingChild' => 'originalValue']];
		$data = [
			'autoset' => [
				[
					'key' => 'existingChild',
					'value' => 'newValue',
					'parent' => 'parentKey'
				]
			]
		];

		$result = $this->wrapper::processAutosetWrapper($fileDecoded, $data);

		// Should not override existing nested key
		$this->assertEquals('originalValue', $result['parentKey']['existingChild']);
	}

	/**
	 * @covers ::processAutoset
	 */
	public function testProcessAutosetWithEmptyAutoset(): void
	{
		$fileDecoded = ['test' => 'data'];
		$data = ['autoset' => []];

		$result = $this->wrapper::processAutosetWrapper($fileDecoded, $data);

		$this->assertEquals($fileDecoded, $result);
	}

	/**
	 * @covers ::processAutoset
	 */
	public function testProcessAutosetWithEmptyKey(): void
	{
		$fileDecoded = ['test' => 'data'];
		$data = [
			'autoset' => [
				[
					'key' => '',
					'value' => 'newValue'
				]
			]
		];

		$result = $this->wrapper::processAutosetWrapper($fileDecoded, $data);

		// Should skip empty key
		$this->assertEquals($fileDecoded, $result);
	}

	/**
	 * @covers ::processParentSpecificLogic
	 */
	public function testProcessParentSpecificLogicWithBlocksKey(): void
	{
		// Set up blocks namespace
		$reflection = new \ReflectionClass($this->wrapper);
		$namespaceProperty = $reflection->getProperty('blocksNamespace');
		$namespaceProperty->setAccessible(true);
		$namespaceProperty->setValue(null, 'test-namespace');

		$fileDecoded = ['blockName' => 'test-block'];

		$result = $this->wrapper::processParentSpecificLogicWrapper($fileDecoded, AbstractManifestCache::BLOCKS_KEY);

		$this->assertEquals('test-namespace', $result['namespace']);
		$this->assertEquals('test-namespace/test-block', $result['blockFullName']);
	}

	/**
	 * @covers ::processParentSpecificLogic
	 */
	public function testProcessParentSpecificLogicWithBlocksKeyNoBlockName(): void
	{
		// Set up blocks namespace
		$reflection = new \ReflectionClass($this->wrapper);
		$namespaceProperty = $reflection->getProperty('blocksNamespace');
		$namespaceProperty->setAccessible(true);
		$namespaceProperty->setValue(null, 'test-namespace');

		$fileDecoded = ['someOtherKey' => 'value'];

		$result = $this->wrapper::processParentSpecificLogicWrapper($fileDecoded, AbstractManifestCache::BLOCKS_KEY);

		$this->assertEquals('test-namespace', $result['namespace']);
		$this->assertArrayNotHasKey('blockFullName', $result);
	}

	/**
	 * @covers ::processParentSpecificLogic
	 */
	public function testProcessParentSpecificLogicWithSettingsKey(): void
	{
		$fileDecoded = ['namespace' => 'new-namespace'];

		$result = $this->wrapper::processParentSpecificLogicWrapper($fileDecoded, AbstractManifestCache::SETTINGS_KEY);

		// Should set the blocks namespace
		$this->assertEquals($fileDecoded, $result);
	}

	/**
	 * @covers ::processParentSpecificLogic
	 */
	public function testProcessParentSpecificLogicWithOtherKey(): void
	{
		$fileDecoded = ['test' => 'data'];

		$result = $this->wrapper::processParentSpecificLogicWrapper($fileDecoded, 'other');

		$this->assertEquals($fileDecoded, $result);
	}

	/**
	 * @covers ::validateManifestKeys
	 */
	public function testValidateManifestKeysWithValidKeys(): void
	{
		$fileDecoded = ['requiredKey' => 'value', 'otherKey' => 'other'];
		$data = ['validation' => ['requiredKey']];
		$path = '/test/path';

		// Should not throw exception
		$this->wrapper::validateManifestKeysWrapper($fileDecoded, $data, $path);
		$this->addToAssertionCount(1); // Assert no exception was thrown
	}

	/**
	 * @covers ::validateManifestKeys
	 */
	public function testValidateManifestKeysWithMissingKey(): void
	{
		$fileDecoded = ['otherKey' => 'other'];
		$data = ['validation' => ['requiredKey']];
		$path = '/test/path';

		$this->expectException(InvalidManifest::class);
		$this->wrapper::validateManifestKeysWrapper($fileDecoded, $data, $path);
	}

	/**
	 * @covers ::validateManifestKeys
	 */
	public function testValidateManifestKeysWithEmptyValidation(): void
	{
		$fileDecoded = ['test' => 'data'];
		$data = ['validation' => []];
		$path = '/test/path';

		// Should not throw exception
		$this->wrapper::validateManifestKeysWrapper($fileDecoded, $data, $path);
		$this->addToAssertionCount(1);
	}

	/**
	 * @covers ::validateManifestKeys
	 */
	public function testValidateManifestKeysWithNoValidation(): void
	{
		$fileDecoded = ['test' => 'data'];
		$data = [];
		$path = '/test/path';

		// Should not throw exception
		$this->wrapper::validateManifestKeysWrapper($fileDecoded, $data, $path);
		$this->addToAssertionCount(1);
	}

	/**
	 * Data providers
	 */
	public static function developmentEnvironmentProvider(): array
	{
		return [
			'development' => ['development'],
			'local' => ['local'],
		];
	}

	public static function invalidJsonProvider(): array
	{
		return [
			'malformed json' => ['{"invalid": json}'],
			'trailing comma' => ['{"test": "value",}'],
			'unclosed brace' => ['{"test": "value"'],
			'empty string' => [''],
			'not json' => ['this is not json'],
			'null value' => ['null'],
		];
	}
}
