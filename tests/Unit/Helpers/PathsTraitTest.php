<?php

/**
 * Comprehensive tests for PathsTrait helper methods.
 *
 * @package EightshiftLibs\Tests\Unit\Helpers
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Helpers;

use EightshiftLibs\Tests\BaseTestCase;
use EightshiftLibs\Helpers\PathsTrait;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Wrapper class to test PathsTrait methods without conflicts.
 */
class PathsTraitWrapper
{
	use PathsTrait;
}

/**
 * Comprehensive test case for PathsTrait utility methods.
 *
 * @coversDefaultClass EightshiftLibs\Helpers\PathsTrait
 */
class PathsTraitTest extends BaseTestCase
{
	private PathsTraitWrapper $wrapper;

	protected function setUp(): void
	{
		parent::setUp();
		$this->wrapper = new PathsTraitWrapper();

		// Reset static caches between tests
		$reflection = new \ReflectionClass(PathsTraitWrapper::class);
		$basePathsProperty = $reflection->getProperty('basePaths');
		$basePathsProperty->setAccessible(true);
		$basePathsProperty->setValue(null, null);

		$pathConfigsProperty = $reflection->getProperty('pathConfigs');
		$pathConfigsProperty->setAccessible(true);
		$pathConfigsProperty->setValue(null, null);
	}

	/**
	 * @covers ::initializePathCaches
	 */
	public function testInitializePathCaches(): void
	{
		// Call the method
		$this->wrapper::initializePathCaches();

		// Use reflection to check if caches are initialized
		$reflection = new \ReflectionClass(PathsTraitWrapper::class);

		$basePathsProperty = $reflection->getProperty('basePaths');
		$basePathsProperty->setAccessible(true);
		$basePaths = $basePathsProperty->getValue();

		$pathConfigsProperty = $reflection->getProperty('pathConfigs');
		$pathConfigsProperty->setAccessible(true);
		$pathConfigs = $pathConfigsProperty->getValue();

		$this->assertIsArray($basePaths);
		$this->assertIsArray($pathConfigs);

		// Check required base paths exist
		$this->assertArrayHasKey('root', $basePaths);
		$this->assertArrayHasKey('projectRoot', $basePaths);
		$this->assertArrayHasKey('src', $basePaths);
		$this->assertArrayHasKey('public', $basePaths);
		$this->assertArrayHasKey('blocksRoot', $basePaths);

		// Check required path configs exist
		$this->assertArrayHasKey('root', $pathConfigs);
		$this->assertArrayHasKey('eightshift', $pathConfigs);
		$this->assertArrayHasKey('src', $pathConfigs);
		$this->assertArrayHasKey('blocks', $pathConfigs);
		$this->assertArrayHasKey('components', $pathConfigs);
	}

	/**
	 * @covers ::initializePathCaches
	 */
	public function testInitializePathCachesOnlyOnce(): void
	{
		// Call twice and ensure it doesn't reinitialize
		$this->wrapper::initializePathCaches();

		$reflection = new \ReflectionClass(PathsTraitWrapper::class);
		$basePathsProperty = $reflection->getProperty('basePaths');
		$basePathsProperty->setAccessible(true);
		$firstCall = $basePathsProperty->getValue();

		$this->wrapper::initializePathCaches();
		$secondCall = $basePathsProperty->getValue();

		$this->assertSame($firstCall, $secondCall);
	}

	/**
	 * @covers ::getProjectPaths
	 */
	public function testGetProjectPathsWithEmptyType(): void
	{
		$result = $this->wrapper::getProjectPaths();

		$this->assertIsString($result);
		$this->assertStringStartsWith(DIRECTORY_SEPARATOR, $result);
		$this->assertStringEndsWith(DIRECTORY_SEPARATOR, $result);
	}

	/**
	 * @covers ::getProjectPaths
	 */
	#[DataProvider('validPathTypesProvider')]
	public function testGetProjectPathsWithValidTypes(string $type): void
	{
		$result = $this->wrapper::getProjectPaths($type);

		$this->assertIsString($result);
		$this->assertStringStartsWith(DIRECTORY_SEPARATOR, $result);
		$this->assertStringEndsWith(DIRECTORY_SEPARATOR, $result);
	}

	/**
	 * @covers ::getProjectPaths
	 */
	public function testGetProjectPathsWithUnknownType(): void
	{
		$result = $this->wrapper::getProjectPaths('unknown-type');

		$this->assertIsString($result);
		$this->assertStringStartsWith(DIRECTORY_SEPARATOR, $result);
		$this->assertStringEndsWith(DIRECTORY_SEPARATOR, $result);
	}

