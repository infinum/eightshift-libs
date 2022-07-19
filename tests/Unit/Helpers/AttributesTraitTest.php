<?php

namespace Tests\Unit\Helpers;

use EightshiftBoilerplate\Blocks\BlocksExample;
use EightshiftLibs\Helpers\Components;
use Exception;

use function Tests\buildTestBlocks;
use function Tests\mock;

beforeEach(function () {
	buildTestBlocks();
});

// ------------------------------------------
// checkAttr
// ------------------------------------------

test('Asserts that checkAttr works in case attribute is string', function () {
	$manifest = Components::getManifest(Components::getProjectPaths('blocksDestinationComponents', 'variables'));
	$attributes['buttonAlign'] = 'right';

	$results = Components::checkAttr('buttonAlign', $attributes, $manifest);

	expect($results)
		->toBeString()
		->toBe('right');
});

test('checkAttr will throw an exception in the case that the block name is missing in the manifest', function () {
	$manifest = Components::getManifest(Components::getProjectPaths('blocksDestinationCustom', 'button'));
	$attributes['buttonText'] = 'left';

	Components::checkAttr('buttonAlign', $attributes, $manifest);
})->throws(Exception::class, 'buttonAlign key does not exist in the button block manifest. Please check your implementation.');


test('Asserts that checkAttr works in case attribute is boolean', function () {
	$manifest = Components::getManifest(Components::getProjectPaths('blocksDestinationComponents', 'button'));
	$attributes['buttonIsAnchor'] = true;

	$results = Components::checkAttr('buttonIsAnchor', $attributes, $manifest);

	expect($results)
		->toBeBool()
		->toBeTrue();
});

test('Asserts that checkAttr returns false in case attribute is boolean and default is not set', function () {
	$manifest = Components::getManifest(Components::getProjectPaths('blocksDestinationComponents', 'button'));
	$attributes['buttonIsAnchor'] = true;

	$results = Components::checkAttr('buttonIsNewTab', $attributes, $manifest);

	expect($results)
		->toBeBool()
		->toBeFalse();
});

test('Asserts that checkAttr returns null in case attribute is boolean, default is not set and undefined is allowed', function () {
	$manifest = Components::getManifest(Components::getProjectPaths('blocksDestinationComponents', 'button'));
	$attributes['buttonIsAnchor'] = true;

	$results = Components::checkAttr('buttonIsNewTab', $attributes, $manifest, true);

	expect($results)
		->not->toBeBool()
		->toBeNull();
});

test('Asserts that checkAttr works in case attribute is array', function () {
	$manifest = Components::getManifest(Components::getProjectPaths('blocksDestinationComponents', 'button'));
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
	$manifest = Components::getManifest(Components::getProjectPaths('blocksDestinationComponents', 'button'));
	$attributes['buttonSize'] = 'large';

	$results = Components::checkAttr('buttonAttrs', $attributes, $manifest);


	expect($results)
		->toBeArray()
		->toBe([]);
});

test('Asserts that checkAttr returns null in case attribute is array or object, default is not set, and undefined is allowed', function () {
	$manifest = Components::getManifest(Components::getProjectPaths('blocksDestinationComponents', 'button'));
	$attributes['buttonSize'] = 'large';

	$results = Components::checkAttr('buttonAttrs', $attributes, $manifest, true);

	expect($results)
		->not->toBeArray()
		->toBeNull();
});

test('Asserts that checkAttr returns default value', function () {
	$manifest = Components::getManifest(Components::getProjectPaths('blocksDestinationComponents', 'button'));
	$attributes['title'] = 'Some attribute';

	$results = Components::checkAttr('buttonAlign', $attributes, $manifest, 'button');

	expect($results)
		->toBeString()
		->toBe('left');
});

test('Asserts that checkAttr throws exception if manifest key is not set', function () {
	$manifest = Components::getManifest(Components::getProjectPaths('blocksDestinationComponents', 'button'));
	$attributes['title'] = 'Some attribute';

	Components::checkAttr('bla', $attributes, $manifest, 'button');
})->throws(Exception::class, "bla key does not exist in the button component manifest. Please check your implementation.");

