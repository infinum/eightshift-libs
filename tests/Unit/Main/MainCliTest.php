<?php

namespace Tests\Unit\Main;

use EightshiftLibs\Main\MainCli;

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

$this->main = new MainCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	deleteCliOutput();
});

test('Main CLI command will correctly copy the Main class with defaults', function () {
	$main = $this->main;
	$main([], []);

	$outputPath = \dirname(__FILE__, 4) . '/cliOutput/src/Main/Main.php';

	// Check the output dir if the generated method is correctly generated.
	$generatedMain = \file_get_contents($outputPath);

	$this->assertStringContainsString('class Main extends AbstractMain', $generatedMain);
	$this->assertStringContainsString('@package EightshiftLibs\Main', $generatedMain);
	$this->assertStringContainsString('namespace EightshiftLibs\Main', $generatedMain);
	$this->assertStringNotContainsString('footer.php', $generatedMain);
	$this->assertFileExists($outputPath);
});

test('Main CLI command will correctly copy the Main class with set arguments', function () {
	$main = $this->main;
	$main([], [
		'namespace' => 'CoolTheme',
	]);

	// Check the output dir if the generated method is correctly generated.
	$generatedMain = \file_get_contents(\dirname(__FILE__, 4) . '/cliOutput/src/Main/Main.php');

	$this->assertStringContainsString('namespace CoolTheme\Main', $generatedMain);
	$this->assertStringNotContainsString('namespace EightshiftLibs\Main', $generatedMain);
});

test('Main CLI documentation is correct', function () {
	$main = $this->main;

	$documentation = $main->getDoc();

	$key = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($key, $documentation);
	$this->assertArrayNotHasKey('synopsis', $documentation);
	$this->assertSame('Generates Main class file for all other features using service container pattern.', $documentation[$key]);
});
