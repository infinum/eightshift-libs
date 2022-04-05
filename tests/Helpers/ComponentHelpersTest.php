<?php

namespace Tests\Unit\Helpers;

use EightshiftLibs\Exception\ComponentException;
use EightshiftLibs\Exception\InvalidBlock;
use EightshiftLibs\Helpers\Components;

use Brain\Monkey;

use function Tests\setupMocks;

beforeAll(function () {
	Monkey\setUp();
	setupMocks();
});

afterAll(function() {
	Monkey\tearDown();
});

beforeEach(function() {
	global $esBlocks;
	$esBlocks = null;
});

afterEach(function() {
	global $esBlocks;
	$esBlocks = null;
});

test('Asserts that reading manifest.json using getManifest will return an array', function () {
	$results = Components::getManifestDirect(\dirname(__FILE__, 2) . '/data/src/Blocks/components/button');

	expect($results)
		->toBeArray()
		->toHaveKey('componentName');
});

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

test('Asserts that not specifying the path in getManifest will throw an exception', function () {
	Components::getManifestDirect(\dirname(__FILE__));
})->throws(ComponentException::class);

test('Asserts that arrayIsList function will correctly identify lists', function () {

	$isList = Components::arrayIsList([1, 2, 3]);
	$isNotList = Components::arrayIsList(['a' => 1, 'b' => 2, 'c' => 3]);

	expect($isList)
		->toBeBool()
		->toBeTrue();

	expect($isNotList)
		->toBeBool()
		->not->toBeTrue();
});
