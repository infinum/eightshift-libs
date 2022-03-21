<?php

namespace Tests\Unit\Helpers;

use EightshiftLibs\Exception\ComponentException;
use EightshiftLibs\Exception\InvalidBlock;
use EightshiftLibs\Helpers\Components;

use Brain\Monkey;
use Brain\Monkey\Functions;
use EightshiftBoilerplate\Blocks\BlocksExample;
use Exception;

use function Tests\setupMocks;
use function Tests\mock;

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
	$results = Components::getManifest(\dirname(__FILE__, 2) . '/data/src/Blocks/components/button');

	expect($results)
		->toBeArray()
		->toHaveKey('componentName');
});


test('Asserts that not specifying the path in getManifest will throw an exception', function () {
	Components::getManifest(\dirname(__FILE__));
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
	$manifest = Components::getManifest(\dirname(__FILE__, 2) . '/data/src/Blocks/components/button/');
	$attributes['buttonAlign'] = 'right';

	$results = Components::checkAttr('buttonAlign', $attributes, $manifest);

	expect($results)
		->toBeString()
		->toBe('right');
});


test('checkAttr will throw an exception in the case that the block name is missing in the manifest', function () {
	$manifest = Components::getManifest(\dirname(__FILE__, 2) . '/data/src/Blocks/custom/button/');
	$attributes['buttonText'] = 'left';

	Components::checkAttr('buttonAlign', $attributes, $manifest);
})->throws(Exception::class, 'buttonAlign key does not exist in the button block manifest. Please check your implementation.');


test('Asserts that checkAttr works in case attribute is boolean', function () {
	$manifest = Components::getManifest(\dirname(__FILE__, 2) . '/data/src/Blocks/components/button/');
	$attributes['buttonIsAnchor'] = true;

	$results = Components::checkAttr('buttonIsAnchor', $attributes, $manifest);

	expect($results)
		->toBeBool()
		->toBeTrue();
});


test('Asserts that checkAttr returns false in case attribute is boolean and default is not set', function () {
	$manifest = Components::getManifest(\dirname(__FILE__, 2) . '/data/src/Blocks/components/button/');
	$attributes['buttonIsAnchor'] = true;

	$results = Components::checkAttr('buttonIsNewTab', $attributes, $manifest);

	expect($results)
		->toBeBool()
		->toBeFalse();
});


test('Asserts that checkAttr returns null in case attribute is boolean, default is not set and undefined is allowed', function () {
	$manifest = Components::getManifest(\dirname(__FILE__, 2) . '/data/src/Blocks/components/button/');
	$attributes['buttonIsAnchor'] = true;

	$results = Components::checkAttr('buttonIsNewTab', $attributes, $manifest, true);

	expect($results)
		->not->toBeBool()
		->toBeNull();
});


test('Asserts that checkAttr works in case attribute is array', function () {
	$manifest = Components::getManifest(\dirname(__FILE__, 2) . '/data/src/Blocks/components/button/');
	$attributes['buttonAttrs'] = ['attr 1', 'attr 2'];

	$results = Components::checkAttr('buttonAttrs', $attributes, $manifest);

	expect($results)
		->toBeArray();

	expect($results[0])
		->toBe('attr 1');
	expect($results[1])
		->toBe('attr 2');
});


test('Asserts that checkAttr returns empty array in case attribute is array or object and default is not set', function () {
	$manifest = Components::getManifest(\dirname(__FILE__, 2) . '/data/src/Blocks/components/button/');
	$attributes['buttonSize'] = 'large';

	$results = Components::checkAttr('buttonAttrs', $attributes, $manifest);


	expect($results)
		->toBeArray()
		->toBe([]);
});


test('Asserts that checkAttr returns null in case attribute is array or object, default is not set, and undefined is allowed', function () {
	$manifest = Components::getManifest(\dirname(__FILE__, 2) . '/data/src/Blocks/components/button/');
	$attributes['buttonSize'] = 'large';

	$results = Components::checkAttr('buttonAttrs', $attributes, $manifest, true);

	expect($results)
		->not->toBeArray()
		->toBeNull();
});


test('Asserts that checkAttr returns default value', function () {
	$manifest = Components::getManifest(\dirname(__FILE__, 2) . '/data/src/Blocks/components/button/');
	$attributes['title'] = 'Some attribute';

	$results = Components::checkAttr('buttonAlign', $attributes, $manifest, 'button');

	expect($results)
		->toBeString()
		->toBe('left');
});


