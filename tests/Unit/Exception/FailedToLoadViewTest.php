<?php

namespace Tests\Unit\Exception;

use EightshiftLibs\Exception\FailedToLoadView;
use Exception;

test('Checks if the viewException method will return correct response.', function () {

	$uri = 'https://random.uri';
	$exception = new Exception('Error message');

	$exceptionObject = FailedToLoadView::viewException($uri, $exception);
	$message = "Could not load the View URI: {$uri}. Reason: {$exception->getMessage()}.";

	expect($exceptionObject)->toBeObject()
		->toBeInstanceOf(FailedToLoadView::class)
		->toHaveProperty('message')
		->and($message)
		->toEqual($exceptionObject->getMessage());
});
