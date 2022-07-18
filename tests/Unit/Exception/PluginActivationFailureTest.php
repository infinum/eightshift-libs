<?php

namespace Tests\Unit\Exception;

use EightshiftLibs\Exception\PluginActivationFailure;

test('Checks if the activationMessage method will return correct response.', function () {

	$message = 'Error message';

	$exceptionObject = PluginActivationFailure::activationMessage($message);

	$this->assertIsObject($exceptionObject, "The {$exceptionObject} should be an instance of PluginActivationFailure class");
	$this->assertObjectHasAttribute('message', $exceptionObject, "Object doesn't contain message attribute");
	$this->assertSame($message, $exceptionObject->getMessage(), "Strings for error activation message do not match!");
});
