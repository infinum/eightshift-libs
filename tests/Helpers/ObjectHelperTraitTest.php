<?php

namespace Tests\Unit\Helpers;

use Brain\Monkey\Functions;
use EightshiftLibs\Helpers\Components;
use EightshiftLibs\Helpers\ObjectHelperTrait;

class MockObjectHelper {
	use ObjectHelperTrait;
}

beforeEach(function () {
	$this->mockHelper = new MockObjectHelper();
});


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


test('Throws type exception if wrong argument type is passed to isValidXml',
	function () {
		$this->mockHelper->isValidXml(['test']);
	})
	->throws(\TypeError::class);


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


test('Throws type exception if wrong argument type is passed to isJson',
	function () {
		$this->mockHelper->isJson(['test']);
	})
	->throws(\TypeError::class);


test('Test That multidimensional array is flattened', function () {
	$array = [
		'foo' => 'bar',
		'baz' => [
			1, 2, 3
		]
	];

    $this->assertSame(['bar', 1, 2, 3], $this->mockHelper->flattenArray($array));
});

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

test('Asserts that flattenArray will return the flattened array', function () {

	$array = Components::flattenArray(['a' => ['b', 'c' => [1, 2, 3]]]);

	expect($array)
		->toBeArray()
		->toBe(['b', 1, 2, 3]);
});
