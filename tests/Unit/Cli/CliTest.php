<?php

namespace Tests\Unit\Cli;

use Brain\Monkey;
use EightshiftLibs\Blocks\BlockCli;
use EightshiftLibs\Blocks\BlockComponentCli;
use EightshiftLibs\Blocks\BlocksStorybookCli;
use EightshiftLibs\Blocks\BlockVariationCli;
use EightshiftLibs\Blocks\BlockWrapperCli;
use EightshiftLibs\Cli\Cli;
use EightshiftLibs\Cli\CliReset;
use EightshiftLibs\Cli\CliRunAll;
use EightshiftLibs\Cli\CliShowAll;
use EightshiftLibs\Db\ExportCli;
use EightshiftLibs\Db\ImportCli;
use EightshiftLibs\Setup\UpdateCli;

use function Tests\deleteCliOutput;
use function Tests\mock;
use function Tests\setupUnitTestMocks;

/**
 * Mock before tests.
 */
beforeEach(function () {
	Monkey\setUp();
	setupUnitTestMocks();

	$wpCliMock = mock('alias:WP_CLI');

	$wpCliMock
		->shouldReceive('success')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('error')
		->andReturnUsing(function ($message) {
			putenv("ERROR_HAPPENED={$message}");
		});

	$wpCliMock
		->shouldReceive('log')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('runcommand')
		->andReturn(putenv("INIT_CALLED=true"));

	$this->cli = new Cli();
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	deleteCliOutput();

	putenv('ERROR_HAPPENED');
	putenv('INIT_CALLED');

	Monkey\tearDown();
});


test('Cli getDevelopClasses return correct class list', function () {
	$developClasses = $this->cli->getDevelopClasses();

	expect($developClasses)
		->toBeArray()
		->not->toHaveKey(BlockComponentCli::class)
		->not->toHaveKey(BlockWrapperCli::class)
		->not->toHaveKey(BlockVariationCli::class)
		->not->toHaveKey(BlockCli::class)
		->not->toHaveKey(BlocksStorybookCli::class)
		->not->toHaveKey(UpdateCli::class)
		->not->toHaveKey(ExportCli::class)
		->not->toHaveKey(ImportCli::class);

	expect(\count($developClasses))
		->toBeInt()
		->toBe(36); // Dev classes count.
});


test('Cli getPublicClasses return correct class list', function () {
	$publicClasses = $this->cli->getPublicClasses();

	expect($publicClasses)
		->toBeArray()
		->not->toHaveKey(CliReset::class)
		->not->toHaveKey(CliRunAll::class)
		->not->toHaveKey(CliShowAll::class);

	expect(\count($publicClasses))
		->toBeInt()
		->toBe(42); // Public classes count.
});


test('Running develop commands throws error if command name is not specified', function() {
	$this->cli->loadDevelop();

	$this->assertSame('First argument must be a valid command name.', \getenv('ERROR_HAPPENED'));
});

test('Running develop commands runs a particular command successfully', function() {
	$this->cli->loadDevelop(['create_menu']);

	// Check the output dir if the generated method is correctly generated.
	$generatedMenu = \file_get_contents(\dirname(__FILE__, 4) . '/cliOutput/src/Menu/Menu.php');
	$this->assertStringContainsString('class Menu extends AbstractMenu', $generatedMenu);
	$this->assertStringContainsString('header_main_nav', $generatedMenu);
	$this->assertStringNotContainsString('footer_main_nav', $generatedMenu);
});


test('Running load command works', function() {
	$this->cli->load('boilerplate');

	// We could add all 36 of the public CLI classes, but I don't think that makes sense ¯\_(ツ)_/¯.
	$this->assertSame(10, has_action('cli_init', 'EightshiftLibs\Menu\MenuCli->registerCommand()'));
});

