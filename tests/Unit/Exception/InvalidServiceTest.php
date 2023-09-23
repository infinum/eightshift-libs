<?php

namespace Tests\Unit\Exception;

use EightshiftLibs\Exception\InvalidService;

test('Checks if the fromService method will return correct response.', function () {

	$service = 'nonexistent';

	$exceptionObject = InvalidService::fromService($service);

	expect($exceptionObject)->toBeObject()
		->toBeInstanceOf(InvalidService::class)
		->toHaveProperty('message')
		->and("The service {$service} is not recognized and cannot be registered.")
		->toEqual($exceptionObject->getMessage());
});