	/**
	 * @covers ::getProjectPaths
	 */
	#[DataProvider('suffixWithExtensionProvider')]
	public function testGetProjectPathsWithFileExtensionSuffix(string $suffix): void
	{
		$result = $this->wrapper::getProjectPaths('src', $suffix);

		$this->assertIsString($result);
		$this->assertStringStartsWith(DIRECTORY_SEPARATOR, $result);
		// Should NOT end with slash when file has extension
		$this->assertStringEndsWith($suffix, $result);
		$this->assertThat($result, $this->logicalNot($this->stringEndsWith(DIRECTORY_SEPARATOR)));
	}

	/**
	 * @covers ::getProjectPaths
	 */
	#[DataProvider('suffixWithoutExtensionProvider')]
	public function testGetProjectPathsWithDirectorySuffix(string $suffix): void
	{
		$result = $this->wrapper::getProjectPaths('src', $suffix);

		$this->assertIsString($result);
		$this->assertStringStartsWith(DIRECTORY_SEPARATOR, $result);
		$this->assertStringEndsWith(DIRECTORY_SEPARATOR, $result);
	}

	/**
	 * @covers ::getProjectPaths
	 */
	public function testGetProjectPathsWithArraySuffix(): void
	{
		$suffix = ['subfolder', 'file.php'];
		$result = $this->wrapper::getProjectPaths('src', $suffix);

		$this->assertIsString($result);
		$this->assertStringContainsString('subfolder', $result);
		$this->assertStringEndsWith('file.php', $result);
		$this->assertThat($result, $this->logicalNot($this->stringEndsWith(DIRECTORY_SEPARATOR)));
	}

	/**
	 * @covers ::getProjectPaths
	 */
	public function testGetProjectPathsWithEmptySuffix(): void
	{
		$result = $this->wrapper::getProjectPaths('src', '');

		$this->assertIsString($result);
		$this->assertStringStartsWith(DIRECTORY_SEPARATOR, $result);
		$this->assertStringEndsWith(DIRECTORY_SEPARATOR, $result);
	}

	/**
	 * @covers ::joinPaths
	 */
	public function testJoinPathsWithEmptyArray(): void
	{
		$result = $this->wrapper::joinPaths([]);

		$this->assertEquals(DIRECTORY_SEPARATOR, $result);
	}

	/**
	 * @covers ::joinPaths
	 */
	public function testJoinPathsWithSinglePath(): void
	{
		$result = $this->wrapper::joinPaths(['folder']);

		$this->assertEquals(DIRECTORY_SEPARATOR . 'folder' . DIRECTORY_SEPARATOR, $result);
	}

	/**
	 * @covers ::joinPaths
	 */
	public function testJoinPathsWithMultiplePaths(): void
	{
		$result = $this->wrapper::joinPaths(['root', 'src', 'components']);

		$expected = DIRECTORY_SEPARATOR . 'root' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR;
		$this->assertEquals($expected, $result);
	}

	/**
	 * @covers ::joinPaths
	 */
	public function testJoinPathsWithFileExtension(): void
	{
		$result = $this->wrapper::joinPaths(['root', 'src', 'file.php']);

		$expected = DIRECTORY_SEPARATOR . 'root' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'file.php';
		$this->assertEquals($expected, $result);
		$this->assertThat($result, $this->logicalNot($this->stringEndsWith(DIRECTORY_SEPARATOR)));
	}

	/**
	 * @covers ::joinPaths
	 */
	#[DataProvider('pathsWithMultipleDotsProvider')]
	public function testJoinPathsWithMultipleDots(array $paths, string $expected): void
	{
		$result = $this->wrapper::joinPaths($paths);

		$this->assertEquals($expected, $result);
	}

	/**
	 * @covers ::joinPaths
	 */
	public function testJoinPathsWithEmptyPathElements(): void
	{
		$result = $this->wrapper::joinPaths(['', 'src', '', 'components', '']);

		$expected = DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR;
		$this->assertEquals($expected, $result);
	}

	/**
	 * @covers ::joinPaths
	 */
	public function testJoinPathsWithLeadingAndTrailingSlashes(): void
	{
		$result = $this->wrapper::joinPaths(['/root/', '/src/', '/file.php']);

		$expected = DIRECTORY_SEPARATOR . 'root' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'file.php';
		$this->assertEquals($expected, $result);
	}

