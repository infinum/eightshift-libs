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

/**
 * Components::ensureString tests
 */
test('Asserts ensure string returns a correct result', function ($args) {
	$this->assertIsString(Components::ensureString($args));
})->with('correctArguments');

test('Throws type exception if wrong argument type is passed to ensureString', function ($argument) {
	Components::ensureString($argument);
})
->throws(ComponentException::class)
->with('errorStringArguments');

/**
 * Components::classnames tests
 */
test('Asserts classnames returns a string', function ($args) {
	$this->assertIsString(Components::classnames($args));
})->with('classesArray');

test('Throws type exception if wrong argument type is passed to classnames',
	function ($argument) {
		Components::classnames($argument);
	})
	->throws(\TypeError::class)
	->with('errorStringArguments');


/**
 * Components::getManifest tests
 */
test('Asserts that reading manifest.json using getManifest will return an array', function () {
	$results = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/button');

	$this->assertIsArray($results, 'The result is not an array');
	$this->assertArrayHasKey('componentName', $results, 'Missing a key from the manifest.json file');
});

test('Asserts that not specifying the path in getManifest will throw an exception', function () {
	Components::getManifest(dirname(__FILE__));
})->throws(ComponentException::class);

/**
 * Components::render tests
 */
test('Asserts that rendering a component works', function () {
	$results = Components::render('button', []);

	$this->assertNotEmpty($results, 'Component should be rendered here');
	$this->assertStringContainsString('Hello!', $results, 'Component should contain a string');
});

test('Asserts that rendering a component will output a wrapper if parentClass is provided', function () {
	$results = Components::render('button', ['parentClass' => 'test']);

	$this->assertNotEmpty($results, 'Component should be rendered here');
	$this->assertStringContainsString('Hello!', $results, 'Component should contain a string');
	$this->assertStringNotContainsString('test__button.php', $results, 'Component should contain a class name, not file type');
	$this->assertStringContainsString('test__button', $results, 'Component should contain a class name');
});

test('Asserts that providing a missing component will throw an exception without extension', function () {
	Components::render('component', []);
})->throws(ComponentException::class);

test('Asserts that providing a missing component will throw an exception', function () {
	Components::render('component-a.php', []);
})->throws(ComponentException::class);

test('Asserts that render used components defaults', function () {
	$results = Components::render('button', [], '', true);

	$this->assertNotEmpty($results, 'Component should be rendered here');
	$this->assertStringContainsString('Hello!', $results, 'Component should contain a string');
});

/**
 * Components::getDefaultRenderAttributes tests
 */
test('Asserts that getDefaultRenderAttributes will merge rendered attributes with manifest attributes that have default values', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/button/');

	$output = Components::getDefaultRenderAttributes(
		$manifest,
		[
			'buttonContent' => 'test',
			'buttonColor' => 'black',
		]
	);

	$this->assertIsArray($output);
	$this->assertArrayHasKey('buttonContent', $output);
	$this->assertArrayHasKey('buttonColor', $output);
	$this->assertArrayHasKey('buttonSize', $output);
	$this->assertArrayNotHasKey('buttonId', $output);
});

/**
 * Components::responsiveSelectors tests
 */
test('Asserts that using responsive selectors will work', function () {
	$modifiers = ['mobile' => '12', 'tablet' => '12', 'desktop' => '6'];
	$modifiersAlt = ['mobile' => '12', 'tablet' => '12', 'desktop' => ''];

	$withModifier = Components::responsiveSelectors($modifiers, 'width', 'column', true);
	$withoutModifier = Components::responsiveSelectors($modifiers, 'width', 'column', false);
	$withEmptyString = Components::responsiveSelectors($modifiersAlt, 'width', 'column');

	$this->assertIsString($withModifier, 'Result should be a string');
	$this->assertIsString($withoutModifier, 'Result should be a string');
	$this->assertIsString($withEmptyString, 'Result should be a string');

	$this->assertEquals(
		'column__width-mobile--12 column__width-tablet--12 column__width-desktop--6'
		, $withModifier,
		'Strings are not equal in the case of modifiers added'
	);
	$this->assertEquals(
		'column__width-mobile column__width-tablet column__width-desktop',
		$withoutModifier,
		'Strings are not equal in the case there is no modifier'
	);
	$this->assertEquals(
		'column__width-mobile--12 column__width-tablet--12',
		$withEmptyString,
		'Strings are not equal when one option is empty'
	);
});