test('Asserts that checkAttr throws exception if manifest key is not set', function () {
	$manifest = Components::getManifest(\dirname(__FILE__, 2) . '/data/src/Blocks/components/button/');
	$attributes['title'] = 'Some attribute';

	Components::checkAttr('bla', $attributes, $manifest, 'button');
})->throws(Exception::class, "bla key does not exist in the button component manifest. Please check your implementation.");


test('Asserts that checkAttr returns attribute based on prefix if set', function () {
	$manifest = Components::getManifest(\dirname(__FILE__, 2) . '/data/src/Blocks/components/button/');
	$attributes = [
		'prefix' => 'prefixedMultipleTimesButton',
		'prefixedMultipleTimesButtonAlign' => 'right'
	];

	$results = Components::checkAttr('buttonAlign', $attributes, $manifest);

	expect($results)
		->toBeString()
		->toBe('right');
});


test('Asserts that checkAttrResponsive returns the correct output.', function () {
	$manifest = Components::getManifest(\dirname(__FILE__, 2) . '/data/src/Blocks/components/heading/');
	$attributes = [
		'headingContentSpacingLarge' => '10',
		'headingContentSpacingDesktop' => '5',
		'headingContentSpacingTablet' => '3',
		'headingContentSpacingMobile' => '1',
	];

	$results = Components::checkAttrResponsive('headingContentSpacing', $attributes, $manifest);

	expect($results)
		->toBeArray()
		->toHaveKey('large');

	expect($results['large'])
		->toBe('10');
});


test('Asserts that checkAttrResponsive returns empty values if attribute is not provided.', function () {
	$manifest = Components::getManifest(\dirname(__FILE__, 2) . '/data/src/Blocks/components/heading/');
	$attributes = [];

	$results = Components::checkAttrResponsive('headingContentSpacing', $attributes, $manifest);

	expect($results)
		->toBeArray()
		->toHaveKey('large');

	expect($results['large'])
		->toBe('');
});


test('Asserts that checkAttrResponsive returns null if default is not set and undefined is allowed.', function () {
	$manifest = Components::getManifest(\dirname(__FILE__, 2) . '/data/src/Blocks/components/heading/');
	$attributes = [
		'headingContentSpacingDesktop' => '2'
	];

	$results = Components::checkAttrResponsive('headingContentSpacing', $attributes, $manifest, true);

	expect($results)
		->toBeArray()
		->toHaveKey('large')
		->toHaveKey('desktop')
		->toHaveKey('tablet');

	expect($results['large'])
		->toBeNull();
	expect($results['desktop'])
		->toBe('2');
	expect($results['tablet'])
		->toBeNull();
});


test('Asserts that checkAttrResponsive throws error if responsiveAttribute key is missing', function () {
	$manifest = Components::getManifest(\dirname(__FILE__, 2) . '/data/src/Blocks/components/button/');
	$attributes = [];

	Components::checkAttrResponsive('headingContentSpacing', $attributes, $manifest, 'button');
})->throws(Exception::class, 'It looks like you are missing responsiveAttributes key in your button component manifest.');


test('Asserts that checkAttrResponsive throws error if keyName key is missing responsiveAttributes array', function () {
	$manifest = Components::getManifest(\dirname(__FILE__, 2) . '/data/src/Blocks/components/heading/');
	$attributes = [];

	Components::checkAttrResponsive('testAttribute', $attributes, $manifest, 'button');
})->throws(Exception::class, 'It looks like you are missing the testAttribute key in your manifest responsiveAttributes array.');


test('Asserts that checkAttrResponsive throws error if keyName key is missing responsiveAttributes array for blockName', function () {
	$manifest = Components::getManifest(\dirname(__FILE__, 2) . '/data/src/Blocks/custom/heading/');
	$attributes = [];

	Components::checkAttrResponsive('bla', $attributes, $manifest, 'button');
})->throws(Exception::class, 'It looks like you are missing responsiveAttributes key in your heading block manifest.');


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


test('Asserts that outputCssVariablesGlobal returns the correct CSS variables from global manifest', function () {
	$globalManifest = Components::getManifest(\dirname(__FILE__, 2) . '/data/src/Blocks');

	$output = Components::outputCssVariablesGlobal($globalManifest);

	expect($output)
		->toBeString()
		->toContain('<style>')
		->toContain(':root {')
		->toContain('--global-colors-primary: #C3151B;')
		->toContain('--global-colors-primary-values: 195 21 27;')
		->not->toContain('--button-content:');
});


test('Asserts that outputCssVariablesGlobal returns empty string if global manifest data is not provided', function () {
	$output = Components::outputCssVariablesGlobal([]);

	expect($output)
		->toBeString()
		->not->toContain('<style>');
});

