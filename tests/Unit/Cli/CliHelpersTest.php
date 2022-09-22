<?php

namespace Tests\Unit\Cli;

use EightshiftLibs\Cli\CliHelpers;

test('cliError wrapper will return a WPCLI error', function () {
	CliHelpers::cliError('Some random cli error happened');
})->expectExceptionMessage('Some random cli error happened');