/**
 * Components::checkAttr tests
 */
test('Asserts that checkAttr works in case attribute is string', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/button/');
	$attributes['buttonAlign'] = 'right';

	$results = Components::checkAttr('buttonAlign', $attributes, $manifest);

	$this->assertIsString($results, 'Result should be a string');
	$this->assertEquals('right', $results, "The set attribute should be {$attributes['buttonAlign']}");
});

test('Asserts that checkAttr works in case attribute is boolean', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/button/');
	$attributes['buttonIsAnchor'] = true;

	$results = Components::checkAttr('buttonIsAnchor', $attributes, $manifest);

	$this->assertIsBool($results, 'THe result should be a boolean');
	$this->assertEquals(true, $results, "The set attribute should be {$attributes['buttonIsAnchor']}");
});

test('Asserts that checkAttr returns false in case attribute is boolean and default is not set', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/button/');
	$attributes['buttonIsAnchor'] = true;

	$results = Components::checkAttr('buttonIsNewTab', $attributes, $manifest);

	$this->assertIsBool($results, 'THe result should be a boolean');
	$this->assertEquals(false, $results, "The set attribute should be false");
});

test('Asserts that checkAttr works in case attribute is array', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/button/');
	$attributes['buttonAttrs'] = ['attr 1', 'attr 2'];

	$results = Components::checkAttr('buttonAttrs', $attributes, $manifest);

	$this->assertIsArray($results, 'The result should be an array');
	$this->assertEquals('attr 1', $results[0], 'The value in the array is not correct');
	$this->assertEquals('attr 2', $results[1], 'The value in the array is not correct');
});

test('Asserts that checkAttr returns empty array in case attribute is array or object and default is not set', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/button/');
	$attributes['buttonSize'] = 'large';

	$results = Components::checkAttr('buttonAttrs', $attributes, $manifest);

	$this->assertIsArray($results, 'The result should be an empty array');
	$this->assertEquals([], $results, "The set attribute should be empty array");
});

test('Asserts that checkAttr returns default value', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/button/');
	$attributes['title'] = 'Some attribute';

	$results = Components::checkAttr('buttonAlign', $attributes, $manifest, 'button');

	$this->assertIsString($results, 'The default value should be a string');
	$this->assertEquals('left', $results, 'The default value should be left');
});

test('Asserts that checkAttr throws exception if manifest key is not set', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/button/');
	$attributes['title'] = 'Some attribute';

	Components::checkAttr('bla', $attributes, $manifest, 'button');
})->throws(\Exception::class, "bla key does not exist in the button component manifest. Please check your implementation.");

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
	$this->assertEquals($results['large'], '10');
});

test('Asserts that checkAttrResponsive returns empty values if attribute is not provided.', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/heading/');
	$attributes = [];

	$results = Components::checkAttrResponsive('headingContentSpacing', $attributes, $manifest);

	$this->assertIsArray($results, 'Result should be an array');
	$this->assertArrayHasKey('large', $results);
	$this->assertEquals($results['large'], '');
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
	$this->assertEquals('button__icon--blue', $selector);
});

test('Asserts that selector returns the correct class when only block class is set', function () {
	$selector = Components::selector('button', 'button');

	$this->assertIsString($selector);
	$this->assertEquals('button', $selector);
});

test('Asserts that selector returns the correct class when element is an empty string', function () {
	$selector = Components::selector('button', 'button', '    ');

	$this->assertIsString($selector);
	$this->assertEquals('button', $selector);
});

/**
 * Components::outputCssVariablesGlobal tests
 */
test('Asserts that outputCssVariablesGlobal returns the correct CSS variables from global manifest', function () {
	$globalManifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks');

	$output = Components::outputCssVariablesGlobal($globalManifest);

	$this->assertIsString($output);
	$this->assertStringContainsString('<style>', $output);
	$this->assertStringContainsString(':root {', $output);
	$this->assertStringContainsString('--global-colors-primary: #C3151B;', $output);
	$this->assertStringNotContainsString('--button-content:', $output);
});

