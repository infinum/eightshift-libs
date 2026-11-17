<?php

declare(strict_types=1);

namespace EightshiftLibs\Tests\Unit\Enqueue;

use Brain\Monkey\Functions;
use EightshiftLibs\Enqueue\AbstractAssets;
use EightshiftLibs\Helpers\Helpers;
use EightshiftLibs\Tests\BaseTestCase;
use ReflectionProperty;
use RuntimeException;

final class AssetMetadataProbe extends AbstractAssets
{
	/** @var array<string, string> */
	public array $assets = [];

	public function register(): void {}

	public function getAssetsPrefix(): string
	{
		return 'test';
	}

	public function getAssetsVersion(): string
	{
		return '1';
	}

	public function setAssetsItem(string $key): string
	{
		return $this->assets[$key] ?? throw new RuntimeException('Missing asset');
	}

	/** @return array<string, mixed>|null */
	public function metadata(string $key): ?array
	{
		return $this->readAssetMetadata($key);
	}

	/**
	 * @param array<int, string> $dependencies
	 * @return array<int, string>
	 */
	public function dependencies(string $key, array $dependencies): array
	{
		return $this->mergeAssetDependencies($key, $dependencies);
	}
}

final class AssetMetadataTest extends BaseTestCase
{
	private string $projectRoot;
	private mixed $originalBasePaths;
	private mixed $originalPathConfigs;
	/** @var list<string> */
	private array $files = [];
	/** @var list<string> */
	private array $directories = [];
	private AssetMetadataProbe $assets;

	protected function setUp(): void
	{
		parent::setUp();

		$this->projectRoot = sys_get_temp_dir() . '/eightshift-metadata-' . bin2hex(\random_bytes(8));
		mkdir($this->projectRoot);
		mkdir($this->projectRoot . '/public');
		$this->directories = [$this->projectRoot, $this->projectRoot . '/public'];

		$basePaths = new ReflectionProperty(Helpers::class, 'basePaths');
		$pathConfigs = new ReflectionProperty(Helpers::class, 'pathConfigs');
		$this->originalBasePaths = $basePaths->getValue();
		$this->originalPathConfigs = $pathConfigs->getValue();
		$basePaths->setValue(null, ['root' => $this->projectRoot]);
		$pathConfigs->setValue(null, ['public' => [$this->projectRoot, 'public']]);

		Functions\when('get_stylesheet_directory')->justReturn($this->projectRoot);
		Functions\when('get_stylesheet_directory_uri')->justReturn('https://example.test/wp-content/themes/example');
		Functions\when('wp_parse_url')->alias('parse_url');
		$this->assets = new AssetMetadataProbe();
	}

	protected function tearDown(): void
	{
		(new ReflectionProperty(Helpers::class, 'basePaths'))->setValue(null, $this->originalBasePaths);
		(new ReflectionProperty(Helpers::class, 'pathConfigs'))->setValue(null, $this->originalPathConfigs);

		foreach (array_reverse($this->files) as $file) {
			unlink($file);
		}
		foreach (array_reverse($this->directories) as $directory) {
			rmdir($directory);
		}

		parent::tearDown();
	}

	/** @param array<string, mixed> $metadata */
	private function writeMetadata(string $path, array $metadata): void
	{
		$file = $this->projectRoot . '/public/' . $path;
		$directory = dirname($file);
		if (!is_dir($directory)) {
			mkdir($directory);
			$this->directories[] = $directory;
		}
		file_put_contents($file, json_encode($metadata, JSON_THROW_ON_ERROR));
		$this->files[] = $file;
	}

	public function testUnknownPrefixDoesNotLoadUnrelatedMetadata(): void
	{
		$this->writeMetadata('applicationBlocksEditor.asset.json', [
			'dependencies' => ['wp-blocks'],
			'format' => 'module',
			'readiness' => 'eightshift-editor-v1',
		]);
		$this->assets->assets['applicationBlocksEditor.asset.json'] = '/assets/theme/missing/applicationBlocksEditor.asset.json';

		$this->assertNull($this->assets->metadata('applicationBlocksEditor.js'));
		$this->assertSame(['wp-element'], $this->assets->dependencies('applicationBlocksEditor.js', ['wp-element']));
	}

	public function testRelativePathLoadsNestedHashedMetadata(): void
	{
		$metadata = ['dependencies' => ['wp-i18n']];
		$this->writeMetadata('build/application.123.asset.json', $metadata);
		$this->assets->assets['application.asset.json'] = 'build/application.123.asset.json';

		$this->assertSame($metadata, $this->assets->metadata('application.js'));
	}

	public function testDefaultPublicUrlPrefersItsExactLocalPath(): void
	{
		$metadata = ['dependencies' => ['wp-blocks']];
		$this->writeMetadata('build/application.asset.json', $metadata);
		$this->writeMetadata('application.asset.json', ['dependencies' => ['wp-element']]);
		$this->assets->assets['application.asset.json'] = 'https://example.test/wp-content/themes/example/public/build/application.asset.json';

		$this->assertSame($metadata, $this->assets->metadata('application.js'));
	}

	public function testMissingDefaultPublicUrlDoesNotLoadAnotherFile(): void
	{
		$this->writeMetadata('application.asset.json', ['dependencies' => ['wp-element']]);
		$this->assets->assets['application.asset.json'] = 'https://example.test/wp-content/themes/example/public/build/application.asset.json';

		$this->assertNull($this->assets->metadata('application.js'));
	}

	public function testDefaultPublicUrlDoesNotFallBackFromAnOutsideSymlink(): void
	{
		$this->writeMetadata('application.asset.json', ['dependencies' => ['wp-element']]);
		$outside = $this->projectRoot . '/outside.json';
		file_put_contents($outside, '{"dependencies":["wp-blocks"]}');
		$this->files[] = $outside;
		$directory = $this->projectRoot . '/public/build';
		mkdir($directory);
		$this->directories[] = $directory;
		$link = $directory . '/application.asset.json';
		if (!@symlink($outside, $link)) {
			self::markTestSkipped('Symlinks are not available.');
		}
		$this->files[] = $link;
		$this->assets->assets['application.asset.json'] = 'https://example.test/wp-content/themes/example/public/build/application.asset.json';

		$this->assertNull($this->assets->metadata('application.js'));
	}

	public function testMetadataCannotEscapePublicDirectory(): void
	{
		$outside = $this->projectRoot . '/outside.json';
		file_put_contents($outside, '{"dependencies":["wp-element"]}');
		$this->files[] = $outside;
		$this->assets->assets['application.asset.json'] = '/%2e%2e/outside.json';

		$this->assertNull($this->assets->metadata('application.js'));
	}

	public function testStandardPublicPrefixAndLegacyKeyStillWork(): void
	{
		$metadata = ['dependencies' => ['wp-i18n']];
		$this->writeMetadata('application.json', $metadata);
		$this->assets->assets['application.json'] = 'https://example.test/wp-content/themes/example/public/application.json';

		$this->assertSame($metadata, $this->assets->metadata('application.js'));
	}

}
