<?php

namespace Tests\Unit\Init;

use Brain\Monkey\Functions;
use EightshiftLibs\Init\InitThemeCli;

use function Patchwork\{redefine, always};
use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new InitThemeCli('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});

test('Initializing the project command returns correct command name', function () {
	$commandName = $this->mock->getCommandName();

	$this->assertIsString($commandName);
	$this->assertSame('theme', $commandName);
});


test('InitThemeCli CLI documentation is correct', function () {
	expect($this->mock->getDoc())->toBeArray();
});

test('InitTheme CLI command will correctly copy the project classes', function () {
	$mock = $this->mock;
	$mock([], $mock->getDefaultArgs());

	expect(\getenv('ES_CLI_LOG_HAPPENED'))
		->toContain('Happy developing!');
});
