<?php

namespace Tests\Unit\CustomPostType;

use EightshiftLibs\Db\ExportCli;

use function Tests\setupMocks;

beforeEach(function() {
	$wpCliMock = \Mockery::mock('alias:WP_CLI');

	$wpCliMock
		->shouldReceive('success')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('runcommand')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('log')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('error')
		->andReturnUsing(
            function ($errorMessage) {
                throw new \Exception($errorMessage);
            }
	);

	setupMocks();

	$this->export = new ExportCli('boilerplate');
});


test('Exporting DB functionality fails if --skip_db parameter is not specified', function () {
	$dbExport = $this->export;

	$dbExport([], []);
});
