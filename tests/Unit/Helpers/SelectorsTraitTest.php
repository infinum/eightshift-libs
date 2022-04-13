<?php

namespace Tests\Unit\Helpers;

use EightshiftLibs\Exception\ComponentException;
use EightshiftLibs\Helpers\Components;

use Brain\Monkey;

use function Tests\setupUnitTestMocks;

beforeAll(function () {
	Monkey\setUp();
	setupUnitTestMocks();
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

// ------------------------------------------
// selector
// ------------------------------------------

test('Asserts that selectorBlock returns the correct class when attributes are set', function () {
	$selector = Components::selector('button', 'button', 'icon', 'blue');

	expect($selector)
		->toBeString()
		->toBe('button__icon--blue');
});


test('Asserts that selector returns the correct class when only block class is set', function () {
	$selector = Components::selector('button', 'button');

	expect($selector)
		->toBeString()
		->toBe('button');
});


test('Asserts that selector returns the correct class when element is an empty string', function () {
	$selector = Components::selector('button', 'button', '    ');

	expect($selector)
		->toBeString()
		->toBe('button');
});

// ------------------------------------------
// responsiveSelectors
// ------------------------------------------

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

// ------------------------------------------
// ensureString
// ------------------------------------------

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

// ------------------------------------------
// classnames
// ------------------------------------------

test('Throws type exception if wrong argument type is passed to classnames', function ($argument) {
	Components::classnames($argument);
})->throws(\TypeError::class)
	->with('errorStringArguments');