test('Asserts that outputCssVariablesGlobal returns empty string if global manifest data is not provided', function () {
	$output = Components::outputCssVariablesGlobal([]);

	$this->assertIsString($output);
	$this->assertStringNotContainsString('<style>', $output);
});

/**
 * Components::globalInner tests
 */
test('Asserts that globalInner returns the correct css variable for color', function () {
	$globalManifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks');

	$output = Components::globalInner(
		$globalManifest['globalVariables']['colors'],
		'colors'
	);

	$this->assertIsString($output);
	$this->assertStringContainsString('--global-colors-primary: #C3151B;', $output);
});

test('Asserts that globalInner returns the correct css variable for gradients', function () {
	$globalManifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks');

	$output = Components::globalInner(
		$globalManifest['globalVariables']['gradients'],
		'gradients'
	);

	$this->assertIsString($output);
	$this->assertStringContainsString('--global-gradients-black: #000000;', $output);
});

test('Asserts that globalInner returns the correct css variable for fontSizes', function () {
	$globalManifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks');

	$output = Components::globalInner(
		$globalManifest['globalVariables']['fontSizes'],
		'fontSizes'
	);

	$this->assertIsString($output);
	$this->assertStringContainsString('--global-font-sizes-normal: normal;', $output);
});

test('Asserts that globalInner returns the correct css variable for generic value', function () {
	$globalManifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks');

	$output = Components::globalInner(
		$globalManifest['globalVariables']['gutters'],
		'gutters'
	);

	$this->assertIsString($output);
	$this->assertStringContainsString('--global-gutters-none: 0;', $output);
	$this->assertStringContainsString('--global-gutters-default: 1.25em;', $output);
	$this->assertStringContainsString('--global-gutters-big: 2.5em;', $output);
});

test('Asserts that globalInner provided data si wrong', function () {

	$output = Components::globalInner(
		[],
		''
	);

	$this->assertIsString($output);
	$this->assertStringNotContainsString('--global', $output);
});

/**
 * Components::outputCssVariables tests
 */
test('Asserts that outputCssVariables returns the correct CSS variables output for default type no responsive', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/variables');
	$globalManifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks');

	$attributes = [
		'variableDefault' => 'test',
	];

	$output = Components::outputCssVariables(
		$attributes,
		$manifest,
		'uniqueString',
		$globalManifest
	);

	$this->assertIsString($output);
	$this->assertStringContainsString('<style>', $output);
	$this->assertStringContainsString('--variable-default: default;', $output);
	$this->assertStringContainsString(".variables[data-id='uniqueString']", $output);
});

test('Asserts that outputCssVariables returns the correct CSS variables output for default type with responsive', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/variables');
	$globalManifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks');

	$attributes = [
		'variableDefault' => 'test',
	];

	$output = Components::outputCssVariables(
		$attributes,
		$manifest,
		'uniqueString',
		$globalManifest
	);

	$this->assertIsString($output);
	$this->assertStringContainsString('<style>', $output);
	$this->assertStringContainsString('--variable-default: default;', $output);
	$this->assertStringContainsString('@media (min-width: 1200px)', $output);
	$this->assertStringContainsString('--variable-default-large: large;', $output);
	$this->assertStringContainsString('--variable-default-tablet: test;', $output);
	$this->assertStringContainsString(".variables[data-id='uniqueString']", $output);
});

test('Asserts that outputCssVariables returns the correct CSS variables output for value type no responsive', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/variables');
	$globalManifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks');

	$attributes = [
		'variableValue' => 'value1',
	];

	$output = Components::outputCssVariables(
		$attributes,
		$manifest,
		'uniqueString',
		$globalManifest
	);

	$this->assertIsString($output);
	$this->assertStringContainsString('<style>', $output);
	$this->assertStringContainsString('--variable-value-default: default;', $output);
	$this->assertStringContainsString(".variables[data-id='uniqueString']", $output);
});

