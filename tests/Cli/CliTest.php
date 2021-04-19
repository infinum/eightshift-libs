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
use EightshiftLibs\Menu\MenuCli;
use EightshiftLibs\Setup\UpdateCli;

use function Tests\deleteCliOutput;
use function Tests\mock;
use function Tests\setupMocks;

/**
 * Mock before tests.
 */
beforeEach(function () {
	Monkey\setUp();
	setupMocks();

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
	$output = dirname(__FILE__, 3) . '/cliOutput';

	deleteCliOutput($output);

	putenv('ERROR_HAPPENED');
	putenv('INIT_CALLED');

	Monkey\tearDown();
});


test('Cli getDevelopClasses return correct class list', function () {
	$developClasses = $this->cli->getDevelopClasses();

	$numberOfDevClasses = 31;

	$this->assertIsArray($developClasses);
	$this->assertTrue(count($developClasses) === $numberOfDevClasses, 'Total number of classes is correct');
	$this->assertArrayNotHasKey(BlockComponentCli::class, $developClasses, 'Public class found');
	$this->assertArrayNotHasKey(BlockWrapperCli::class, $developClasses, 'Public class found');
	$this->assertArrayNotHasKey(BlockVariationCli::class, $developClasses, 'Public class found');
	$this->assertArrayNotHasKey(BlockCli::class, $developClasses, 'Public class found');
	$this->assertArrayNotHasKey(BlocksStorybookCli::class, $developClasses, 'Public class found');
	$this->assertArrayNotHasKey(UpdateCli::class, $developClasses, 'Public class found');
	$this->assertArrayNotHasKey(ExportCli::class, $developClasses, 'Public class found');
	$this->assertArrayNotHasKey(ImportCli::class, $developClasses, 'Public class found');
});


test('Cli getPublicClasses return correct class list', function () {
	$publicClasses = $this->cli->getPublicClasses();

	$numberOfPublicClasses = 36;

	$this->assertIsArray($publicClasses);
	$this->assertTrue(count($publicClasses) === $numberOfPublicClasses, 'Total number of classes is correct');
	$this->assertArrayNotHasKey(CliReset::class, $publicClasses, 'Development class found');
	$this->assertArrayNotHasKey(CliRunAll::class, $publicClasses, 'Development class found');
	$this->assertArrayNotHasKey(CliShowAll::class, $publicClasses, 'Development class found');
});


test('Running develop commands throws error if command name is not specified', function() {
	$this->cli->loadDevelop();

	$this->assertSame('First argument must be a valid command name.', getenv('ERROR_HAPPENED'));
});

test('Running develop commands runs a particular command successfully', function() {
	$this->cli->loadDevelop(['create_menu']);

	// Check the output dir if the generated method is correctly generated.
	$generatedMenu = file_get_contents(dirname(__FILE__, 3) . '/cliOutput/src/Menu/Menu.php');
	$this->assertStringContainsString('class Menu extends AbstractMenu', $generatedMenu);
	$this->assertStringContainsString('header_main_nav', $generatedMenu);
	$this->assertStringNotContainsString('footer_main_nav', $generatedMenu);
});


test('Running load command works', function() {
	$this->cli->load('boilerplate');

	// We could add all 36 of the public CLI classes, but I don't think that makes sense ¯\_(ツ)_/¯.
	$this->assertSame(10, has_action('cli_init', 'EightshiftLibs\Menu\MenuCli->registerCommand()'));
});

