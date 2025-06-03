<?php

/**
 * Tests for InvalidManifest exception class.
 *
 * @package EightshiftLibs\Tests\Unit\Exception
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Exception;

use EightshiftLibs\Tests\BaseTestCase;
use EightshiftLibs\Exception\InvalidManifest;
use EightshiftLibs\Exception\GeneralExceptionInterface;
use InvalidArgumentException;
use Brain\Monkey\Functions;

/**
 * Test case for InvalidManifest exception.
 *
 * @coversDefaultClass EightshiftLibs\Exception\InvalidManifest
 */
class InvalidManifestTest extends BaseTestCase
{
	protected function setUp(): void
	{
		parent::setUp();

		// Mock WordPress functions
		Functions\when('esc_html__')->returnArg(1);
	}

	/**
	 * @covers ::missingManifestKeyException
	 */
	public function testMissingManifestKeyExceptionMessage(): void
	{
		$key = 'blockName';
		$path = '/blocks/button/manifest.json';
		$exception = InvalidManifest::missingManifestKeyException($key, $path);

		$this->assertInstanceOf(InvalidManifest::class, $exception);
		$this->assertInstanceOf(InvalidArgumentException::class, $exception);
		$this->assertInstanceOf(GeneralExceptionInterface::class, $exception);

		$expectedMessage = 'blockName key does not exist in manifest.json at /blocks/button/manifest.json. Please check if the provided key is correct.';
		$this->assertEquals($expectedMessage, $exception->getMessage());
	}

	/**
	 * @covers ::emptyOrErrorManifestException
	 */
	public function testEmptyOrErrorManifestExceptionMessage(): void
	{
		$path = '/components/card/manifest.json';
		$exception = InvalidManifest::emptyOrErrorManifestException($path);

		$this->assertInstanceOf(InvalidManifest::class, $exception);
		$this->assertInstanceOf(InvalidArgumentException::class, $exception);
		$this->assertInstanceOf(GeneralExceptionInterface::class, $exception);

		$expectedMessage = 'Manifest.json at /components/card/manifest.json is empty or has errors. Please check it and try again.';
		$this->assertEquals($expectedMessage, $exception->getMessage());
	}

	/**
	 * @covers ::missingManifestException
	 */
	public function testMissingManifestExceptionMessage(): void
	{
		$path = '/missing/path/manifest.json';
		$exception = InvalidManifest::missingManifestException($path);

		$this->assertInstanceOf(InvalidManifest::class, $exception);
		$this->assertInstanceOf(InvalidArgumentException::class, $exception);
		$this->assertInstanceOf(GeneralExceptionInterface::class, $exception);

		$expectedMessage = 'Manifest.json missing at /missing/path/manifest.json. Please verify it exists and try again.';
		$this->assertEquals($expectedMessage, $exception->getMessage());
	}

	/**
	 * @covers ::notAllowedManifestPathException
	 */
	public function testNotAllowedManifestPathExceptionMessage(): void
	{
		$path = '/unauthorized/directory/manifest.json';
		$exception = InvalidManifest::notAllowedManifestPathException($path);

		$this->assertInstanceOf(InvalidManifest::class, $exception);
		$this->assertInstanceOf(InvalidArgumentException::class, $exception);
		$this->assertInstanceOf(GeneralExceptionInterface::class, $exception);

		$expectedMessage = 'Trying to get manifest.json from outside of the Blocks folder. Please check your implementation. Path provided: /unauthorized/directory/manifest.json';
		$this->assertEquals($expectedMessage, $exception->getMessage());
	}

	/**
	 * @covers ::notAllowedManifestPathItemException
	 */
	public function testNotAllowedManifestPathItemExceptionMessage(): void
	{
		$path = '/invalid/folder/manifest.json';
		$exception = InvalidManifest::notAllowedManifestPathItemException($path);

		$this->assertInstanceOf(InvalidManifest::class, $exception);
		$this->assertInstanceOf(InvalidArgumentException::class, $exception);
		$this->assertInstanceOf(GeneralExceptionInterface::class, $exception);

		$expectedMessage = 'Trying to load manifest.json from outside of allowed folders. Manifest can only be loaded from: custom, components, variations, wrapper. Provided path: /invalid/folder/manifest.json';
		$this->assertEquals($expectedMessage, $exception->getMessage());
	}