test('Asserts that outputCssVariables throws exception if the global block details aren\'t set', function () {
	$manifest = Components::getManifest(\dirname(__FILE__, 2) . '/data/src/Blocks/components/variables');
	$globalManifest = Components::getManifest(\dirname(__FILE__, 2) . '/data/src/Blocks');

	$attributes = [
		'variableValue' => 'value3',
	];

	Components::outputCssVariables(
		$attributes,
		$manifest,
		'uniqueString',
		$globalManifest
	);
})->throws(InvalidBlock::class);

test('Asserts that getManifest throws exception for paths in case the global details aren\'t set', function ($path) {
	Components::getManifest($path, false);
})->throws(InvalidBlock::class)->with([
	\dirname(__FILE__, 2) . '/data/src/Blocks',
	\dirname(__FILE__, 2) . '/data/src/Blocks/wrapper',
	\dirname(__FILE__, 2) . '/data/src/Blocks/components/button',
	\dirname(__FILE__, 2) . '/data/src/Blocks/custom/button',
]);


test('Asserts that getManifest returns empty array in case of a wrong path', function () {
	$manifest = Components::getManifest(\dirname(__FILE__, 2) . '/data/src', false);

	expect($manifest)
		->toBeArray()
		->toBeEmpty();
});


test('Asserts that getSettings throws exception in case the global details aren\'t set', function () {
	Components::getSettings('component');
})->throws(InvalidBlock::class);


test('Asserts that getSettings works', function () {
	// Arrange - fill the $esBlocks global variable.
	(new BlocksExample())->getBlocksDataFullRaw();

	$settings = Components::getSettings('config', 'outputCssVariablesGlobally');

	expect($settings)
		->toBeBool()
		->toBeTrue();
});


test('getSettings parses items correctly in the the case the type is block', function () {
	// Arrange - fill the $esBlocks global variable.
	(new BlocksExample())->getBlocksDataFullRaw();

	$settings = Components::getSettings('block', 'button');

	expect($settings)
		->toBeArray()
		->toHaveKey('blockName');

	expect($settings['blockName'])
		->toBe('button');
});


test('getSettings parses items correctly in the the case the type is component', function () {
	// Arrange - fill the $esBlocks global variable.
	(new BlocksExample())->getBlocksDataFullRaw();

	$settings = Components::getSettings('component', 'button');

	expect($settings)
		->toBeArray()
		->toHaveKey('componentName');

	expect($settings['componentName'])
		->toBe('button');
});


test('getSettings throws exception when key is missing', function () {
	// Arrange - fill the $esBlocks global variable.
	(new BlocksExample())->getBlocksDataFullRaw();

	Components::getSettings('component', 'button-nonexistent');
})->throws(InvalidBlock::class);


test('getSettings throws exception when type is not block or component and the item is missing', function () {
	// Arrange - fill the $esBlocks global variable.
	(new BlocksExample())->getBlocksDataFullRaw();

	Components::getSettings('settings', 'testing');
})->throws(InvalidBlock::class);


test('getSettings throws exception when the method is called with the non existent key from the global settings array', function () {
	// Arrange - fill the $esBlocks global variable.
	(new BlocksExample())->getBlocksDataFullRaw();

	Components::getSettings('what');
})->throws(InvalidBlock::class);


test('Asserts that getAttrKey will return the key in case of the wrapper', function () {

	$attributeKey = Components::getAttrKey('wrapper', [], []);

	expect($attributeKey)
		->toBeString()
		->toBe('wrapper');
});


test('Asserts that getUnique function will return some random string', function () {

	Functions\when('wp_rand')->justReturn(mt_rand());
	$unique = Components::getUnique();

	expect($unique)
		->toBeString();
});


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


test('Asserts that getDefaultRenderAttributes function will return empty array on non iterable manifest', function () {

	$output = Components::render('button-false', [], '', true);

	expect($output)
		->toBeString();
});


test('Asserts that flattenArray will return the flattened array', function () {

	$array = Components::flattenArray(['a' => ['b', 'c' => [1, 2, 3]]]);

	expect($array)
		->toBeArray()
		->toBe(['b', 1, 2, 3]);
});


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
	$output = Components::props($headingBlock['blockName'], $attributes);

	expect($output)
		->toBeArray()
		->toHaveKey('headingAlign')
		->not->toHaveKey('typographySize');
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
	$output = Components::props('typography', $attributes);

	expect($output)
		->toBeArray()
		->toHaveKey('typographyContent')
		->not->toHaveKey('headingSize');
});


