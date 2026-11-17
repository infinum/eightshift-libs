<?php

/**
 * Request-local shared font verification and registration tests.
 *
 * @package EightshiftLibs\Tests\Unit\Enqueue
 */

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Enqueue;

use Brain\Monkey\Functions;
use EightshiftLibs\Enqueue\SharedFonts;
use EightshiftLibs\Tests\BaseTestCase;

/** Count real hash operations in the independently prefixed copy. */
function recordFontHash(string $algorithm, string $filename): string|false
{
	$GLOBALS['shared_fonts_test_hashes']++;
	return \hash_file($algorithm, $filename);
}

/** Simulate Windows creation-time ctime when a font's contents change. */
function simulatedWindowsFontStat(string $filename): array|false
{
	$state = \Patchwork\CodeManipulation\Stream::bypass(static function () use ($filename): array|false {
		\clearstatcache(true, $filename);
		return \stat($filename);
	});
	if ($state !== false) {
		$state['ctime'] = $GLOBALS['shared_fonts_test_windows_ctimes'][$filename] ??= $state['ctime'];
	}
	return $state;
}

final class SharedFontsTest extends BaseTestCase
{
	private const PREFIXED_CLASS = 'EightshiftLibs\\Tests\\Unit\\Enqueue\\Prefixed\\SharedFonts';

	private string $fixtureDirectory;

	private string $activeRegistry = 'main';

	private array $styles = [];

	private array $enqueued = [];

	protected function setUp(): void
	{
		parent::setUp();

		$this->fixtureDirectory = __DIR__ . '/.shared-fonts-' . \bin2hex(\random_bytes(8));
		\mkdir($this->fixtureDirectory, 0777, true);
		$this->activeRegistry = 'main';
		$this->styles = [];
		$this->enqueued = [];
		unset($GLOBALS['eightshift_shared_fonts_v1'], $GLOBALS['eightshift_shared_fonts_verified_v1'], $GLOBALS['eightshift_shared_fonts_manifests_v1']);
		$GLOBALS['shared_fonts_test_hashes'] = 0;
		$GLOBALS['shared_fonts_test_windows_ctimes'] = [];

		if (!\class_exists(self::PREFIXED_CLASS, false)) {
			$source = \file_get_contents(__DIR__ . '/../../../src/Enqueue/SharedFonts.php');
			self::assertIsString($source);
			$source = \str_replace(
				['namespace EightshiftLibs\\Enqueue;', '\\hash_file('],
				['namespace EightshiftLibs\\Tests\\Unit\\Enqueue\\Prefixed;', '\\EightshiftLibs\\Tests\\Unit\\Enqueue\\recordFontHash('],
				$source,
				$count,
			);
			self::assertSame(2, $count);
			$copy = $this->fixtureDirectory . '/PrefixedSharedFonts.php';
			\file_put_contents($copy, $source);
			require $copy;
		}

		Functions\when('wp_using_ext_object_cache')->justReturn(false);
		Functions\when('home_url')->justReturn('https://example.test/');
		Functions\when('wp_parse_url')->alias('parse_url');
		Functions\when('wp_style_is')->alias(function (string $handle, string $status): bool {
			return $status === 'registered' && isset($this->styles[$this->activeRegistry][$handle]);
		});
		Functions\when('wp_register_style')->alias(function (string $handle, mixed $source, array $dependencies, mixed $version): bool {
			$this->styles[$this->activeRegistry][$handle] = ['source' => $source, 'css' => []];
			return true;
		});
		Functions\when('wp_add_inline_style')->alias(function (string $handle, string $css): bool {
			$this->styles[$this->activeRegistry][$handle]['css'][] = $css;
			return true;
		});
		Functions\when('wp_enqueue_style')->alias(function (string $handle): void {
			$this->enqueued[$this->activeRegistry][] = $handle;
		});
	}

	protected function tearDown(): void
	{
		unset($GLOBALS['eightshift_shared_fonts_v1'], $GLOBALS['eightshift_shared_fonts_verified_v1'], $GLOBALS['shared_fonts_test_hashes'], $GLOBALS['shared_fonts_test_windows_ctimes']);
		$this->removeFixtureDirectory($this->fixtureDirectory);
		parent::tearDown();
	}