	/**
	 * @covers ::missingCacheTopItemException
	 */
	public function testMissingCacheTopItemExceptionMessage(): void
	{
		$key = 'components';
		$cacheType = 'manifestCache';
		$exception = InvalidManifest::missingCacheTopItemException($key, $cacheType);

		$this->assertInstanceOf(InvalidManifest::class, $exception);
		$this->assertInstanceOf(InvalidArgumentException::class, $exception);
		$this->assertInstanceOf(GeneralExceptionInterface::class, $exception);

		$expectedMessage = 'Unable to get components from manifest data or cache. Please check if provided key is correct or cache type is correct. Cache type provided is: manifestCache.';
		$this->assertEquals($expectedMessage, $exception->getMessage());
	}

	/**
	 * @covers ::missingCacheSubItemException
	 */
	public function testMissingCacheSubItemExceptionMessage(): void
	{
		$key = 'attributes';
		$name = 'buttonText';
		$cacheType = 'blockCache';
		$exception = InvalidManifest::missingCacheSubItemException($key, $name, $cacheType);

		$this->assertInstanceOf(InvalidManifest::class, $exception);
		$this->assertInstanceOf(InvalidArgumentException::class, $exception);
		$this->assertInstanceOf(GeneralExceptionInterface::class, $exception);

		$expectedMessage = 'Unable to get attributes from manifest data or cache with subitem buttonText.
					Please check if provided key is correct or cache type is correct. Cache type provided is: blockCache.';
		$this->assertEquals($expectedMessage, $exception->getMessage());
	}

	/**
	 * @covers ::manifestStructureException
	 */
	public function testManifestStructureExceptionMessage(): void
	{
		$error = 'Syntax error, malformed JSON.';
		$exception = InvalidManifest::manifestStructureException($error);

		$this->assertInstanceOf(InvalidManifest::class, $exception);
		$this->assertInstanceOf(InvalidArgumentException::class, $exception);
		$this->assertInstanceOf(GeneralExceptionInterface::class, $exception);

		$expectedMessage = 'Syntax error, malformed JSON.';
		$this->assertEquals($expectedMessage, $exception->getMessage());
	}

	/**
	 * Test with empty values.
	 * @covers ::missingManifestKeyException
	 * @covers ::emptyOrErrorManifestException
	 * @covers ::missingManifestException
	 * @covers ::notAllowedManifestPathException
	 * @covers ::notAllowedManifestPathItemException
	 * @covers ::missingCacheTopItemException
	 * @covers ::missingCacheSubItemException
	 * @covers ::manifestStructureException
	 */
	public function testExceptionsWithEmptyValues(): void
	{
		$missingKey = InvalidManifest::missingManifestKeyException('', '');
		$emptyManifest = InvalidManifest::emptyOrErrorManifestException('');
		$missingManifest = InvalidManifest::missingManifestException('');
		$notAllowedPath = InvalidManifest::notAllowedManifestPathException('');
		$notAllowedPathItem = InvalidManifest::notAllowedManifestPathItemException('');
		$missingCache = InvalidManifest::missingCacheTopItemException('', '');
		$missingCacheSub = InvalidManifest::missingCacheSubItemException('', '', '');
		$structureError = InvalidManifest::manifestStructureException('');

		$this->assertInstanceOf(InvalidManifest::class, $missingKey);
		$this->assertInstanceOf(InvalidManifest::class, $emptyManifest);
		$this->assertInstanceOf(InvalidManifest::class, $missingManifest);
		$this->assertInstanceOf(InvalidManifest::class, $notAllowedPath);
		$this->assertInstanceOf(InvalidManifest::class, $notAllowedPathItem);
		$this->assertInstanceOf(InvalidManifest::class, $missingCache);
		$this->assertInstanceOf(InvalidManifest::class, $missingCacheSub);
		$this->assertInstanceOf(InvalidManifest::class, $structureError);
	}

