<?php

namespace Tests\Unit\Helpers;

use Brain\Monkey\Functions;
use EightshiftLibs\Exception\InvalidManifest;
use EightshiftLibs\Helpers\Components;
use EightshiftLibs\Helpers\ObjectHelperTrait;


class MockObjectHelper {
	use ObjectHelperTrait;
}

beforeEach(function () {
	$this->mockHelper = new MockObjectHelper();
});

// ------------------------------------------
// isValidXml
// ------------------------------------------

test('Test valid XML checker helper with valid XML', function () {
		$validXml = '<?xml version="1.0" encoding="UTF-8"?><note><to>Tove</to><from>Jani</from><heading>Reminder</heading><body>Do not forget me this weekend!</body></note>';

		$isValidXml = $this->mockHelper->isValidXml($validXml);

		$this->assertTrue($isValidXml);
});


test('Test valid XML checker helper with invalid XML', function () {
	$invalidXml = '<?xml version="1.0" encoding="UTF-8"?><note><to>Tove</to><from>Jani</Ffrom><heading>Reminder</heading><body>Do not forget me this weekend!</body></note>';

		$isValidXml = $this->mockHelper->isValidXml($invalidXml);

		$this->assertFalse($isValidXml);
});

test('Throws type exception if wrong argument type is passed to isValidXml', function () {
	$this->mockHelper->isValidXml(['test']);
})->throws(\TypeError::class);

// ------------------------------------------
// isJson
// ------------------------------------------

test('Test JSON validator with valid JSON', function () {
	$validJson = '{"name":"John", "age":30, "car":null}';

	$isValidJson = $this->mockHelper->isJson($validJson);

	$this->assertTrue($isValidJson);
});

test('Test JSON validator with invalid JSON', function () {
	$invalidJson = '["test": 123]';

	$isInvalidJson = $this->mockHelper->isJson($invalidJson);

	$this->assertNotTrue($isInvalidJson);
});

test('Throws type exception if wrong argument type is passed to isJson', function () {
		$this->mockHelper->isJson(['test']);
})->throws(\TypeError::class);

// ------------------------------------------
// flattenArray
// ------------------------------------------


// ------------------------------------------
// flattenArray
// ------------------------------------------

test('Asserts that flattenArray will return the flattened array', function () {

	$array = Components::flattenArray(['a' => ['b', 'c' => [1, 2, 3]]]);

	expect($array)
		->toBeArray()
		->toBe(['b', 1, 2, 3]);
});

// ------------------------------------------
// sanitizeArray
// ------------------------------------------

test('Sanitization of array works', function() {
	$array = [
		'foo' => 'bar',
		'baz' => [
			1, 2, 3
		]
	];

	/**
	 * Mock sanitization function so that it returns a specific output.
	 * That way we'll know if it's working.
	 */
	Functions\when('sanitize_text_field')->alias(function (string $str) {
		return "{$str} is sanitized!";
	});

	$sanitizedArray = $this->mockHelper->sanitizeArray($array, 'sanitize_text_field');

	$expectedSanitizedArray = [
		'foo' => 'bar is sanitized!',
		'baz' => [
			'1 is sanitized!', '2 is sanitized!', '3 is sanitized!'
		]
	];

	$this->assertSame($expectedSanitizedArray, $sanitizedArray);
});

// ------------------------------------------
// sortArrayByOrderKey
// ------------------------------------------

test('Test that sorting helper works', function() {
	$arrayToSort = [
		['name' => 'Anne', 'order' => 2],
		['name' => 'Mike', 'order' => 3],
		['name' => 'Bob', 'order' => 1],
	];

	$orderedArray = $this->mockHelper->sortArrayByOrderKey($arrayToSort);

	$expectedArray = [
		['name' => 'Bob', 'order' => 1],
		['name' => 'Anne', 'order' => 2],
		['name' => 'Mike', 'order' => 3],
	];

	$this->assertSame($expectedArray, $orderedArray);
});

// ------------------------------------------
// camelToKebabCase
// ------------------------------------------

test('Return correct case - camelCase to kebab-case', function ($input, $output) {
	$case = Components::camelToKebabCase($input);

	$this->assertIsString($case);
	$this->assertSame($case, $output);
})->with('camelToKebabCaseCheckCorrect');

test('Return wrong case - camelCase to kebab-case', function ($input, $output) {
	$case = Components::camelToKebabCase($input);

	$this->assertIsString($case);
	$this->assertNotSame($case, $output);
})->with('camelToKebabCaseCheckWrong');

// ------------------------------------------
// kebabToCamelCase
// ------------------------------------------

test('Return correct case - kebab-case to camelCase', function ($input, $output) {
	$case = Components::kebabToCamelCase($input);

	$this->assertIsString($case);
	$this->assertSame($case, $output);
})->with('kebabToCamelCaseCheckCorrect');

test('Return wrong case - kebab-case to camelCase', function ($input, $output) {
	$case = Components::kebabToCamelCase($input);

	$this->assertIsString($case);
	$this->assertNotSame($case, $output);
})->with('kebabToCamelCaseCheckWrong');

// ------------------------------------------
// arrayIsList
// ------------------------------------------

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

// ------------------------------------------
// parseManifest
// ------------------------------------------

test('Asserts that "parseManifest" function will return array is manifest returns no errors.', function () {
	expect(Components::parseManifest('{"a": "b"}'))->toBeArray()->toEqual(["a" => "b"]);
});

test('Asserts that "parseManifest" function will throw error if manifest is broken by JSON_ERROR_SYNTAX.', function () {
	Components::parseManifest('afasf');
})->throws(InvalidManifest::class, 'Syntax error, malformed JSON.');

test('Asserts that "parseManifest" function will throw error if manifest is broken by JSON_ERROR_STATE_MISMATCH.', function () {
	Components::parseManifest('{"j": 1 ] }');
})->throws(InvalidManifest::class, 'Invalid or malformed JSON.');
