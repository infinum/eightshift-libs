<?php

namespace Tests\Unit\Helpers;

use EightshiftLibs\Exception\ComponentException;
use EightshiftLibs\Helpers\Components;

use EightshiftBoilerplate\Blocks\BlocksExample;

use function Tests\buildTestBlocks;
use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	buildTestBlocks();
	(new BlocksExample())->getBlocksDataFullRaw();
});

afterEach(function () {
	setAfterEach();
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
	expect(Components::getManifest(\dirname(__FILE__, 2) . '/data/src/Blocks/wrapper'))
		->toBeArray()
		->toHaveKey('componentName');

	expect(Components::getManifest(\dirname(__FILE__, 2) . '/data/src/Blocks'))
		->toBeArray()
		->toHaveKey('namespace');

	expect(Components::getManifest(\dirname(__FILE__, 2) . '/data/src/Blocks/components/button'))
		->toBeArray()
		->toHaveKey('componentName');

	expect(Components::getManifest(\dirname(__FILE__, 2) . '/data/src/Blocks/custom/button'))
		->toBeArray()
		->toHaveKey('blockName');
});

test('Asserts that "getManifest" will return correct files if name is used.', function () {
	expect(Components::getManifest('wrapper'))
		->toBeArray()
		->toHaveKey('componentName');

	expect(Components::getManifest('settings'))
		->toBeArray()
		->toHaveKey('namespace');

	expect(Components::getManifest('component', 'button'))
		->toBeArray()
		->toHaveKey('componentName');

	expect(Components::getManifest('block', 'button'))
		->toBeArray()
		->toHaveKey('blockName');
});

// ------------------------------------------
// getManifestDirect
// ------------------------------------------

test('Asserts that reading manifest.json using getManifest will return an array', function () {
	$results = Components::getManifestDirect(\dirname(__FILE__, 2) . '/data/src/Blocks/components/button');

	expect($results)
		->toBeArray()
		->toHaveKey('componentName');
});

test('Asserts that not specifying the path in getManifest will throw an exception', function () {
	Components::getManifestDirect(\dirname(__FILE__));
})->throws(ComponentException::class);
