<?php

namespace Tests\Helpers;

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

    $this->assertNotTrue($isValidXml);
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

    $this->assertEquals(['bar', 1, 2, 3], $this->mockHelper->flattenArray($array));
});
