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


test('Asserts that checkAttr works in case attribute is string', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/button/');
	$attributes['buttonAlign'] = 'right';

	$results = Components::checkAttr('buttonAlign', $attributes, $manifest);

	expect($results)
		->toBeString()
		->toBe('right');
});


test('Asserts that checkAttr works in case attribute is boolean', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/button/');
	$attributes['buttonIsAnchor'] = true;

	$results = Components::checkAttr('buttonIsAnchor', $attributes, $manifest);

	expect($results)
		->toBeBool()
		->toBeTrue();
});


test('Asserts that checkAttr returns false in case attribute is boolean and default is not set', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/button/');
	$attributes['buttonIsAnchor'] = true;

	$results = Components::checkAttr('buttonIsNewTab', $attributes, $manifest);

	expect($results)
		->toBeBool()
		->toBeFalse();
});


test('Asserts that checkAttr returns null in case attribute is boolean, default is not set and undefined is allowed', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/button/');
	$attributes['buttonIsAnchor'] = true;

	$results = Components::checkAttr('buttonIsNewTab', $attributes, $manifest, true);

	expect($results)
		->not->toBeBool()
		->toBeNull();
});

// To do: refactor the rest


test('Asserts that checkAttr works in case attribute is array', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/button/');
	$attributes['buttonAttrs'] = ['attr 1', 'attr 2'];

	$results = Components::checkAttr('buttonAttrs', $attributes, $manifest);

	$this->assertIsArray($results, 'The result should be an array');
	$this->assertSame('attr 1', $results[0], 'The value in the array is not correct');
	$this->assertSame('attr 2', $results[1], 'The value in the array is not correct');
});

test('Asserts that checkAttr returns empty array in case attribute is array or object and default is not set', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/button/');
	$attributes['buttonSize'] = 'large';

	$results = Components::checkAttr('buttonAttrs', $attributes, $manifest);

	$this->assertIsArray($results, 'The result should be an empty array');
	$this->assertSame([], $results, "The set attribute should be empty array");
});

test('Asserts that checkAttr returns null in case attribute is array or object, default is not set, and undefined is allowed', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/button/');
	$attributes['buttonSize'] = 'large';

	$results = Components::checkAttr('buttonAttrs', $attributes, $manifest, true);

	$this->assertIsNotArray($results, 'The result should not be an empty array');
	$this->assertSame(null, $results, "The set attribute should be null");
});

test('Asserts that checkAttr returns default value', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/button/');
	$attributes['title'] = 'Some attribute';

	$results = Components::checkAttr('buttonAlign', $attributes, $manifest, 'button');

	$this->assertIsString($results, 'The default value should be a string');
	$this->assertSame('left', $results, 'The default value should be left');
});

test('Asserts that checkAttr throws exception if manifest key is not set', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/button/');
	$attributes['title'] = 'Some attribute';

	Components::checkAttr('bla', $attributes, $manifest, 'button');
})->throws(\Exception::class, "bla key does not exist in the button component manifest. Please check your implementation.");

test('Asserts that checkAttr returns attribute based on prefix if set', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/button/');
	$attributes = [
		'prefix' => 'prefixedMultipleTimesButton',
		'prefixedMultipleTimesButtonAlign' => 'right'
	];

	$results = Components::checkAttr('buttonAlign', $attributes, $manifest);

	$this->assertIsString($results, 'Result should be a string');
	$this->assertSame('right', $results, "The set attribute should be {$attributes['prefixedMultipleTimesButtonAlign']}");
});

/**
 * Components::checkAttrResponsive tests
 */
test('Asserts that checkAttrResponsive returns the correct output.', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/heading/');
	$attributes = [
		'headingContentSpacingLarge' => '10',
		'headingContentSpacingDesktop' => '5',
		'headingContentSpacingTablet' => '3',
		'headingContentSpacingMobile' => '1',
	];

	$results = Components::checkAttrResponsive('headingContentSpacing', $attributes, $manifest);

	$this->assertIsArray($results, 'Result should be an array');
	$this->assertArrayHasKey('large', $results);
	$this->assertSame($results['large'], '10');
});

test('Asserts that checkAttrResponsive returns empty values if attribute is not provided.', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/heading/');
	$attributes = [];

	$results = Components::checkAttrResponsive('headingContentSpacing', $attributes, $manifest);

	$this->assertIsArray($results, 'Result should be an array');
	$this->assertArrayHasKey('large', $results);
	$this->assertSame($results['large'], '');
});

test('Asserts that checkAttrResponsive returns null if default is not set and undefined is allowed.', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/heading/');
	$attributes = [
		'headingContentSpacingDesktop' => '2'
	];

	$results = Components::checkAttrResponsive('headingContentSpacing', $attributes, $manifest, true);

	$this->assertIsArray($results, 'Result should be an array');
	$this->assertArrayHasKey('large', $results);
	$this->assertArrayHasKey('desktop', $results);
	$this->assertArrayHasKey('tablet', $results);
	$this->assertSame($results['large'], null);
	$this->assertSame($results['desktop'], '2');
	$this->assertSame($results['tablet'], null);
});

test('Asserts that checkAttrResponsive throws error if responsiveAttribute key is missing.', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/button/');
	$attributes = [];

	Components::checkAttrResponsive('headingContentSpacing', $attributes, $manifest, 'button');
})->throws(\Exception::class, 'It looks like you are missing responsiveAttributes key in your button component manifest.');

test('Asserts that checkAttrResponsive throws error if keyName key is missing responsiveAttributes array.', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/heading/');
	$attributes = [];

	Components::checkAttrResponsive('testAttribute', $attributes, $manifest, 'button');
})->throws(\Exception::class, 'It looks like you are missing the testAttribute key in your manifest responsiveAttributes array.');

/**
 * Components::selector tests
 */
test('Asserts that selectorBlock returns the correct class when attributes are set', function () {
	$selector = Components::selector('button', 'button', 'icon', 'blue');

	$this->assertIsString($selector);
	$this->assertSame('button__icon--blue', $selector);
});

test('Asserts that selector returns the correct class when only block class is set', function () {
	$selector = Components::selector('button', 'button');

	$this->assertIsString($selector);
	$this->assertSame('button', $selector);
});

test('Asserts that selector returns the correct class when element is an empty string', function () {
	$selector = Components::selector('button', 'button', '    ');

	$this->assertIsString($selector);
	$this->assertSame('button', $selector);
});
