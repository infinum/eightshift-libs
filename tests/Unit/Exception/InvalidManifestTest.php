<?php

namespace Tests\Unit\Exception;

use EightshiftLibs\Exception\InvalidManifest;

test('Checks if the missingManifestItemException method will return correct response.', function () {

	$key = 'randomKey';

	$exceptionObject = InvalidManifest::missingManifestItemException($key);

	$this->assertIsObject($exceptionObject, "The {$exceptionObject} should be an instance of InvalidManifest class");
	$this->assertObjectHasAttribute('message', $exceptionObject, "Object doesn't contain message attribute");
	$this->assertSame("{$key} key does not exist in manifest.json. Please check if provided key is correct.", $exceptionObject->getMessage(), "Strings for message if manifest key is missing do not match!");
});

test('Checks if the missingManifestException method will return correct response.', function () {

	$manifestPath = 'some/random/path';

	$exceptionObject = InvalidManifest::missingManifestException($manifestPath);

	$this->assertIsObject($exceptionObject, "The {$exceptionObject} should be an instance of InvalidManifest class");
	$this->assertObjectHasAttribute('message', $exceptionObject, "Object doesn't contain message attribute");
	$this->assertSame("manifest.json is missing at this path: {$manifestPath}. Bundle the theme before using it. Or your bundling process is returning an error.", $exceptionObject->getMessage(), "Strings for message if manifest is missing do not match!");
});

test('Checks if the manifestStructureException method will return correct response.', function () {

	$errorMessage = 'Some error message';

	$exceptionObject = InvalidManifest::manifestStructureException($errorMessage);

	$this->assertIsObject($exceptionObject, "The {$exceptionObject} should be an instance of InvalidManifest class");
	$this->assertObjectHasAttribute('message', $exceptionObject, "Object doesn't contain message attribute");
	$this->assertSame($errorMessage, $exceptionObject->getMessage(), "Strings for manifest structure error message do not match!");
});
