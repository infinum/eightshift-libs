<?php

namespace Tests\Unit\Helpers;

use EightshiftBoilerplate\Blocks\BlocksExample;
use EightshiftLibs\Helpers\Helpers;
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
	$manifest = Helpers::getManifestByDir(Helpers::getProjectPaths('blocksDestinationComponents', 'variables'));
	$attributes['buttonAlign'] = 'right';

	$results = Helpers::checkAttr('buttonAlign', $attributes, $manifest);

	expect($results)
		->toBeString()
		->toBe('right');
});

test('checkAttr will throw an exception in the case that the block name is missing in the manifest', function () {
	$manifest = Helpers::getManifestByDir(Helpers::getProjectPaths('blocksDestinationCustom', 'button'));
	$attributes['buttonText'] = 'left';

	Helpers::checkAttr('buttonAlign', $attributes, $manifest);
})->throws(Exception::class, 'buttonAlign key does not exist in the button block manifest. Please check your implementation. If you are using additional components, check if you used the correct block/component prefix in your attribute name.');


test('Asserts that checkAttr works in case attribute is boolean', function () {
	$manifest = Helpers::getManifestByDir(Helpers::getProjectPaths('blocksDestinationComponents', 'button'));
	$attributes['buttonIsAnchor'] = true;

	$results = Helpers::checkAttr('buttonIsAnchor', $attributes, $manifest);

	expect($results)
		->toBeBool()
		->toBeTrue();
});

test('Asserts that checkAttr returns false in case attribute is boolean and default is not set', function () {
	$manifest = Helpers::getManifestByDir(Helpers::getProjectPaths('blocksDestinationComponents', 'button'));
	$attributes['buttonIsAnchor'] = true;

	$results = Helpers::checkAttr('buttonIsNewTab', $attributes, $manifest);

	expect($results)
		->toBeBool()
		->toBeFalse();
});

test('Asserts that checkAttr returns null in case attribute is boolean, default is not set and undefined is allowed', function () {
	$manifest = Helpers::getManifestByDir(Helpers::getProjectPaths('blocksDestinationComponents', 'button'));
	$attributes['buttonIsAnchor'] = true;

	$results = Helpers::checkAttr('buttonIsNewTab', $attributes, $manifest, true);

	expect($results)
		->not->toBeBool()
		->toBeNull();
});

test('Asserts that checkAttr works in case attribute is array', function () {
	$manifest = Helpers::getManifestByDir(Helpers::getProjectPaths('blocksDestinationComponents', 'button'));
	$attributes['buttonAttrs'] = ['attr 1', 'attr 2'];

	$results = Helpers::checkAttr('buttonAttrs', $attributes, $manifest);

	expect($results)
		->toBeArray();

	expect($results[0])
		->toBe('attr 1');
	expect($results[1])
		->toBe('attr 2');
});

test('Asserts that checkAttr returns empty array in case attribute is array or object and default is not set', function () {
	$manifest = Helpers::getManifestByDir(Helpers::getProjectPaths('blocksDestinationComponents', 'button'));
	$attributes['buttonSize'] = 'large';

	$results = Helpers::checkAttr('buttonAttrs', $attributes, $manifest);


	expect($results)
		->toBeArray()
		->toBe([]);
});

test('Asserts that checkAttr returns null in case attribute is array or object, default is not set, and undefined is allowed', function () {
	$manifest = Helpers::getManifestByDir(Helpers::getProjectPaths('blocksDestinationComponents', 'button'));
	$attributes['buttonSize'] = 'large';

	$results = Helpers::checkAttr('buttonAttrs', $attributes, $manifest, true);

	expect($results)
		->not->toBeArray()
		->toBeNull();
});

test('Asserts that checkAttr returns default value', function () {
	$manifest = Helpers::getManifestByDir(Helpers::getProjectPaths('blocksDestinationComponents', 'button'));
	$attributes['title'] = 'Some attribute';

	$results = Helpers::checkAttr('buttonAlign', $attributes, $manifest, 'button');

	expect($results)
		->toBeString()
		->toBe('left');
});

