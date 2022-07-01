<?php

namespace Tests\Unit\WpCli;

use EightshiftBoilerplate\WpCli\WpCliExample;
use EightshiftLibs\Services\ServiceCliInterface;
use RuntimeException;

use function Tests\deleteCliOutput;
use function Tests\mock;
use function Tests\setAfterEach;
use function Tests\setBeforeEach;

beforeEach(function () {
	setBeforeEach();

	$this->mock = new WpCliExample('boilerplate');
});

afterEach(function () {
	setAfterEach();

	unset($this->mock);
});

test('Register method will call init hook', function () {
	$this->mock->register();

	$this->assertSame(10, has_action('cli_init', 'EightshiftBoilerplate\WpCli\WpCliExample->registerCommand()'));
});

test('Prepare command docs returns correct doc', function() {
	$customCommand = $this->mock->getDocs();

	$this->assertIsArray($customCommand);
	$this->assertArrayHasKey('shortdesc', $customCommand);
});

test('Custom command class is callable', function() {
	$customCommand = $this->mock;

	expect($customCommand)->toBeCallable();
});


test('Custom command example documentation is correct', function () {
	expect($this->mock->getDocs())->toBeArray();
});