test('Asserts that outputCssVariables returns the correct CSS variables output for value type with responsive.', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/variables');
	$globalManifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks');

	$attributes = [
		'variableValue' => 'value2',
	];

	$output = Components::outputCssVariables(
		$attributes,
		$manifest,
		'uniqueString',
		$globalManifest
	);

	$this->assertIsString($output);
	$this->assertStringContainsString('<style>', $output);
	$this->assertStringContainsString('--variable-value-default: default;', $output);
	$this->assertStringContainsString('@media (min-width: 991px)', $output);
	$this->assertStringContainsString('--variable-value-tablet: tablet;', $output);
	$this->assertStringContainsString(".variables[data-id='uniqueString']", $output);
});

test('Asserts that outputCssVariables returns the correct CSS variables output for value type with responsive inverse order.', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/variables');
	$globalManifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks');

	$attributes = [
		'variableValue' => 'value3',
	];

	$output = Components::outputCssVariables(
		$attributes,
		$manifest,
		'uniqueString',
		$globalManifest
	);

	$this->assertIsString($output);
	$this->assertStringContainsString('<style>', $output);
	$this->assertStringContainsString('--variable-value-default: default;', $output);
	$this->assertStringContainsString('@media (max-width: 991px)', $output);
	$this->assertStringContainsString('--variable-value-tablet: tablet;', $output);
	$this->assertStringContainsString(".variables[data-id='uniqueString']", $output);
});

test('Asserts that outputCssVariables returns empty for empty variables.', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/variables');
	$globalManifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks');

	$attributes = [
		'variableEmpty' => 'value3',
	];

	$output = Components::outputCssVariables(
		$attributes,
		$manifest,
		'uniqueString',
		$globalManifest
	);

	$this->assertIsString($output);
	$this->assertStringNotContainsString('<style>', $output);
	$this->assertStringNotContainsString('--variable-value-default: default;', $output);
});

test('Asserts that outputCssVariables returns empty for missing variables.', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/variables');
	$globalManifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks');

	$attributes = [
		'variableMissing' => 'value3',
	];

	$output = Components::outputCssVariables(
		$attributes,
		$manifest,
		'uniqueString',
		$globalManifest
	);

	$this->assertIsString($output);
	$this->assertStringNotContainsString('<style>', $output);
	$this->assertStringNotContainsString('--variable-value-default: default;', $output);
});

test('Asserts that outputCssVariables returns array of attributes if default is set.', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/variables');
	$globalManifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks');

	$output = Components::outputCssVariables(
		[],
		$manifest,
		'uniqueString',
		$globalManifest
	);

	$this->assertIsString($output);
	$this->assertStringNotContainsString('<style>', $output);
	$this->assertStringNotContainsString('--variable-value-default: default;', $output);
});

test('Asserts that outputCssVariables returns empty if none of attributes have variables.', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/variables');
	$globalManifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks');

	$output = Components::outputCssVariables(
		[
			"variablesWithoutVariable" => "ivan",
		],
		$manifest,
		'uniqueString',
		$globalManifest
	);

	$this->assertIsString($output);
	$this->assertStringNotContainsString('<style>', $output);
	$this->assertStringNotContainsString('--variable-value-default: default;', $output);
});

test('Asserts that outputCssVariables returns empty if attributes variables option is not set.', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/variables');
	$globalManifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks');

	$output = Components::outputCssVariables(
		[
			"variableValueNotSetValue" => "ivan",
		],
		$manifest,
		'uniqueString',
		$globalManifest
	);

	$this->assertIsString($output);
	$this->assertStringNotContainsString('<style>', $output);
	$this->assertStringNotContainsString('--variable-value-default: default;', $output);
});

test('Asserts that outputCssVariables returns empty for missing attributes array.', function () {
	$globalManifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks');

	$output = Components::outputCssVariables(
		[],
		[],
		'uniqueString',
		$globalManifest
	);

	$this->assertIsString($output);
	$this->assertStringNotContainsString('<style>', $output);
	$this->assertStringNotContainsString('--variable-value-default: default;', $output);
});

test('Asserts that outputCssVariables returns empty for missing variables array.', function () {
	$globalManifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks');
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/typography');

	$output = Components::outputCssVariables(
		[
			"test" => "1",
		],
		$manifest,
		'uniqueString',
		$globalManifest
	);

	$this->assertIsString($output);
	$this->assertStringNotContainsString('<style>', $output);
	$this->assertStringNotContainsString('--variable-value-default: default;', $output);
});