	public function testPersistentCacheAvoidsHashingAcrossRequests(): void
	{
		if (\PHP_OS_FAMILY === 'Windows') {
			self::markTestSkipped('Persistent stat caching is disabled on Windows.');
		}
		$cache = [];
		Functions\when('wp_using_ext_object_cache')->justReturn(true);
		Functions\when('wp_cache_get')->alias(static function (string $key, string $group) use (&$cache): mixed {
			return $cache[$group][$key] ?? false;
		});
		Functions\when('wp_cache_set')->alias(static function (string $key, mixed $value, string $group, int $ttl) use (&$cache): bool {
			$cache[$group][$key] = $value;
			return true;
		});
		[$directory] = $this->createProvider('persistent');
		\sleep(2);
		self::enqueuePrefixed($directory, 'https://example.test/persistent/public/');
		self::assertSame(1, $GLOBALS['shared_fonts_test_hashes']);
		unset($GLOBALS['eightshift_shared_fonts_verified_v1']);
		self::enqueuePrefixed($directory, 'https://example.test/persistent/public/');
		self::assertSame(1, $GLOBALS['shared_fonts_test_hashes']);
		self::assertNotEmpty($cache);
	}

	public function testRepeatedFontsRegisterEachFaceOnce(): void
	{
		[$directory, $hash, $face] = $this->createProvider('first');
		$this->writeManifest($directory, $hash, $face, 2);
		\sleep(2);

		self::enqueuePrefixed($directory, 'https://example.test/first/public/');
		self::enqueuePrefixed($directory, 'https://example.test/first/public/');

		self::assertSame(\PHP_OS_FAMILY === 'Windows' ? 4 : 1, $GLOBALS['shared_fonts_test_hashes']);
		self::assertCount(1, $this->styles['main']);
		self::assertCount(2, $this->enqueued['main']);
		self::assertSame($this->enqueued['main'][0], $this->enqueued['main'][1]);
		$style = \reset($this->styles['main']);
		self::assertFalse($style['source']);
		self::assertCount(1, $style['css']);
		self::assertStringContainsString('https://example.test/first/public/fonts/' . $hash . '.woff2', $style['css'][0]);
	}

	public function testPrefixedCopiesRegisterInEveryStyleRegistry(): void
	{
		[$directory, $hash] = $this->createProvider('first');
		\sleep(2);

		SharedFonts::enqueue($directory, 'https://example.test/first/public/');
		self::enqueuePrefixed($directory, 'https://example.test/other-prefix/');
		$this->activeRegistry = 'editor';
		self::enqueuePrefixed($directory, 'https://example.test/other-prefix/');

		self::assertSame(\PHP_OS_FAMILY === 'Windows' ? 2 : 0, $GLOBALS['shared_fonts_test_hashes']);
		self::assertCount(1, $this->styles['main']);
		self::assertCount(1, $this->styles['editor']);
		self::assertCount(2, $this->enqueued['main']);
		self::assertCount(1, $this->enqueued['editor']);
		self::assertSame($this->enqueued['main'][0], $this->enqueued['editor'][0]);
		$main = \reset($this->styles['main']);
		$editor = \reset($this->styles['editor']);
		self::assertCount(1, $main['css']);
		self::assertSame($main['css'], $editor['css']);
		self::assertStringContainsString('https://example.test/first/public/fonts/' . $hash . '.woff2', $editor['css'][0]);
	}

