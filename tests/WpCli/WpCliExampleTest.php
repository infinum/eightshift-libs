<?php

namespace Tests\Unit\WpCli;

use EightshiftBoilerplate\WpCli\WpCliExample;
use EightshiftLibs\Services\ServiceCliInterface;
use RuntimeException;

use function Tests\deleteCliOutput;
use function Tests\mock;

/**
 * Mock before tests.
 */
beforeEach(function () {
	$wpCliMock = mock('alias:WP_CLI');

	$wpCliMock
		->shouldReceive('success')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('error')
		->andReturnArg(0);

	$this->customCommand = new WpCliExample();
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	deleteCliOutput();
});


test('Register method will call init hook', function () {
	$this->customCommand->register();

	$this->assertSame(10, has_action('cli_init', 'EightshiftBoilerplate\WpCli\WpCliExample->registerCommand()'));
});

test('Prepare command docs returns correct doc', function() {
	$customCommand = $this->customCommand->getDocs();

	$this->assertIsArray($customCommand);
	$this->assertArrayHasKey('shortdesc', $customCommand);
});

test('Custom command class is callable', function() {
	$customCommand = $this->customCommand;

	expect($customCommand)->toBeCallable();
});


test('Custom command example documentation is correct', function () {
	$customCommand = $this->customCommand;

	$documentation = $customCommand->getDocs();

	$descKey = 'shortdesc';

	expect($documentation)
		->toBeArray($documentation)
		->toHaveKey('shortdesc');
});
