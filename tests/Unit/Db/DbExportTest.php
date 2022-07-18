<?php

namespace Tests\Unit\CustomPostType;

use EightshiftLibs\Db\ExportCli;
use Brain\Monkey\Functions;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new ExportCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});

test('Exporting DB functionality fails if --skip_db parameter is not specified', function () {
	Functions\when('shell_exec')->returnArg();
	$dbExport = $this->mock;

	$dbExport([], []);

	expect(\getenv('ES_CLI_SUCCESS_HAPPENED'))->toContain('Export complete!');
});
