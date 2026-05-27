<?php

/**
 * Rector bootstrap.
 *
 * @package EightshiftSso
 */

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\Property\RemoveUselessVarTagRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return RectorConfig::configure()
	->withPaths([__DIR__ . '/src'])
	->withBootstrapFiles([__DIR__ . '/vendor/autoload.php'])
	->withPhpSets(php84: true)
	->withSets([LevelSetList::UP_TO_PHP_84, SetList::CODE_QUALITY, SetList::DEAD_CODE, SetList::TYPE_DECLARATION, SetList::EARLY_RETURN])
	->withSkip([
		RemoveUselessVarTagRector::class,
		__DIR__ . '/src/**/*Example.php',
	])
	->withIndent("\t", indentSize: 1)
	->withImportNames(importShortClasses: false, removeUnusedImports: true)
	->withParallel();
