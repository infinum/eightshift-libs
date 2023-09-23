<?php

namespace Tests\Unit\Exception;

use EightshiftLibs\Exception\InvalidCallback;

test('Checks if the fromCallback method will return correct response.', function () {

	$callback = 'randomText';

	$exceptionObject = InvalidCallback::fromCallback($callback);

	expect($exceptionObject)->toBeObject()
		->toBeInstanceOf(InvalidCallback::class)
		->toHaveProperty('message')
		->and("The callback {$callback} is not recognized and cannot be registered.")
		->toEqual($exceptionObject->getMessage());
});