	/**
	 * @covers ::joinPaths
	 */
	public function testJoinPathsWithOnlyEmptyStrings(): void
	{
		$result = $this->wrapper::joinPaths(['', '', '']);

		$this->assertEquals(DIRECTORY_SEPARATOR, $result);
	}

	/**
	 * @covers ::getEightshiftOutputPath
	 */
	public function testGetEightshiftOutputPathWithoutFileName(): void
	{
		$result = $this->wrapper::getEightshiftOutputPath();

		$this->assertIsString($result);
		$this->assertStringStartsWith(DIRECTORY_SEPARATOR, $result);
		$this->assertStringEndsWith(DIRECTORY_SEPARATOR, $result);
		$this->assertStringContainsString('eightshift', $result);
	}

	/**
	 * @covers ::getEightshiftOutputPath
	 */
	public function testGetEightshiftOutputPathWithFileName(): void
	{
		$fileName = 'test-file.json';
		$result = $this->wrapper::getEightshiftOutputPath($fileName);

		$this->assertIsString($result);
		$this->assertStringEndsWith($fileName, $result);
		$this->assertStringContainsString('eightshift', $result);
	}

	/**
	 * @covers ::getEightshiftOutputPath
	 */
	public function testGetEightshiftOutputPathCaching(): void
	{
		$first = $this->wrapper::getEightshiftOutputPath('file1.json');
		$second = $this->wrapper::getEightshiftOutputPath('file2.json');

		// Both should use same base path
		$basePath1 = str_replace('file1.json', '', $first);
		$basePath2 = str_replace('file2.json', '', $second);

		$this->assertEquals($basePath1, $basePath2);
	}

	/**
	 * @covers ::joinPaths
	 */
	public function testJoinPathsImprovedFileDetection(): void
	{
		// Test the pathinfo() based file detection behavior

		// These should be treated as files (no trailing slash)
		$fileTests = [
			[['src', 'file.php'], '/src/file.php'],
			[['src', 'config.json'], '/src/config.json'],
			[['src', 'script.min.js'], '/src/script.min.js'],
			[['src', 'backup.tar.gz'], '/src/backup.tar.gz'],
			[['src', 'document.pdf'], '/src/document.pdf'],
		];

		foreach ($fileTests as [$input, $expected]) {
			$result = $this->wrapper::joinPaths($input);
			$this->assertEquals($expected, $result, "File test failed for " . end($input));
			$this->assertThat($result, $this->logicalNot($this->stringEndsWith('/')));
		}

		// These are also treated as files by pathinfo() because they contain dots
		$folderWithDotsTests = [
			[['src', 'v1.2.3'], '/src/v1.2.3'],        // extension: "3"
			[['src', 'folder.with.dots'], '/src/folder.with.dots'], // extension: "dots"
			[['src', 'app.name'], '/src/app.name'],    // extension: "name"
			[['src', 'config.dev'], '/src/config.dev'], // extension: "dev"
			[['src', '.hidden'], '/src/.hidden'],      // extension: "hidden"
		];

		foreach ($folderWithDotsTests as [$input, $expected]) {
			$result = $this->wrapper::joinPaths($input);
			$this->assertEquals($expected, $result, "Folder with dots test failed for " . end($input));
			$this->assertThat($result, $this->logicalNot($this->stringEndsWith('/')));
		}

		// These should be treated as folders (with trailing slash) - no extension detected
		$realFolderTests = [
			[['src', '...'], '/src/.../'],              // no extension
			[['src', 'folder'], '/src/folder/'],        // no dots
			[['src', 'components'], '/src/components/'], // no dots
		];

		foreach ($realFolderTests as [$input, $expected]) {
			$result = $this->wrapper::joinPaths($input);
			$this->assertEquals($expected, $result, "Real folder test failed for " . end($input));
			$this->assertStringEndsWith('/', $result);
		}
	}

	/**
	 * @covers ::joinPaths
	 */
	public function testJoinPathsEdgeCasesWithPathinfoDetection(): void
	{
		// Test edge cases with pathinfo() detection

		// Files without extensions - should get trailing slash
		$this->assertEquals('/src/Makefile/', $this->wrapper::joinPaths(['src', 'Makefile']));
		$this->assertEquals('/src/README/', $this->wrapper::joinPaths(['src', 'README']));
		$this->assertEquals('/src/LICENSE/', $this->wrapper::joinPaths(['src', 'LICENSE']));

		// Version folders are treated as files by pathinfo() because of the dots
		$this->assertEquals('/packages/v1.2.3', $this->wrapper::joinPaths(['packages', 'v1.2.3']));
		$this->assertEquals('/releases/2.1.4', $this->wrapper::joinPaths(['releases', '2.1.4']));

		// App/module names with dots are treated as files
		$this->assertEquals('/apps/my.app', $this->wrapper::joinPaths(['apps', 'my.app']));
		$this->assertEquals('/modules/auth.service', $this->wrapper::joinPaths(['modules', 'auth.service']));

		// Complex nested paths
		$this->assertEquals(
			'/v1.2/modules/auth.service/config.json',
			$this->wrapper::joinPaths(['v1.2', 'modules', 'auth.service', 'config.json'])
		);
	}

