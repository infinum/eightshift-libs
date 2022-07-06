<?php

namespace Tests\Unit\Cli;

use EightshiftLibs\Cli\Cli;
use EightshiftLibs\Cli\CliReset;
use EightshiftLibs\Cli\CliRunAll;
use EightshiftLibs\Cli\CliShowAll;

use function Tests\setAfterEach;
use function Tests\setBeforeEach;

/**
 * Mock before tests.
 */
beforeEach(function () {
	setBeforeEach();

	$this->cli = new Cli();
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	setAfterEach();
});

test('Cli getCommandsClasses return correct class list', function () {
	$publicClasses = $this->cli->getCommandsClasses();

	expect($publicClasses)
		->toBeArray()
		->not->toHaveKey(CliReset::class)
		->not->toHaveKey(CliRunAll::class)
		->not->toHaveKey(CliShowAll::class);

	expect(\count($publicClasses))
		->toBeInt()
		->toBe(50); // Public classes count.
});

test('Running load command works', function() {
	$this->cli->load('boilerplate');

	// We could add all 36 of the public CLI classes, but I don't think that makes sense ¯\_(ツ)_/¯.
	$this->assertSame(10, has_action('cli_init', 'EightshiftLibs\Menu\MenuCli->registerCommand()'));
});
