<?php

namespace Tests\Unit\Cli;

use EightshiftLibs\Cli\CliHelpers;

test('cliError wrapper will return a WPCLI error', function () {
	$class = new class {
		use CliHelpers;
	};

	$class::cliError('Some random cli error happened');
})->expectExceptionMessage('Some random cli error happened');
