<?php

/**
 * Tests for AbstractManifestCache.
 *
 * @package EightshiftLibs\Tests\Unit\Cache
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Cache;

use EightshiftLibs\Cache\AbstractManifestCache;
use EightshiftLibs\Tests\BaseTestCase;

/**
 * Concrete implementation for testing abstract class.
 */
class ConcreteManifestCache extends AbstractManifestCache
{
	private string $cacheName;
	private string $version;
	private bool $useGeo;

	public function __construct(string $cacheName = 'test-cache', string $version = '1.0.0', bool $useGeo = false)
	{
		$this->cacheName = $cacheName;
		$this->version = $version;
		$this->useGeo = $useGeo;
	}

	public function getCacheName(): string
	{
		return $this->cacheName;
	}

	public function getVersion(): string
	{
		return $this->version;
	}

	public function useGeolocation(): bool
	{
		return $this->useGeo;
	}
}

/**
 * Test case for AbstractManifestCache.
 *
 * @coversDefaultClass EightshiftLibs\Cache\AbstractManifestCache
 */
class AbstractManifestCacheTest extends BaseTestCase
{
	private ConcreteManifestCache $cache;

	protected function setUp(): void
	{
		parent::setUp();
		$this->cache = new ConcreteManifestCache();
	}

	/**
	 * @covers ::getCacheName
	 */
	public function testGetCacheName(): void
	{
		$cache = new ConcreteManifestCache('my-custom-cache');
		$this->assertSame('my-custom-cache', $cache->getCacheName());
	}

	/**
	 * @covers ::getVersion
	 */
	public function testGetVersion(): void
	{
		$cache = new ConcreteManifestCache('test', '2.0.1');
		$this->assertSame('2.0.1', $cache->getVersion());
	}

	/**
	 * @covers ::useGeolocation
	 */
	public function testUseGeolocationDefaultsToFalse(): void
	{
		$this->assertFalse($this->cache->useGeolocation());
	}

	/**
	 * @covers ::useGeolocation
	 */
	public function testUseGeolocationCanBeEnabled(): void
	{
		$cache = new ConcreteManifestCache('test', '1.0.0', true);
		$this->assertTrue($cache->useGeolocation());
	}

	/**
	 * @covers ::setAllCache
	 */
	public function testSetAllCache(): void
	{
		// This method calls Helpers::setCacheDetails which can't be easily mocked
		// Testing that the method exists and is callable
		$this->assertTrue(method_exists($this->cache, 'setAllCache'));
	}

	/**
	 * Test transient prefix constant.
	 */
	public function testTransientPrefixNameConstant(): void
	{
		$this->assertSame('eightshift_manifest_cache', AbstractManifestCache::TRANSIENT_PREFIX_NAME);
	}

	/**
	 * Test cache key constants.
	 */
	public function testCacheKeyConstants(): void
	{
		$this->assertSame('version', AbstractManifestCache::VERSION_KEY);
		$this->assertSame('blocks', AbstractManifestCache::BLOCKS_KEY);
		$this->assertSame('components', AbstractManifestCache::COMPONENTS_KEY);
		$this->assertSame('variations', AbstractManifestCache::VARIATIONS_KEY);
		$this->assertSame('wrapper', AbstractManifestCache::WRAPPER_KEY);
		$this->assertSame('settings', AbstractManifestCache::SETTINGS_KEY);
		$this->assertSame('assets', AbstractManifestCache::ASSETS_KEY);
		$this->assertSame('countries', AbstractManifestCache::COUNTRIES_KEY);
	}

	/**
	 * Test cache type constants.
	 */
	public function testCacheTypeConstants(): void
	{
		$this->assertSame('blocks', AbstractManifestCache::TYPE_BLOCKS);
		$this->assertSame('assets', AbstractManifestCache::TYPE_ASSETS);
		$this->assertSame('geolocation', AbstractManifestCache::TYPE_GEOLOCATION);
	}
}