test('Asserts that checkAttr throws exception if manifest key is not set', function () {
	$manifest = Helpers::getManifestByDir(Helpers::getProjectPaths('blocksDestinationComponents', 'button'));
	$attributes['title'] = 'Some attribute';

	Helpers::checkAttr('bla', $attributes, $manifest, 'button');
})->throws(Exception::class, "bla key does not exist in the button component manifest. Please check your implementation.");

test('Asserts that checkAttr returns attribute based on prefix if set', function () {
	$manifest = Helpers::getManifestByDir(Helpers::getProjectPaths('blocksDestinationComponents', 'button'));
	$attributes = [
		'prefix' => 'prefixedMultipleTimesButton',
		'prefixedMultipleTimesButtonAlign' => 'right'
	];

	$results = Helpers::checkAttr('buttonAlign', $attributes, $manifest);

	expect($results)
		->toBeString()
		->toBe('right');
});

// ------------------------------------------
// checkAttrResponsive
// ------------------------------------------

test('Asserts that checkAttrResponsive returns the correct output.', function () {
	$manifest = Helpers::getManifestByDir(Helpers::getProjectPaths('blocksDestinationComponents', 'heading'));
	$attributes = [
		'headingContentSpacingLarge' => '10',
		'headingContentSpacingDesktop' => '5',
		'headingContentSpacingTablet' => '3',
		'headingContentSpacingMobile' => '1',
	];

	$results = Helpers::checkAttrResponsive('headingContentSpacing', $attributes, $manifest);

	expect($results)
		->toBeArray()
		->toHaveKey('large');

	expect($results['large'])
		->toBe('10');
});

test('Asserts that checkAttrResponsive returns empty values if attribute is not provided.', function () {
	$manifest = Helpers::getManifestByDir(Helpers::getProjectPaths('blocksDestinationComponents', 'heading'));
	$attributes = [];

	$results = Helpers::checkAttrResponsive('headingContentSpacing', $attributes, $manifest);

	expect($results)
		->toBeArray()
		->toHaveKey('large');

	expect($results['large'])
		->toBe('');
});

test('Asserts that checkAttrResponsive returns null if default is not set and undefined is allowed.', function () {
	$manifest = Helpers::getManifestByDir(Helpers::getProjectPaths('blocksDestinationComponents', 'heading'));
	$attributes = [
		'headingContentSpacingDesktop' => '2'
	];

	$results = Helpers::checkAttrResponsive('headingContentSpacing', $attributes, $manifest, true);

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
	$manifest = Helpers::getManifestByDir(Helpers::getProjectPaths('blocksDestinationComponents', 'button'));
	$attributes = [];

	Helpers::checkAttrResponsive('headingContentSpacing', $attributes, $manifest, 'button');
})->throws(Exception::class, 'It looks like you are missing responsiveAttributes key in your button component manifest.');

test('Asserts that checkAttrResponsive throws error if keyName key is missing responsiveAttributes array', function () {
	$manifest = Helpers::getManifestByDir(Helpers::getProjectPaths('blocksDestinationComponents', 'heading'));
	$attributes = [];

	Helpers::checkAttrResponsive('testAttribute', $attributes, $manifest, 'button');
})->throws(Exception::class, 'It looks like you are missing the testAttribute key in your manifest responsiveAttributes array.');

test('Asserts that checkAttrResponsive throws error if keyName key is missing responsiveAttributes array for blockName', function () {
	$manifest = Helpers::getManifestByDir(Helpers::getProjectPaths('blocksDestinationCustom', 'heading'));
	$attributes = [];

	Helpers::checkAttrResponsive('bla', $attributes, $manifest, 'button');
})->throws(Exception::class, 'It looks like you are missing responsiveAttributes key in your heading block manifest.');

// ------------------------------------------
// getAttrKey
// ------------------------------------------