test('Asserts that outputCssVariables returns empty if variables array is not array.', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/variables');
	$globalManifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks');

	$output = Components::outputCssVariables(
		[
			"variableValueNotArray" => "aaa",
		],
		$manifest,
		'uniqueString',
		$globalManifest
	);

	$this->assertIsString($output);
	$this->assertStringNotContainsString('<style>', $output);
	$this->assertStringNotContainsString('--variable-value-default: default;', $output);
});

test('Asserts that outputCssVariables returns empty globalManifest is not set.', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/variables');

	$output = Components::outputCssVariables(
		[
			"variableDefault" => "aaa",
		],
		$manifest,
		'uniqueString',
		[]
	);

	$this->assertIsString($output);
	$this->assertStringNotContainsString('<style>', $output);
	$this->assertStringNotContainsString('--variable-value-default: default;', $output);
});

test('Asserts that outputCssVariables returns variable for attributes which expect booleans.', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/variables');
	$globalManifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks');

	$attributes = [
		'variableBool' => true,
	];

	$output = Components::outputCssVariables(
		$attributes,
		$manifest,
		'uniqueString',
		$globalManifest
	);

	$this->assertIsString($output);
	$this->assertStringContainsString('<style>', $output);
	$this->assertStringContainsString('--variable-created-by-boolean: value-when-true;', $output);

	$attributes = [
		'variableBool' => false,
	];

	$output = Components::outputCssVariables(
		$attributes,
		$manifest,
		'uniqueString',
		$globalManifest
	);

	$this->assertIsString($output);
	$this->assertStringContainsString('<style>', $output);
	$this->assertStringContainsString('--variable-created-by-boolean: value-when-false;', $output);
});

test('Asserts that manifest has responsive attributes defined but without variables definition', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/responsiveVariables');
	$globalManifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks');
	unset($manifest['variables']);
	$attributes = [
		'variableValueMobile' => 'value1',
	];

	$output = Components::outputCssVariables(
		$attributes,
		$manifest,
		'uniqueString',
		$globalManifest
	);

	$this->assertIsString($output);
	$this->assertStringNotContainsString('<style>', $output);

	$value = '2';

	$attributes = [
		'variableDefaultDesktop' => $value,
	];

	$output = Components::outputCssVariables(
		$attributes,
		$manifest,
		'uniqueString',
		$globalManifest
	);

	$this->assertIsString($output);
	$this->assertStringNotContainsString('<style>', $output);
	$this->assertStringNotContainsString("variable-default-normal: default-desktop-{$value}", $output);
});

test('Asserts that manifest has responsive attributes defined but without responsive variables definition', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/responsiveVariables');
	$globalManifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks');
	unset($manifest['variables']['responsiveVariableDefault']);
	unset($manifest['variables']['responsiveVariableValue']);
	$attributes = [
		'variableValueMobile' => 'value1',
	];

	$output = Components::outputCssVariables(
		$attributes,
		$manifest,
		'uniqueString',
		$globalManifest
	);

	$this->assertIsString($output);
	$this->assertStringContainsString('<style>', $output);
	$this->assertStringContainsString('variable-value-default: value1-mobile', $output);
	$this->assertStringNotContainsString('variable-value-default-responsive: value1-responsive', $output);

	$value = '2';

	$attributes = [
		'variableDefaultDesktop' => $value,
	];

	$output = Components::outputCssVariables(
		$attributes,
		$manifest,
		'uniqueString',
		$globalManifest
	);

	$this->assertIsString($output);
	$this->assertStringContainsString('<style>', $output);
	$this->assertStringContainsString("variable-default-normal: default-desktop-{$value}", $output);
	$this->assertStringNotContainsString("variable-default-responsive: default-responsive-{$value}", $output);
});

