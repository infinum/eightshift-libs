<?php

namespace Tests\Unit\Exception;

use EightshiftLibs\Exception\PluginActivationFailure;

test('Checks if the activationMessage method will return correct response.', function () {

	$message = 'Error message';

	$exceptionObject = PluginActivationFailure::activationMessage($message);

	expect($exceptionObject)->toBeObject()
		->toBeInstanceOf(PluginActivationFailure::class)
		->toHaveProperty('message')
		->and($message)
		->toEqual($exceptionObject->getMessage());
});
