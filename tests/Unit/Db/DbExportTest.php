<?php

namespace Tests\Unit\CustomPostType;

use EightshiftLibs\Db\ExportCli;
use Brain\Monkey\Functions;
use Exception;

use function Tests\setupUnitTestMocks;
use function Tests\mock;

beforeEach(function() {
	$wpCliMock = mock('alias:WP_CLI');

	$wpCliMock
		->shouldReceive('success')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('runcommand')
		->andReturn(putenv("INIT_CALLED=true"));

	$wpCliMock
		->shouldReceive('log')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('error')
		->andReturnUsing(
            function ($errorMessage) {
                throw new Exception($errorMessage);
            }
	);

	setupUnitTestMocks();

	$this->export = new ExportCli('boilerplate');
});


test('Exporting DB functionality fails if --skip_db parameter is not specified', function () {
	Functions\when('shell_exec')->returnArg();
	$dbExport = $this->export;

	$dbExport([], []);

	$this->assertSame('true', \getenv('INIT_CALLED'));
});
