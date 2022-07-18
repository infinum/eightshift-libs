<?php

namespace Tests\Unit\Helpers;

use EightshiftLibs\Exception\ComponentException;
use EightshiftLibs\Helpers\Components;

use function Tests\buildTestBlocks;

beforeEach(function () {
	buildTestBlocks();
});

// ------------------------------------------
// render
// ------------------------------------------

test('Asserts that rendering a component works', function () {
	$results = Components::render('button', []);

	expect($results)
		->not->toBeEmpty()
		->toContain('Hello!');
});

test('Asserts that rendering a component will output a wrapper if parentClass is provided', function () {
	$results = Components::render('button', ['parentClass' => 'test']);

	expect($results)
		->not->toBeEmpty()
		->toContain('Hello!')
		->not->toContain('test__button.php')
		->toContain('test__button');
});

test('Asserts that providing a missing component will throw an exception without extension', function () {
	Components::render('component', []);
})->throws(ComponentException::class);

test('Asserts that providing a missing component will throw an exception', function () {
	Components::render('component-a.php', []);
})->throws(ComponentException::class);

test('Asserts that render used components defaults', function () {
	$results = Components::render('button', [], '', true);

	expect($results)
		->not->toBeEmpty()
		->toContain('Hello!');
});

// ------------------------------------------
// getManifest
// ------------------------------------------

test('Asserts that "getManifest" will return correct files if path name is used.', function () {
	expect(Components::getManifest(Components::getProjectPaths('testsData', 'src/Blocks/wrapper')))
		->toBeArray()
		->toHaveKey('componentName')
		->and(Components::getManifest(Components::getProjectPaths('testsData', 'src/Blocks')))
		->toBeArray()
		->toHaveKey('namespace')
		->and(Components::getManifest(Components::getProjectPaths('testsData', 'src/Blocks/components/button')))
		->toBeArray()
		->toHaveKey('componentName')
		->and(Components::getManifest(Components::getProjectPaths('testsData', 'src/Blocks/custom/button')))
		->toBeArray()
		->toHaveKey('blockName');
});

test('Asserts that "getManifest" will return correct files if name is used.', function () {
	expect(Components::getManifest('wrapper'))
		->toBeArray()
		->toHaveKey('componentName')
		->and(Components::getManifest('settings'))
		->toBeArray()
		->toHaveKey('namespace')
		->and(Components::getManifest('component', 'button'))
		->toBeArray()
		->toHaveKey('componentName')
		->and(Components::getManifest('block', 'button'))
		->toBeArray()
		->toHaveKey('blockName');
});

// ------------------------------------------
// getManifestDirect
// ------------------------------------------

test('Asserts that reading manifest.json using getManifest will return an array', function () {
	$results = Components::getManifestDirect(Components::getProjectPaths('testsData', 'src/Blocks/components/button'));

	expect($results)
		->toBeArray()
		->toHaveKey('componentName');
});

test('Asserts that not specifying the path in getManifest will throw an exception', function () {
	Components::getManifestDirect(\dirname(__FILE__));
})->throws(ComponentException::class);
