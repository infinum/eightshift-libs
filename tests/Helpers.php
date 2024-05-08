<?php

namespace Tests;

use Mockery;
use EightshiftLibs\Cache\ManifestCacheCli;
use EightshiftLibs\Config\ConfigThemeCli;
use EightshiftLibs\Helpers\Helpers;
use EightshiftLibs\Init\InitBlocksCli;
use Infinum\Blocks\Blocks;
use Infinum\Cache\ManifestCache;
use Mockery\MockInterface;

/**
 * Build all blocks setup output.
 *
 * @return void
 */
function buildTestBlocks()
{
	$configThemeCliMock = new ConfigThemeCli('boilerplate');
	$configThemeCliMock([], getMockArgs($configThemeCliMock->getDefaultArgs()));

	$manifestCacheCliMock = new ManifestCacheCli('boilerplate');
	$manifestCacheCliMock([], getMockArgs($manifestCacheCliMock->getDefaultArgs()));

	$initBlocksCliMock = new InitBlocksCli('boilerplate');
	$initBlocksCliMock([], getMockArgs($initBlocksCliMock->getDefaultArgs()));

	reqOutputFiles(
		'Config/Config.php',
		'Cache/ManifestCache.php',
		'Blocks/Blocks.php',
	);

	(new Blocks(new ManifestCache()))->getBlocksDataFullRaw();
}

/**
 * Get mock args for the cli command.
 *
 * @param array<string, int|string|bool> $args Arguments to pass to the command.
 * @param bool $isCli If the command is cli or not.
 *
 * @return array<string, int|string|bool>
 */
function getMockArgs($args = [], bool $isCli = false): array
{
	$out = $isCli ? 'composer-cli.json' : 'composer.json';
	$sep = \DIRECTORY_SEPARATOR;

	return \array_merge(
		$args,
		[
			'config_path' => Helpers::getProjectPaths('testsData', "composer{$sep}{$out}"),
			'skip_existing' => true,
		]
	);
}

/**
 * Move public manifest data.
 *
 * @return void
 */
function copyPublicManifestData(): void
{
	$sep = \DIRECTORY_SEPARATOR;
	$destination = Helpers::getProjectPaths('cliOutput', "{$sep}public{$sep}manifest.json");
	$source = Helpers::getProjectPaths('testsData', "{$sep}public{$sep}manifest.json");

	$dir = \dirname($destination);

	if (!\file_exists($dir)) {
		\mkdir($dir, 0755, true);
	}

	\copy($source, $destination);
}

/**
 * Require multiple files.
 *
 * @param string ...$files Files to require.
 *
 * @return void
 */
function reqOutputFiles($files): void
{
	$sep = \DIRECTORY_SEPARATOR;
	$files = func_get_args();
	foreach($files as $file) {
		require_once Helpers::getProjectPaths('cliOutput', "{$sep}src{$sep}{$file}");
	}
}

/**
 * Mock Mockery interface.
 *
 * @param string $class Class to mock.
 *
 * @return MockInterface
 */
function mock(string $classname): MockInterface
{
	return Mockery::mock($classname);
}