	/**
	 * Test real-world manifest scenarios.
	 * @covers ::missingManifestKeyException
	 */
	public function testRealWorldManifestScenarios(): void
	{
		// Common manifest keys
		$commonKeys = ['blockName', 'title', 'description', 'category', 'icon', 'keywords', 'attributes'];
		$commonPaths = [
			'/src/Blocks/Button/manifest.json',
			'/src/Components/Card/manifest.json',
			'/src/Variations/Hero/manifest.json',
			'/wp-content/themes/mytheme/src/Blocks/manifest.json'
		];

		foreach ($commonKeys as $key) {
			foreach ($commonPaths as $path) {
				$exception = InvalidManifest::missingManifestKeyException($key, $path);
				$this->assertStringContainsString($key, $exception->getMessage());
				$this->assertStringContainsString($path, $exception->getMessage());
			}
		}
	}

	/**
	 * Test cache-related exceptions with different cache types.
	 * @covers ::missingCacheTopItemException
	 * @covers ::missingCacheSubItemException
	 */
	public function testCacheExceptionsWithDifferentCacheTypes(): void
	{
		$cacheTypes = ['memcache', 'redis', 'file', 'database', 'transient'];
		$keys = ['blocks', 'components', 'variations', 'settings'];

		foreach ($cacheTypes as $cacheType) {
			foreach ($keys as $key) {
				$topException = InvalidManifest::missingCacheTopItemException($key, $cacheType);
				$subException = InvalidManifest::missingCacheSubItemException($key, 'subItem', $cacheType);

				$this->assertStringContainsString($key, $topException->getMessage());
				$this->assertStringContainsString($cacheType, $topException->getMessage());
				$this->assertStringContainsString($key, $subException->getMessage());
				$this->assertStringContainsString('subItem', $subException->getMessage());
				$this->assertStringContainsString($cacheType, $subException->getMessage());
			}
		}
	}

	/**
	 * Test allowed folders message content.
	 * @covers ::notAllowedManifestPathItemException
	 */
	public function testNotAllowedPathItemExceptionContainsAllowedFolders(): void
	{
		$exception = InvalidManifest::notAllowedManifestPathItemException('/wrong/path');

		$message = $exception->getMessage();
		$this->assertStringContainsString('custom', $message);
		$this->assertStringContainsString('components', $message);
		$this->assertStringContainsString('variations', $message);
		$this->assertStringContainsString('wrapper', $message);
	}

	/**
	 * Test that exception inherits from proper parent classes.
	 * @covers ::missingManifestException
	 */
	public function testExceptionInheritance(): void
	{
		$exception = InvalidManifest::missingManifestException('/test/path');

		$this->assertInstanceOf(InvalidArgumentException::class, $exception);
		$this->assertInstanceOf(GeneralExceptionInterface::class, $exception);

		// Verify it's throwable
		$this->expectException(InvalidManifest::class);
		throw $exception;
	}

	/**
	 * Test different JSON structure errors.
	 * @covers ::manifestStructureException
	 */
	public function testManifestStructureExceptionWithVariousErrors(): void
	{
		$jsonErrors = [
			'Syntax error, malformed JSON.',
			'Control character error, possibly incorrectly encoded.',
			'Maximum stack depth exceeded.',
			'Malformed UTF-8 characters, possibly incorrectly encoded.',
			'State mismatch (invalid or malformed JSON).',
			'Unexpected character in JSON at position 42'
		];

		foreach ($jsonErrors as $error) {
			$exception = InvalidManifest::manifestStructureException($error);
			$this->assertEquals($error, $exception->getMessage());
		}
	}
}
