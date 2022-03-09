<?php

namespace Tests\Unit\Helpers;

use EightshiftLibs\Exception\ComponentException;
use EightshiftLibs\Helpers\Components;

use Brain\Monkey;
use EightshiftBoilerplate\Blocks\BlocksExample;

use function Tests\setupMocks;
use function Tests\mock;

beforeAll(function () {
	Monkey\setUp();
	setupMocks();
});

afterAll(function() {
	Monkey\tearDown();
});

test('Asserts ensure string returns a correct result', function ($args) {
	$this->assertIsString(Components::ensureString($args));
})->with('correctArguments');


test('Throws type exception if wrong argument type is passed to ensureString', function ($argument) {
	Components::ensureString($argument);
})->throws(ComponentException::class)
	->with('errorStringArguments');


test('Asserts classnames returns a string', function ($args) {
	$this->assertIsString(Components::classnames($args));
})->with('classesArray');


test('Throws type exception if wrong argument type is passed to classnames', function ($argument) {
	Components::classnames($argument);
})->throws(\TypeError::class)
	->with('errorStringArguments');


test('Asserts that reading manifest.json using getManifest will return an array', function () {
	$results = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/button');

	expect($results)
		->toBeArray()
		->toHaveKey('componentName');
});


test('Asserts that not specifying the path in getManifest will throw an exception', function () {
	Components::getManifest(dirname(__FILE__));
})->throws(ComponentException::class);


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


test('Asserts that using responsive selectors will work', function () {
	$modifiers = ['mobile' => '12', 'tablet' => '12', 'desktop' => '6'];
	$modifiersAlt = ['mobile' => '12', 'tablet' => '12', 'desktop' => ''];

	$withModifier = Components::responsiveSelectors($modifiers, 'width', 'column', true);
	$withoutModifier = Components::responsiveSelectors($modifiers, 'width', 'column', false);
	$withEmptyString = Components::responsiveSelectors($modifiersAlt, 'width', 'column');

	expect($withModifier)
		->toBeString()
		->toBe('column__width-mobile--12 column__width-tablet--12 column__width-desktop--6');

	expect($withoutModifier)
		->toBeString()
		->toBe('column__width-mobile column__width-tablet column__width-desktop');

	expect($withEmptyString)
		->toBeString()
		->toBe('column__width-mobile--12 column__width-tablet--12');
});