	public function testDifferentProvidersValidateTheirOwnFilesAndManifests(): void
	{
		[$first, $hash, $face] = $this->createProvider('first');
		[$second] = $this->createProvider('second');
		$otherFace = $this->face($hash, '700');
		$this->writeManifest($second, $hash, $face . $otherFace);
		\sleep(2);

		self::enqueuePrefixed($first, 'https://example.test/first/public/');
		self::enqueuePrefixed($second, 'https://example.test/second/public/');

		self::assertSame(2, $GLOBALS['shared_fonts_test_hashes']);
		self::assertCount(2, $this->styles['main']);
		self::assertCount(3, $this->enqueued['main']);
		foreach ($this->styles['main'] as $style) {
			self::assertStringContainsString('https://example.test/first/public/fonts/' . $hash . '.woff2', $style['css'][0]);
		}

		$file = $second . '/fonts/' . $hash . '.woff2';
		\file_put_contents($file, 'invalid-font-content');
		self::enqueuePrefixed($second, 'https://example.test/second/public/');
		self::assertSame(3, $GLOBALS['shared_fonts_test_hashes']);
		self::assertCount(3, $this->enqueued['main']);

		\file_put_contents($file, 'example-font-content');
		self::enqueuePrefixed($second, 'https://example.test/second/public/');
		self::assertSame(4, $GLOBALS['shared_fonts_test_hashes']);
		self::assertCount(5, $this->enqueued['main']);
	}

	public function testRecentlyChangedFileIsRehashedEvenWithTheSameSizeAndMtime(): void
	{
		[$directory, $hash] = $this->createProvider('first');
		$file = $directory . '/fonts/' . $hash . '.woff2';
		$mtime = \Patchwork\CodeManipulation\Stream::bypass(static function () use ($file): int|false {
			\clearstatcache(true, $file);
			return \filemtime($file);
		});
		self::assertIsInt($mtime);

		self::enqueuePrefixed($directory, 'https://example.test/first/public/');
		\file_put_contents($file, 'invalid-font-content');
		\touch($file, $mtime);
		$updatedMtime = \Patchwork\CodeManipulation\Stream::bypass(static function () use ($file): int|false {
			\clearstatcache(true, $file);
			return \filemtime($file);
		});
		self::assertSame($mtime, $updatedMtime);
		self::enqueuePrefixed($directory, 'https://example.test/first/public/');

		self::assertSame(2, $GLOBALS['shared_fonts_test_hashes']);
		self::assertCount(1, $this->enqueued['main']);
	}

	public function testSimulatedWindowsRehashesWhenFileMetadataDoesNotChange(): void
	{
		$class = 'EightshiftLibs\\Tests\\Unit\\Enqueue\\Windows\\SharedFonts';
		if (!\class_exists($class, false)) {
			$source = \file_get_contents(__DIR__ . '/../../../src/Enqueue/SharedFonts.php');
			self::assertIsString($source);
			$source = \str_replace(
				['namespace EightshiftLibs\\Enqueue;', '\\hash_file(', '\\stat('],
				['namespace EightshiftLibs\\Tests\\Unit\\Enqueue\\Windows;', '\\EightshiftLibs\\Tests\\Unit\\Enqueue\\recordFontHash(', '\\EightshiftLibs\\Tests\\Unit\\Enqueue\\simulatedWindowsFontStat('],
				$source,
				$count,
			);
			self::assertSame(3, $count);
			$source = \str_replace('\\PHP_OS_FAMILY', "'Windows'", $source);
			$copy = $this->fixtureDirectory . '/WindowsSharedFonts.php';
			\file_put_contents($copy, $source);
			require $copy;
		}

		[$directory, $hash] = $this->createProvider('windows');
		$file = \realpath($directory . '/fonts/' . $hash . '.woff2');
		self::assertIsString($file);
		$mtime = \Patchwork\CodeManipulation\Stream::bypass(static function () use ($file): int|false {
			\clearstatcache(true, $file);
			return \filemtime($file);
		});
		self::assertIsInt($mtime);
		\sleep(2);
		$originalState = simulatedWindowsFontStat($file);
		self::assertIsArray($originalState);

		$class::enqueue($directory, 'https://example.test/windows/public/');
		$class::enqueue($directory, 'https://example.test/windows/public/');
		\file_put_contents($file, 'invalid-font-content');
		\touch($file, $mtime);
		\clearstatcache(true, $file);
		$rewrittenState = simulatedWindowsFontStat($file);
		self::assertIsArray($rewrittenState);
		foreach (['dev', 'ino', 'mode', 'uid', 'gid', 'size', 'mtime', 'ctime'] as $key) {
			self::assertSame($originalState[$key], $rewrittenState[$key], $key);
		}
		$class::enqueue($directory, 'https://example.test/windows/public/');

		self::assertSame(3, $GLOBALS['shared_fonts_test_hashes']);
		self::assertCount(2, $this->enqueued['main']);
		self::assertCount(1, $this->styles['main']);
	}