	/**
	 * @covers ::getProjectPaths
	 */
	public function testGetProjectPathsWithImprovedBehavior(): void
	{
		// Test that getProjectPaths behavior with pathinfo() implementation

		// Version folders are treated as files (no trailing slash) due to pathinfo()
		$result = $this->wrapper::getProjectPaths('src', 'v1.2.3');
		$this->assertStringEndsWith('v1.2.3', $result);
		$this->assertThat($result, $this->logicalNot($this->stringEndsWith('/')));

		// App folders with dots are treated as files
		$result = $this->wrapper::getProjectPaths('src', 'my.app');
		$this->assertStringEndsWith('my.app', $result);
		$this->assertThat($result, $this->logicalNot($this->stringEndsWith('/')));

		// Actual files should not end with slash
		$result = $this->wrapper::getProjectPaths('src', 'config.json');
		$this->assertStringEndsWith('config.json', $result);
		$this->assertThat($result, $this->logicalNot($this->stringEndsWith('/')));

		// Regular folders without dots should end with slash
		$result = $this->wrapper::getProjectPaths('src', 'components');
		$this->assertStringEndsWith('components/', $result);
	}

	/**
	 * Data provider for valid path types.
	 */
	public static function validPathTypesProvider(): array
	{
		return [
			['root'],
			['eightshift'],
			['eightshiftRoot'],
			['src'],
			['public'],
			['libsPrefixed'],
			['libsPrefixedGeolocation'],
			['blocksRoot'],
			['blocks'],
			['components'],
			['variations'],
			['wrapper'],
		];
	}

	/**
	 * Data provider for suffixes with file extensions.
	 */
	public static function suffixWithExtensionProvider(): array
	{
		return [
			['file.php'],
			['style.css'],
			['script.js'],
			['data.json'],
			['image.png'],
			['document.pdf'],
			['config.yml'],
			['test.spec.js'], // Multiple dots
			['file.backup.php'], // Multiple dots
			['my.custom.extension'], // Multiple dots
		];
	}

	/**
	 * Data provider for suffixes without file extensions.
	 */
	public static function suffixWithoutExtensionProvider(): array
	{
		return [
			['folder'],
			['subfolder'],
			['components'],
			['custom-folder'],
			['folder_name'],
			['123folder'],
		];
	}

	/**
	 * Data provider for paths with multiple dots to test edge cases.
	 */
	public static function pathsWithMultipleDotsProvider(): array
	{
		$sep = DIRECTORY_SEPARATOR;

		return [
			// File with multiple dots should not end with slash
			[['root', 'file.test.php'], $sep . 'root' . $sep . 'file.test.php'],
			[['src', 'config.local.json'], $sep . 'src' . $sep . 'config.local.json'],
			[['components', 'script.min.js'], $sep . 'components' . $sep . 'script.min.js'],

			// pathinfo() still treats these as files because they have "extensions"
			// v1.2.3 has extension "3", folder.with.dots has extension "dots"
			[['root', 'folder.with.dots'], $sep . 'root' . $sep . 'folder.with.dots'],
			[['src', 'v1.2.3'], $sep . 'src' . $sep . 'v1.2.3'],

			// Mix of folders and files with dots
			[['root', 'v1.2', 'file.min.js'], $sep . 'root' . $sep . 'v1.2' . $sep . 'file.min.js'],

			// Edge case: only dots (no extension detected, gets slash)
			[['...'], $sep . '...' . $sep],
			[['..', 'file.txt'], $sep . '..' . $sep . 'file.txt'],

			// Hidden files - pathinfo treats these as having extensions
			[['root', '.env'], $sep . 'root' . $sep . '.env'],
			[['root', '.hidden.file'], $sep . 'root' . $sep . '.hidden.file'],

			// Complex cases
			[['app.v2', 'src', 'utils.helper.php'], $sep . 'app.v2' . $sep . 'src' . $sep . 'utils.helper.php'],
		];
	}
}