test('Asserts that getAttrKey will return the key in case of the wrapper', function () {

	$attributeKey = Helpers::getAttrKey('wrapper', [], []);

	expect($attributeKey)
		->toBeString()
		->toBe('wrapper');
});

// ------------------------------------------
// props
// ------------------------------------------

test('Asserts props for heading block will return only heading attributes', function () {
	$headingBlock = Helpers::getManifestByDir(Helpers::getProjectPaths('blocksDestinationCustom', 'heading'));
	$headingComponent = Helpers::getManifestByDir(Helpers::getProjectPaths('blocksDestinationComponents', 'heading'));
	$typographyComponent = Helpers::getManifestByDir(Helpers::getProjectPaths('blocksDestinationComponents', 'typography'));

	$attributes = array_merge(
		$headingBlock['attributes'],
		$headingComponent['attributes'],
		$typographyComponent['attributes']
	);

	mock('alias:EightshiftBoilerplate\Config\Config')
		->shouldReceive('getProjectPath')
		->andReturn('tests/data');

	buildTestBlocks();
	$output = Helpers::props($headingBlock['blockName'], $attributes);

	expect($output)
		->toBeArray()
		->toHaveKey('headingAlign')
		->not->toHaveKey('typographySize');
});

test('Asserts props for heading component will return only typography attributes', function () {
	$headingBlock = Helpers::getManifestByDir(Helpers::getProjectPaths('blocksDestinationCustom', 'heading'));
	$headingComponent = Helpers::getManifestByDir(Helpers::getProjectPaths('blocksDestinationComponents', 'heading'));
	$typographyComponent = Helpers::getManifestByDir(Helpers::getProjectPaths('blocksDestinationComponents', 'typography'));

	$attributes = array_merge(
		$headingBlock['attributes'],
		$headingComponent['attributes'],
		$typographyComponent['attributes']
	);

	mock('alias:EightshiftBoilerplate\Config\Config')
		->shouldReceive('getProjectPath')
		->andReturn('tests/data');

	buildTestBlocks();
	$output = Helpers::props('typography', $attributes);

	expect($output)
		->toBeArray()
		->toHaveKey('typographyContent')
		->not->toHaveKey('headingSize');
});

test('Asserts props will correctly build the prefix', function () {
	$headingBlock = Helpers::getManifestByDir(Helpers::getProjectPaths('blocksDestinationCustom', 'heading'));
	$headingComponent = Helpers::getManifestByDir(Helpers::getProjectPaths('blocksDestinationComponents', 'heading'));
	$typographyComponent = Helpers::getManifestByDir(Helpers::getProjectPaths('blocksDestinationComponents', 'typography'));

	$attributes = array_merge(
		$headingBlock['attributes'],
		$headingComponent['attributes'],
		$typographyComponent['attributes']
	);

	mock('alias:EightshiftBoilerplate\Config\Config')
		->shouldReceive('getProjectPath')
		->andReturn('tests/data');

	buildTestBlocks();
	$output = Helpers::props('heading', $attributes);

	expect($output)
		->toBeArray()
		->toHaveKey('prefix');

	expect($output['prefix'])
		->toBe('heading');

	// Next level
	$output = Helpers::props('typography', $output);

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

	buildTestBlocks();
	$output = Helpers::props('mock-card', $attributes);

	expect($output)
		->toBeArray()
		->toHaveKey('mockCardHeadingTypographyContent')
		->toHaveKey('mockCardParagraphTypographyContent');

	// Now let's pass these to mock heading
	$output = Helpers::props('heading', $output);

	expect($output)
		->toBeArray()
		->toHaveKey('mockCardHeadingTypographyContent')
		->not->toHaveKey('mockCardParagraphTypographyContent');

	$output = Helpers::props('typography', $output);

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

	buildTestBlocks();
	$output = Helpers::props('mock-card', $attributes, $manual);

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

	buildTestBlocks();
	$output = Helpers::props('mock-card', $attributes, $manual);

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

	$output = Helpers::render('button-false', [], '', true);

	expect($output)
		->toBeString();
});