test('Asserts that checkAttr returns attribute based on prefix if set', function () {
	$manifest = Components::getManifest(Components::getProjectPaths('blocksDestinationComponents', 'button'));
	$attributes = [
		'prefix' => 'prefixedMultipleTimesButton',
		'prefixedMultipleTimesButtonAlign' => 'right'
	];

	$results = Components::checkAttr('buttonAlign', $attributes, $manifest);

	expect($results)
		->toBeString()
		->toBe('right');
});

// ------------------------------------------
// checkAttrResponsive
// ------------------------------------------

test('Asserts that checkAttrResponsive returns the correct output.', function () {
	$manifest = Components::getManifest(Components::getProjectPaths('blocksDestinationComponents', 'heading'));
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
	$manifest = Components::getManifest(Components::getProjectPaths('blocksDestinationComponents', 'heading'));
	$attributes = [];

	$results = Components::checkAttrResponsive('headingContentSpacing', $attributes, $manifest);

	expect($results)
		->toBeArray()
		->toHaveKey('large');

	expect($results['large'])
		->toBe('');
});

test('Asserts that checkAttrResponsive returns null if default is not set and undefined is allowed.', function () {
	$manifest = Components::getManifest(Components::getProjectPaths('blocksDestinationComponents', 'heading'));
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
	$manifest = Components::getManifest(Components::getProjectPaths('blocksDestinationComponents', 'button'));
	$attributes = [];

	Components::checkAttrResponsive('headingContentSpacing', $attributes, $manifest, 'button');
})->throws(Exception::class, 'It looks like you are missing responsiveAttributes key in your button component manifest.');

test('Asserts that checkAttrResponsive throws error if keyName key is missing responsiveAttributes array', function () {
	$manifest = Components::getManifest(Components::getProjectPaths('blocksDestinationComponents', 'heading'));
	$attributes = [];

	Components::checkAttrResponsive('testAttribute', $attributes, $manifest, 'button');
})->throws(Exception::class, 'It looks like you are missing the testAttribute key in your manifest responsiveAttributes array.');

test('Asserts that checkAttrResponsive throws error if keyName key is missing responsiveAttributes array for blockName', function () {
	$manifest = Components::getManifest(Components::getProjectPaths('blocksDestinationCustom', 'heading'));
	$attributes = [];

	Components::checkAttrResponsive('bla', $attributes, $manifest, 'button');
})->throws(Exception::class, 'It looks like you are missing responsiveAttributes key in your heading block manifest.');

// ------------------------------------------
// getAttrKey
// ------------------------------------------

test('Asserts that getAttrKey will return the key in case of the wrapper', function () {

	$attributeKey = Components::getAttrKey('wrapper', [], []);

	expect($attributeKey)
		->toBeString()
		->toBe('wrapper');
});

// ------------------------------------------
// props
// ------------------------------------------

test('Asserts props for heading block will return only heading attributes', function () {
	$headingBlock = Components::getManifest(Components::getProjectPaths('blocksDestinationCustom', 'heading'));
	$headingComponent = Components::getManifest(Components::getProjectPaths('blocksDestinationComponents', 'heading'));
	$typographyComponent = Components::getManifest(Components::getProjectPaths('blocksDestinationComponents', 'typography'));

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
	$headingBlock = Components::getManifest(Components::getProjectPaths('blocksDestinationCustom', 'heading'));
	$headingComponent = Components::getManifest(Components::getProjectPaths('blocksDestinationComponents', 'heading'));
	$typographyComponent = Components::getManifest(Components::getProjectPaths('blocksDestinationComponents', 'typography'));

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
	$headingBlock = Components::getManifest(Components::getProjectPaths('blocksDestinationCustom', 'heading'));
	$headingComponent = Components::getManifest(Components::getProjectPaths('blocksDestinationComponents', 'heading'));
	$typographyComponent = Components::getManifest(Components::getProjectPaths('blocksDestinationComponents', 'typography'));

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

// ------------------------------------------
// getDefaultRenderAttributes
// ------------------------------------------

test('Asserts that getDefaultRenderAttributes function will return empty array on non iterable manifest', function () {

	$output = Components::render('button-false', [], '', true);

	expect($output)
		->toBeString();
});
