<?php

namespace Tests\Unit\Exception;

use EightshiftLibs\Exception\ComponentException;
use stdClass;

test('Checks if the throwNotStringOrArray method functions correctly.',
	function ($argument) {
		$exceptionObject = ComponentException::throwNotStringOrArray($argument);
		$type = \gettype($argument);
		$message = "{$argument} variable is not a string or array but rather {$type}";

		expect($exceptionObject)->toBeObject()
			->toBeInstanceOf(ComponentException::class)
			->toHaveProperty('message')
			->and($message)
			->toEqual($exceptionObject->getMessage());
})
->with('exceptionArguments');

test('Checks if the throwNotStringOrArray method functions correctly with objects.',
function () {

	$object = new stdClass();
	$exceptionObject = ComponentException::throwNotStringOrArray($object);

	expect($exceptionObject)->toBeObject()
		->toBeInstanceOf(ComponentException::class)
		->toHaveProperty('message')
		->and('Object couldn\'t be converted to string. Please provide only string or array.')
		->toEqual($exceptionObject->getMessage());
});

test('Checks if throwUnableToLocateComponent method will return correct response.', function () {

	$component = 'nonexistent';
	$output = ComponentException::throwUnableToLocateComponent($component);

	expect($output)->toBeObject()
		->toBeInstanceOf(ComponentException::class)
		->toHaveProperty('message')
		->and("Unable to locate component by path: {$component}")
		->toEqual($output->getMessage());
});

test('Checks if throwUnableToLocatePartial method will return correct response.', function () {

	$path = 'nonexistent';
	$output = ComponentException::throwUnableToLocatePartial($path);

	expect($output)->toBeObject()
		->toBeInstanceOf(ComponentException::class)
		->toHaveProperty('message')
		->and("Unable to locate partial on this path: {$path}")
		->toEqual($output->getMessage());
});
