<?php

namespace Tests\Unit\Init;

use EightshiftLibs\Helpers\Helpers;
use EightshiftLibs\Init\InitProjectCli;

use function Tests\getMockArgs;

beforeEach(function () {
	$this->mock = new InitProjectCli('boilerplate');
});

afterEach(function () {
	unset($this->mock);
});

test('Initializing the project command returns correct command name', function () {
	$commandName = $this->mock->getCommandName();

	$this->assertIsString($commandName);
	$this->assertSame('project', $commandName);
});

test('InitProjectCli CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});

test('InitProject CLI command will correctly copy the project classes', function () {
	$mock = $this->mock;
	$mock([], getMockArgs([
		'path' => Helpers::getProjectPaths('cliOutput'),
	]));

	expect(\getenv('ES_CLI_LOG_HAPPENED'))
	->toContain('Happy developing!');
});
