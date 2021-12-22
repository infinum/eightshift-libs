<?php

namespace Tests\Unit\CliCommands;

use EightshiftBoilerplate\CliCommands\CustomCommandExample;

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

	$this->customCommand = new CustomCommandExample();
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	$output = dirname(__FILE__, 3) . '/cliOutput';

	deleteCliOutput($output);
});


test('Register method will call init hook', function () {
	$this->customCommand->register();

	$this->assertSame(10, has_action('cli_init', 'EightshiftBoilerplate\CliCommands\CustomCommandExample->registerCommand()'));
});


test('Prepare command docs fails if shortdesc doesn\'t exist', function() {
	$customCommand = $this->customCommand;

	$customCommand->prepareCommandDocs([], []);
})->throws(\RuntimeException::class, 'CLI Short description is missing.');


test('Prepare command docs returns correct doc', function() {
	$customCommand = $this->customCommand;

	$docs = [
		'shortdesc' => 'Some description',
		'synopsis' => [
			[
				'type' => 'assoc',
				'name' => 'random',
				'description' => 'Random description.',
				'optional' => true,
			],
		],
	];

	$preparedDocs = $customCommand->prepareCommandDocs($docs, $customCommand->getGlobalSynopsis());

	$this->assertIsArray($preparedDocs);
	$this->assertArrayHasKey('shortdesc', $preparedDocs);
	$this->assertArrayHasKey('synopsis', $preparedDocs);

	$addedSynopsis = array_filter($preparedDocs['synopsis'], function($descArr) {
		return $descArr['name'] === 'random';
	});
	// Check if the synopsis was added to the global one.
	$this->assertNotEmpty($addedSynopsis);
});


test('Custom command example command name is correct', function () {
	$customCommand = $this->customCommand;

	$commandName = $customCommand->getCommandName();

	$this->assertIsString($commandName);
	$this->assertSame($this->customCommand::COMMAND_NAME, $commandName);
});


test('Custom command example documentation is correct', function () {
	$customCommand = $this->customCommand;

	$documentation = $customCommand->getDoc();

	$descKey = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($descKey, $documentation);
	$this->assertSame('Generates custom WPCLI command in your project.', $documentation[$descKey]);
});