test('Asserts props will correctly build the prefix', function () {
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
	$output = Components::props('heading', $attributes);

	expect($output)
		->toBeArray()
		->toHaveKey('prefix');

	expect($output['prefix'])
		->toBe('heading');

	// Next level
	$output = Components::props('typography', $output);

	expect($output)
		->toBeArray()
		->toHaveKey('prefix');

	expect($output['prefix'])
		->toBe('headingTypography');
});


test('Asserts props will correctly leave only the the needed attributes', function () {
	$attributes = [
		'componentName' => 'mock-card',
		'mockCardHeadingTypographyContent' => 'mock heading content',
		'mockCardParagraphTypographyContent' => 'mock paragraph content',
	];

	mock('alias:EightshiftBoilerplate\Config\Config')
		->shouldReceive('getProjectPath')
		->andReturn('tests/data');

	$this->blocksExample = new BlocksExample();
	$this->blocksExample->getBlocksDataFullRaw();
	$output = Components::props('mock-card', $attributes);

	expect($output)
		->toBeArray()
		->toHaveKey('mockCardHeadingTypographyContent')
		->toHaveKey('mockCardParagraphTypographyContent');

	// Now let's pass these to mock heading
	$output = Components::props('heading', $output);

	expect($output)
		->toBeArray()
		->toHaveKey('mockCardHeadingTypographyContent')
		->not->toHaveKey('mockCardParagraphTypographyContent');

	$output = Components::props('typography', $output);

	expect($output)
		->toBeArray()
		->toHaveKey('mockCardHeadingTypographyContent');
});


test('Asserts props will correctly generate manual keys in camelCase', function () {
	$attributes = [
		'componentName' => 'mock-card',
		'mockCardHeadingContent' => 'mock heading content',
		'mockCardParagraphContent' => 'mock paragraph content',
	];

	$manual = [
		'buttonContent' => 'mock button content',
	];

	mock('alias:EightshiftBoilerplate\Config\Config')
		->shouldReceive('getProjectPath')
		->andReturn('tests/data');

	$this->blocksExample = new BlocksExample();
	$this->blocksExample->getBlocksDataFullRaw();
	$output = Components::props('mock-card', $attributes, $manual);

	expect($output)
		->toBeArray()
		->toHaveKey('mockCardButtonContent')
		->not->toHaveKey('mockCardbuttonContent');
});


test('Props will include the correct attribute in the manual case', function () {
	$attributes = [
		'componentName' => 'mock-card',
		'componentJsClass' => 'js-mock-card',
		'mockCardHeadingContent' => 'mock heading content',
		'mockCardParagraphContent' => 'mock paragraph content',
	];

	$manual = [
		'buttonContent' => 'mock button content',
		'selectorClass' => 'selector',
	];

	mock('alias:EightshiftBoilerplate\Config\Config')
		->shouldReceive('getProjectPath')
		->andReturn('tests/data');

	$this->blocksExample = new BlocksExample();
	$this->blocksExample->getBlocksDataFullRaw();
	$output = Components::props('mock-card', $attributes, $manual);

	expect($output)
		->toBeArray()
		->toHaveKey('mockCardButtonContent')
		->toHaveKey('selectorClass')
		->toHaveKey('componentJsClass');
});


test('outputCssVariables returns empty string if global breakpoints are missing', function () {
	// Arrange - fill the $esBlocks global variable.
	(new BlocksExample())->getBlocksDataFullRaw();

	$output = Components::outputCssVariables(
		[],
		[],
		'uniqueString',
		['namespace' => 'eightshift']
	);

	expect($output)
		->toBeString()
		->toBeEmpty();
});


test('outputCssVariables returns empty string if global attributes or manifest are missing', function () {
	// Arrange - fill the $esBlocks global variable.
	(new BlocksExample())->getBlocksDataFullRaw();

	$globalManifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks');

	$output = Components::outputCssVariables(
		['attribute'],
		[],
		'uniqueString',
		$globalManifest
	);

	expect($output)
		->toBeString()
		->toBeEmpty();

	$outputSecond = Components::outputCssVariables(
		[],
		['manifest'],
		'uniqueString',
		$globalManifest
	);

	expect($outputSecond)
		->toBeString()
		->toBeEmpty();
});


test('outputCssVariables returns empty string if variable keys are missing in manifest', function () {
	// Arrange - fill the $esBlocks global variable.
	(new BlocksExample())->getBlocksDataFullRaw();

	$globalManifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks');

	$output = Components::outputCssVariables(
		['attribute'],
		['manifest'],
		'uniqueString',
		$globalManifest
	);

	expect($output)
		->toBeString()
		->toBeEmpty();
});


