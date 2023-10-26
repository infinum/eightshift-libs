<?php

namespace Tests\Unit\Exception;

use EightshiftLibs\Exception\InvalidManifest;

test('Checks if the missingManifestItemException method will return correct response.', function () {

	$key = 'randomKey';

	$exceptionObject = InvalidManifest::missingManifestItemException($key);

	expect($exceptionObject)->toBeObject()
		->toBeInstanceOf(InvalidManifest::class)
		->toHaveProperty('message')
		->and("{$key} key does not exist in manifest.json. Please check if provided key is correct.")
		->toEqual($exceptionObject->getMessage());
});

test('Checks if the missingManifestException method will return correct response.', function () {

	$manifestPath = 'some/random/path';

	$exceptionObject = InvalidManifest::missingManifestException($manifestPath);

	expect($exceptionObject)->toBeObject()
		->toBeInstanceOf(InvalidManifest::class)
		->toHaveProperty('message')
		->and("manifest.json is missing at this path: {$manifestPath}. Bundle the theme before using it. Or your bundling process is returning an error.")
		->toEqual($exceptionObject->getMessage());
});

test('Checks if the manifestStructureException method will return correct response.', function () {

	$errorMessage = 'Some error message';

	$exceptionObject = InvalidManifest::manifestStructureException($errorMessage);

	expect($exceptionObject)->toBeObject()
		->toBeInstanceOf(InvalidManifest::class)
		->toHaveProperty('message')
		->and($errorMessage)
		->toEqual($exceptionObject->getMessage());
});
