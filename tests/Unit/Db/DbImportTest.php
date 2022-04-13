<?php

namespace Tests\Unit\CustomPostType;

use EightshiftLibs\Db\ImportCli;
use Exception;

use function Tests\setupUnitTestMocks;
use function Tests\mock;

beforeEach(function() {
	$wpCliMock = mock('alias:WP_CLI');

	$wpCliMock
		->shouldReceive('success')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('error')
		->andReturnUsing(
            function ($errorMessage) {
                throw new Exception($errorMessage);
            }
	);

	setupUnitTestMocks();

	$this->import = new ImportCli('boilerplate');
});


test('Importing DB functionality fails if --from parameter is not specified', function () {
	$dbImport = $this->import;

	$dbImport([], []);
})->throws(Exception::class, '--from parameter is mandatory. Please provide one url key from setup.json file.');


test('Importing DB functionality fails if --to parameter is not specified', function () {
	$dbImport = $this->import;

	$dbImport([], [
		'from' => 'staging'
	]);
})->throws(Exception::class, '--to parameter is mandatory. Please provide one url key from setup.json file.');


test('Importing DB functionality fails if setup.json is missing', function () {
	$dbImport = $this->import;

	$dbImport([], [
		'from' => 'staging',
		'to' => 'production'
	]);
})->throws(Exception::class, 'setup.json is missing at this path: setup.json');