	public function testCachedFontsStillRequireValidOriginAndManifest(): void
	{
		[$directory, $hash, $face] = $this->createProvider('first');
		\sleep(2);

		self::enqueuePrefixed($directory, 'https://example.test/first/public/');
		self::enqueuePrefixed($directory, 'https://other.test/first/public/');
		self::enqueuePrefixed($directory, 'https://example.test/first/public/?query=1');
		$this->writeManifest($directory, $hash, '@font-face{src:url("https://example.test/other.woff2")}');
		self::enqueuePrefixed($directory, 'https://example.test/first/public/');
		self::assertCount(1, $this->enqueued['main']);
		self::assertSame(\PHP_OS_FAMILY === 'Windows' ? 2 : 1, $GLOBALS['shared_fonts_test_hashes']);

		$this->writeManifest($directory, $hash, $face . $this->face($hash, '700'));
		self::enqueuePrefixed($directory, 'https://example.test/first/public/');
		self::assertCount(2, $this->styles['main']);
		self::assertCount(3, $this->enqueued['main']);
		self::assertSame(\PHP_OS_FAMILY === 'Windows' ? 3 : 1, $GLOBALS['shared_fonts_test_hashes']);
	}

	public function testCachedFontCannotBeRetargetedOutsideThePublicDirectory(): void
	{
		[$directory, $hash] = $this->createProvider('first');
		$outside = $this->fixtureDirectory . '/outside.woff2';
		\file_put_contents($outside, 'example-font-content');
		\sleep(2);

		self::enqueuePrefixed($directory, 'https://example.test/first/public/');
		$file = $directory . '/fonts/' . $hash . '.woff2';
		\unlink($file);
		if (!@\symlink($outside, $file)) {
			self::markTestSkipped('Symlinks are not available.');
		}
		self::enqueuePrefixed($directory, 'https://example.test/first/public/');

		self::assertSame(1, $GLOBALS['shared_fonts_test_hashes']);
		self::assertCount(1, $this->enqueued['main']);
	}

	private static function enqueuePrefixed(string $directory, string $url): void
	{
		$class = self::PREFIXED_CLASS;
		$class::enqueue($directory, $url);
	}

	private function createProvider(string $name): array
	{
		$directory = $this->fixtureDirectory . '/' . $name;
		\mkdir($directory . '/fonts', 0777, true);
		$contents = 'example-font-content';
		$hash = \hash('sha256', $contents);
		\file_put_contents($directory . '/fonts/' . $hash . '.woff2', $contents);
		$face = $this->face($hash, '400');
		$this->writeManifest($directory, $hash, $face);
		return [$directory, $hash, $face];
	}

	private function face(string $hash, string $weight): string
	{
		return '@font-face{font-family:Example;src:url("./fonts/' . $hash . '.woff2") format("woff2");font-weight:' . $weight . ';}';
	}

	private function writeManifest(string $directory, string $hash, string $css, int $fontCount = 1): void
	{
		$data = [
			'version' => 1,
			'fonts' => \array_fill(0, $fontCount, ['hash' => $hash, 'file' => 'fonts/' . $hash . '.woff2']),
			'css' => $css,
		];
		\file_put_contents($directory . '/shared-fonts.json', \json_encode($data, \JSON_THROW_ON_ERROR));
	}

	private function removeFixtureDirectory(string $directory): void
	{
		foreach (\scandir($directory) as $name) {
			if ($name === '.' || $name === '..') {
				continue;
			}
			$path = $directory . '/' . $name;
			if (\is_dir($path) && !\is_link($path)) {
				$this->removeFixtureDirectory($path);
			} else {
				\unlink($path);
			}
		}
		\rmdir($directory);
	}
}
