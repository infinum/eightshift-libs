<?php

namespace Tests\Unit\Helpers;

use EightshiftLibs\Exception\ComponentException;
use EightshiftLibs\Exception\InvalidPath;
use EightshiftLibs\Helpers\Helpers;

use function Tests\buildTestBlocks;

beforeEach(function () {
	buildTestBlocks();
});

// ------------------------------------------
// render
// ------------------------------------------

test('Asserts that rendering a component works', function () {
	$results = Helpers::render('button', []);

	expect($results)
		->not->toBeEmpty()
		->toContain('Hello!');
});

test('Asserts that rendering a component will output a wrapper if parentClass is provided', function () {
	$results = Helpers::render('button', ['parentClass' => 'test'], 'blocks');

	expect($results)
		->not->toBeEmpty()
		->toContain('Hello!')
		->not->toContain('test__button.php');
});

test('Asserts that providing a missing component will throw an exception without extension', function () {
	Helpers::render('component', []);
})->throws(InvalidPath::class);

test('Asserts that providing a missing component will throw an exception', function () {
	Helpers::render('component-a.php', []);
})->throws(InvalidPath::class);

test('Asserts that render used components defaults', function () {
	$results = Helpers::render('button', [], '', true);

	expect($results)
		->not->toBeEmpty()
		->toContain('Hello!');
});

// ------------------------------------------
// getManifest
// ------------------------------------------

test('Asserts that "getManifestByDir" will return correct files if path name is used.', function () {
	expect(Helpers::getManifestByDir(Helpers::getProjectPaths('cliOutput', 'src/Blocks/wrapper')))
		->toBeArray()
		->toHaveKey('componentName')
		->and(Helpers::getManifestByDir(Helpers::getProjectPaths('cliOutput', 'src/Blocks/components/button')))
		->toBeArray()
		->toHaveKey('componentName')
		->and(Helpers::getManifestByDir(Helpers::getProjectPaths('cliOutput', 'src/Blocks/custom/button')))
		->toBeArray()
		->toHaveKey('blockName');
});