test('outputCssVariables works when correct attributes are passed to it', function () {
	global $esBlocks;

	// Arrange - fill the $esBlocks global variable.
	(new BlocksExample())->getBlocksDataFullRaw();

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

	expect($output)
		->toBeString()
		->toBeEmpty();

	// The output is put in the global variable.
	expect($esBlocks)
		->toBeArray()
		->toHaveKey($globalManifest['namespace']);

	expect($esBlocks[$globalManifest['namespace']]['styles'])
		->toBeArray();

	expect($esBlocks[$globalManifest['namespace']]['styles'][0])
		->toBeArray()
		->toHaveKey('name')
		->toHaveKey('unique')
		->toHaveKey('variables');

	expect($esBlocks[$globalManifest['namespace']]['styles'][0]['name'])
		->toBeString()
		->toBe('variables');

	expect($esBlocks[$globalManifest['namespace']]['styles'][0]['unique'])
		->toBeString()
		->toBe('uniqueString');

	expect($esBlocks[$globalManifest['namespace']]['styles'][0]['variables'])
		->toBeArray();

	expect($esBlocks[$globalManifest['namespace']]['styles'][0]['variables'][0])
		->toBeArray()
		->toHaveKey('type')
		->toHaveKey('variable')
		->toHaveKey('value');

	expect($esBlocks[$globalManifest['namespace']]['styles'][0]['variables'][1])
		->toBeArray()
		->toHaveKey('type')
		->toHaveKey('variable')
		->toHaveKey('value');

	expect($esBlocks[$globalManifest['namespace']]['styles'][0]['variables'][1]['type'])
		->toBeString()
		->toBe('max');

	expect($esBlocks[$globalManifest['namespace']]['styles'][0]['variables'][1]['variable'])
		->toBeString()
		->toBe('--variable-value-tablet: tablet;');

	expect($esBlocks[$globalManifest['namespace']]['styles'][0]['variables'][1]['value'])
		->toBeInt()
		->toBe(991);
});


test('outputCssVariables works when correct attributes are passed to it and has a unique name', function () {
	global $esBlocks;

	// Arrange - fill the $esBlocks global variable.
	(new BlocksExample())->getBlocksDataFullRaw();

	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/variables');
	$globalManifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks');

	$attributes = [
		'variableValue' => 'value3',
	];

	$output = Components::outputCssVariables(
		$attributes,
		$manifest,
		'uniqueString',
		$globalManifest,
		'customNameAttribute'
	);

	expect($output)
		->toBeString()
		->toBeEmpty();

	// The output is put in the global variable.
	expect($esBlocks)
		->toBeArray()
		->toHaveKey($globalManifest['namespace']);

	expect($esBlocks[$globalManifest['namespace']]['styles'])
		->toBeArray();

	expect($esBlocks[$globalManifest['namespace']]['styles'][0])
		->toBeArray()
		->toHaveKey('name')
		->toHaveKey('unique')
		->toHaveKey('variables');

	expect($esBlocks[$globalManifest['namespace']]['styles'][0]['name'])
		->toBeString()
		->toBe('customNameAttribute');
});


test('outputCssVariables outputs the style tag if the outputCssVariablesGlobally is set to false', function () {
	global $esBlocks;

	// Arrange - fill the $esBlocks global variable.
	(new BlocksExample())->getBlocksDataFullRaw();

	$globalManifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks');
	$esBlocks[$globalManifest['namespace']]['config']['outputCssVariablesGlobally'] = false;

	$manifest = Components::getManifest(dirname(__FILE__, 2) . '/data/src/Blocks/components/variables');

	$attributes = [
		'variableValue' => 'value3',
	];

	$output = Components::outputCssVariables(
		$attributes,
		$manifest,
		'uniqueString',
		$globalManifest,
		'customNameAttribute'
	);

	expect($output)
		->toBeString()
		->toBeEmpty();

});

test('Check that hexToRgb returns the correct output for a valid hex code', function ($input, $output) {
	$converted = Components::hexToRgb($input);

	$this->assertIsString($converted);
	$this->assertSame($converted, $output);
})->with('hexToRgbValid');

test('Check that hexToRgb returns the fallback for an invalid hex code', function ($input, $output) {
	$converted = Components::hexToRgb($input);

	$this->assertIsString($converted);
	$this->assertSame($converted, $output);
})->with('hexToRgbInvalid');
