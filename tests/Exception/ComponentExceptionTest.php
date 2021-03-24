<?php

namespace Tests\Unit\Exception;

use Brain\Monkey;
use Brain\Monkey\Functions;

use EightshiftLibs\Exception\ComponentException;
use stdClass;

use function Tests\setupMocks;

beforeAll(function () {
	setupMocks();
});

afterEach(function() {
	Monkey\tearDown();
});

test('Throws exception if ensure_string argument is invalid.', function () {

	$object = new stdClass();
	$integer = 7;
	$null = null;
	$bool = true;

	$exceptionObject = ComponentException::throwNotStringOrArray($object);
	$exceptionInteger = ComponentException::throwNotStringOrArray($integer);
	$exceptionNull = ComponentException::throwNotStringOrArray($null);
	$exceptionBool = ComponentException::throwNotStringOrArray($bool);

	$this->assertIsObject($exceptionObject);
	$this->assertIsObject($exceptionInteger);
	$this->assertIsObject($exceptionNull);
	$this->assertIsObject($exceptionBool);
	
	$this->assertObjectHasAttribute('message', $exceptionObject);
	$this->assertObjectHasAttribute('message', $exceptionInteger);
	$this->assertObjectHasAttribute('message', $exceptionNull);
	$this->assertObjectHasAttribute('message', $exceptionBool);

	$this->assertStringContainsString('Object couldn&#039;t be converted to string. Please provide only string or array.', $exceptionObject->getMessage());
	$this->assertStringContainsString("{$integer} variable is not a string or array but rather integer", $exceptionInteger->getMessage());
	$this->assertStringContainsString("{$null} variable is not a string or array but rather NULL", $exceptionNull->getMessage());
	$this->assertStringContainsString("{$bool} variable is not a string or array but rather boolean", $exceptionBool->getMessage());
});

/*test('Throws exception if unable to locate component.', function () {

	$component = 'nonexistent';
	$output = ComponentException::throwUnableToLocateComponent($component);

	$this->assertStringContainsString("Unable to locate component by path: {$component}", $output->getMessage());
});*/