test('Asserts that only responsive variables are defined', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/responsiveVariables');
	$globalManifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks');
	unset($manifest['variables']['variableDefaultLarge']);
	unset($manifest['variables']['variableDefaultDesktop']);
	unset($manifest['variables']['variableDefaultMobile']);
	unset($manifest['variables']['variableValueDesktop']);
	unset($manifest['variables']['variableValueMobile']);

	$attributes = [
		'variableValueMobile' => 'value1',
	];

	$output = Components::outputCssVariables(
		$attributes,
		$manifest,
		'uniqueString',
		$globalManifest
	);

	$this->assertIsString($output);
	$this->assertStringContainsString('<style>', $output);
	$this->assertStringContainsString('variable-value-default-responsive: value1-responsive', $output);
	$this->assertStringNotContainsString('variable-value-default: value1-mobile', $output);

	$value = '2';

	$attributes = [
		'variableDefaultDesktop' => $value,
	];

	$output = Components::outputCssVariables(
		$attributes,
		$manifest,
		'uniqueString',
		$globalManifest
	);

	$this->assertIsString($output);
	$this->assertStringContainsString('<style>', $output);
	$this->assertStringContainsString("variable-default-responsive: default-responsive-{$value}", $output);
	$this->assertStringContainsString('@media (min-width: 1199px)', $output);
	$this->assertStringContainsString("variable-default: default-responsive-{$value}", $output);
	$this->assertStringNotContainsString("variable-default-normal: default-desktop-{$value}", $output);
});

test('Asserts that responsive variables and default variables are defined', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/responsiveVariables');
	$globalManifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks');

	$attributes = [
		'variableValueMobile' => 'value1',
		'variableValueDesktop' => 'value2',
	];

	$output = Components::outputCssVariables(
		$attributes,
		$manifest,
		'uniqueString',
		$globalManifest
	);

	$this->assertIsString($output);
	$this->assertStringContainsString('<style>', $output);
	$this->assertStringContainsString('variable-value-default-responsive: value1-responsive', $output);
	$this->assertStringContainsString('variable-value-default-responsive: value2-responsive', $output);
	$this->assertStringContainsString('@media (min-width: 1199px)', $output);
	$this->assertStringContainsString('variable-value-default: value2-desktop', $output);
	$this->assertStringContainsString('variable-value-default: value1-mobile', $output);

	$value = '2';

	$attributes = [
		'variableDefaultMobile' => $value,
	];

	$output = Components::outputCssVariables(
		$attributes,
		$manifest,
		'uniqueString',
		$globalManifest
	);

	$this->assertIsString($output);
	$this->assertStringContainsString('<style>', $output);
	$this->assertStringContainsString('variable-default:', $output);
	$this->assertStringContainsString("variable-default: default-mobile-{$value}", $output);
	$this->assertStringContainsString("variable-default: default-responsive-{$value}", $output);
	$this->assertStringContainsString("variable-default-responsive: default-responsive-{$value}", $output);
	$this->assertStringContainsString("variable-default-normal: default-mobile-{$value}", $output);
});

test('Asserts that manifest has responsive variables defined but without appearance of responsiveAttributes', function () {
	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/responsiveVariables');
	$globalManifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks');
	unset($manifest['responsiveAttributes']);

	$attributes = [
		'variableValueMobile' => 'value1',
	];

	$output = Components::outputCssVariables(
		$attributes,
		$manifest,
		'uniqueString',
		$globalManifest
	);

	$this->assertIsString($output);
	$this->assertStringContainsString('<style>', $output);
	$this->assertStringContainsString('variable-value-default: value1-mobile', $output);
	$this->assertStringNotContainsString('variable-value-default-responsive: value1-responsive', $output);

	$value = '2';

	$attributes = [
		'variableDefaultDesktop' => $value,
	];

	$output = Components::outputCssVariables(
		$attributes,
		$manifest,
		'uniqueString',
		$globalManifest
	);

	$this->assertIsString($output);
	$this->assertStringContainsString('<style>', $output);
	$this->assertStringContainsString("variable-default-normal: default-desktop-{$value}", $output);
	$this->assertStringNotContainsString("variable-default-responsive: default-responsive-{$value}", $output);
});

/**
 * Components::getUnique tests
 */
test('Asserts that getUnique returns the correct output', function () {
	$output = Components::getUnique();

	$this->assertIsString($output);
	$this->assertMatchesRegularExpression('/[a-z0-9]{1,32}/m', $output);
});

