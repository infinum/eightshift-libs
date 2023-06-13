<?php

namespace Tests\Unit\Exception;

use EightshiftLibs\Exception\InvalidCallback;

test('Checks if the fromCallback method will return correct response.', function () {

	$callback = 'randomText';

	$exceptionObject = InvalidCallback::fromCallback($callback);

	$this->assertIsObject($exceptionObject, "The {$exceptionObject} should be an instance of InvalidBlock class");
	$this->assertObjectHasAttribute('message', $exceptionObject, "Object doesn't contain message attribute");
	$this->assertSame("The callback {$callback} is not recognized and cannot be registered.", $exceptionObject->getMessage(), "Strings for message if callback isn't recognised do not match!");
});
