<?php

/**
 * Tests for the production service cache handling in AbstractMain.
 *
 * @package EightshiftLibs\Tests\Unit\Main
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Main;

use EightshiftLibs\Helpers\Helpers;
use EightshiftLibs\Main\AbstractMain;
use EightshiftLibs\Tests\BaseTestCase;

/**
 * Plain service used as a cached DI target.
 */
class CacheTestService
{
}

/**
 * AbstractMain double that counts filesystem scans.
 */
class CountingMain extends AbstractMain
{
	/**
	 * Number of times the autowiring scan ran.
	 */
	public int $scanCount = 0;

	/**
	 * Services the fake scan returns.
	 *
	 * @var array<string, mixed>
	 */
	public array $scanResult = [CacheTestService::class => []];

	/**
	 * Count scans instead of walking the filesystem.
	 *
	 * @param array<string, mixed> $manuallyDefinedDependencies Unused.
	 * @param bool $skipInvalid Unused.
	 *
	 * @return array<string, mixed>
	 */
	public function buildServiceClasses(array $manuallyDefinedDependencies = [], bool $skipInvalid = false): array
	{
		$this->scanCount++;
		return $this->scanResult;
	}

	/**
	 * Satisfy ServiceInterface; unused here.
	 */
	public function register(): void
	{
	}

	/**
	 * Expose the protected method under test.
	 *
	 * @return object[]
	 */
	public function instances(): array
	{
		return $this->getServiceClassesWithDi();
	}
}

/**
 * @coversDefaultClass EightshiftLibs\Main\AbstractMain
 */
class AbstractMainCacheTest extends BaseTestCase
{
	private const NAMESPACE_ROOT = 'EsLibsCacheTest';

	private string $cacheFile;

	private string $projectRoot;

	/**
	 * @var array<string, string>|null
	 */
	private ?array $originalBasePaths = null;

	/**
	 * @var array<string, string[]>|null
	 */
	private ?array $originalPathConfigs = null;

	protected function setUp(): void
	{
		parent::setUp();

		// The output directory is memoized per process, so use whichever one is in effect.
		// Point it at a throwaway location first in case nothing has memoized it yet.
		$basePaths = new \ReflectionProperty(Helpers::class, 'basePaths');
		$pathConfigs = new \ReflectionProperty(Helpers::class, 'pathConfigs');
		$this->originalBasePaths = $basePaths->getValue();
		$this->originalPathConfigs = $pathConfigs->getValue();

		$this->projectRoot = \sys_get_temp_dir() . '/es-libs-main-cache-' . \getmypid();
		$basePaths->setValue(null, ['root' => $this->projectRoot]);
		$pathConfigs->setValue(null, ['eightshift' => [$this->projectRoot, 'eightshift']]);

		$outputPath = Helpers::getEightshiftOutputPath();
		if (!\is_dir($outputPath) || !\is_writable($outputPath)) {
			$this->restorePaths();
			$this->markTestSkipped("Output directory {$outputPath} is not writable.");
		}

		$this->setShouldCache(true);
		$this->cacheFile = Helpers::getEightshiftOutputPath(self::NAMESPACE_ROOT . 'ServiceClasses.json');
		$this->cleanUp();
	}

	protected function tearDown(): void
	{
		$this->cleanUp();
		$this->setShouldCache(null);
		$this->restorePaths();

		parent::tearDown();
	}

	/**
	 * @covers ::getServiceClassesWithDi
	 */
	public function testValidProductionCacheSkipsScan(): void
	{
		\file_put_contents($this->cacheFile, \json_encode(['services' => [CacheTestService::class => []]]));

		$main = $this->makeMain();
		$instances = $main->instances();

		$this->assertSame(0, $main->scanCount);
		$this->assertCount(1, $instances);
		$this->assertInstanceOf(CacheTestService::class, $instances[0]);
	}

	/**
	 * @covers ::getServiceClassesWithDi
	 */
	public function testMissingCacheScansAndWritesCache(): void
	{
		$main = $this->makeMain();
		$instances = $main->instances();

		$this->assertSame(1, $main->scanCount);
		$this->assertCount(1, $instances);
		$this->assertFileExists($this->cacheFile);
		$this->assertSame(
			['services' => [CacheTestService::class => []]],
			\json_decode((string) \file_get_contents($this->cacheFile), true)
		);

		// The next request reads the cache and does not scan again.
		$second = $this->makeMain();
		$second->instances();
		$this->assertSame(0, $second->scanCount);
	}

	/**
	 * @covers ::getServiceClassesWithDi
	 */
	public function testMalformedCacheScansAndRewritesCache(): void
	{
		\file_put_contents($this->cacheFile, '{not json');

		$main = $this->makeMain();
		$instances = $main->instances();

		$this->assertSame(1, $main->scanCount);
		$this->assertCount(1, $instances);
		$this->assertSame(
			['services' => [CacheTestService::class => []]],
			\json_decode((string) \file_get_contents($this->cacheFile), true)
		);
	}

	/**
	 * @covers ::getServiceClassesWithDi
	 */
	public function testEmptyScanReturnsNoServicesAndWritesNothing(): void
	{
		$main = $this->makeMain();
		$main->scanResult = [];

		$this->assertSame([], $main->instances());
		$this->assertSame(1, $main->scanCount);
		$this->assertFileDoesNotExist($this->cacheFile);
	}

	/**
	 * @covers ::getServiceClassesWithDi
	 */
	public function testCacheIsIgnoredWhenCachingIsDisabled(): void
	{
		$this->setShouldCache(false);
		\file_put_contents($this->cacheFile, \json_encode(['services' => ['Missing\\Service' => []]]));

		$main = $this->makeMain();
		$main->instances();

		$this->assertSame(1, $main->scanCount);
	}

	private function makeMain(): CountingMain
	{
		return new CountingMain([], self::NAMESPACE_ROOT);
	}

	private function setShouldCache(?bool $value): void
	{
		$property = new \ReflectionProperty(Helpers::class, 'shouldCacheResult');
		$property->setValue(null, $value);
	}

	private function restorePaths(): void
	{
		(new \ReflectionProperty(Helpers::class, 'basePaths'))->setValue(null, $this->originalBasePaths);
		(new \ReflectionProperty(Helpers::class, 'pathConfigs'))->setValue(null, $this->originalPathConfigs);
	}

	private function cleanUp(): void
	{
		foreach ([$this->cacheFile ?? '', ...(\glob(Helpers::getEightshiftOutputPath(self::NAMESPACE_ROOT . '*')) ?: [])] as $file) {
			if ($file !== '' && \is_file($file)) {
				\unlink($file);
			}
		}
	}
}