/**
 * Components::arrayIsList tests
 */
test('Asserts that arrayIsList returns the correct output for correct input', function ($input) {
	$case = Components::arrayIsList($input);

	$this->assertIsBool($case);
	$this->assertTrue($case);
})->with('arrayIsListWrong');

test('Asserts that arrayIsList returns the correct output for wrong input', function ($input) {
	$case = Components::arrayIsList($input);

	$this->assertIsBool($case);
	$this->assertFalse($case);
})->with('arrayIsListCorrect');

/**
 * Components::camelToKebabCase tests
 */
test('Asserts that camelToKebabCase returns the correct output', function () {
	$output = Components::camelToKebabCase('superCoolTestString');

	$this->assertEquals('super-cool-test-string', $output);
});

test('Asserts that camelToKebabCase returns the wrong output', function () {
	$output = Components::camelToKebabCase('super_CoolTest-String ivan');

	$this->assertNotEquals('super-cool-test-string-ivan', $output);
});

/**
 * Components::kebabToCamelCase tests
 */
test('Asserts that kebabToCamelCase returns the correct output', function () {
	$output = Components::kebabToCamelCase('super-cool-test-string');

	$this->assertEquals('superCoolTestString', $output);
});

test('Asserts that kebabToCamelCase returns the wrong output', function () {
	$output = Components::kebabToCamelCase('super-cool-test-string-goc');

	$this->assertNotEquals('super_CoolTest-String goc', $output);
});

/**
 * Components::kebabToCamelCase tests
 */
test('Asserts that kebabToCamelCase returns the correct output with a different separator', function () {
	$output = Components::kebabToCamelCase('super_cool_test_string', '_');

	$this->assertEquals('superCoolTestString', $output);
});

/**
 * Components::kebabToCamelCase tests
 */
test('Asserts that kebabToCamelCase returns the correct output with numbers as a string', function () {
	$output = Components::kebabToCamelCase('123-456-789');

	$this->assertEquals('123456789', $output);
});

/**
 * Components::kebabToCamelCase tests
 */
test('Asserts that kebabToCamelCase returns the correct output with a non-kebab-case string', function () {
	$output = Components::kebabToCamelCase('non kebab string');

	$this->assertEquals('non kebab string', $output);
});

/**
 * Components::props tests
 */
test('Asserts props for heading block will return only heading attributes', function () {
	$headingBlock = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/custom/heading');
	$headingComponent = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/heading');
	$typographyComponent = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/typography');

	$attributes = array_merge(
		$headingBlock['attributes'],
		$headingComponent['attributes'],
		$typographyComponent['attributes']
	);

	mock('alias:EightshiftBoilerplate\Config\Config')
		->shouldReceive('getProjectPath')
		->andReturn('tests/data');

	$this->blocksExample = new BlocksExample();
	$this->blocksExample->getBlocksDataFullRaw();
	$output = Components::props($attributes, $headingBlock['blockName'], '');

	$this->assertIsArray($output);
	$this->assertContains('headingAlign', array_keys($output), "Output array doesn't contain headingAlign attribute key.");
	$this->assertNotContains('typographySize', array_keys($output), "Output array does contain typographySize attribute key.");
});

test('Asserts props for heading component will return only typography attributes', function () {
	$headingBlock = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/custom/heading');
	$headingComponent = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/heading');
	$typographyComponent = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/typography');

	$attributes = array_merge(
		$headingBlock['attributes'],
		$headingComponent['attributes'],
		$typographyComponent['attributes']
	);

	mock('alias:EightshiftBoilerplate\Config\Config')
		->shouldReceive('getProjectPath')
		->andReturn('tests/data');

	$this->blocksExample = new BlocksExample();
	$this->blocksExample->getBlocksDataFullRaw();
	$output = Components::props($attributes, 'typography');

	$this->assertIsArray($output);
  echo print_r(array_keys($output), true);
	$this->assertContains('typographyContent', array_keys($output), "Output array doesn't contain typographyContent attribute key.");
	$this->assertNotContains('headingSize', array_keys($output), "Output array does contain headingSize attribute key.");
});
