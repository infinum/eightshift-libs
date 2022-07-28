<?php

namespace Tests\Unit\CustomPostType;

use Brain\Monkey\Functions;
use EightshiftLibs\Db\ExportCli;

beforeEach(function () {
	$this->mock = new ExportCli('boilerplate');
});

afterEach(function () {
	unset($this->mock);
});

test('Exporting DB functionality fails if --skip_db parameter is not specified', function () {
	Functions\when('shell_exec')->returnArg();
	$dbExport = $this->mock;

	$dbExport([], []);

	expect(\getenv('ES_CLI_SUCCESS_HAPPENED'))->toContain('Export complete!');
});
