<?php

namespace Tests\Unit\GitIgnore;

use EightshiftLibs\GitIgnore\GitIgnoreCli;

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

$this->gitignore = new GitIgnoreCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	$output = dirname(__FILE__, 3) . '/cliOutput';

	deleteCliOutput($output);
});

test('GitIgnore CLI command will correctly copy the .gitignore file with defaults', function () {
	$gitignore = $this->gitignore;
	$gitignore([], $gitignore->getDevelopArgs([]));

	$outputPath = dirname(__FILE__, 3) . '/cliOutput/.gitignore';

	// Check the output dir if the generated method is correctly generated.
	$generatedIgnore = file_get_contents($outputPath);

	$this->assertStringContainsString('wp-admin', $generatedIgnore);
	$this->assertStringNotContainsString('footer.php', $generatedIgnore);
	$this->assertFileExists($outputPath);
});

test('GitIgnore CLI command will run under custom command name', function () {
	$gitignore = $this->gitignore;
	$result = $gitignore->getCommandName();

	$this->assertStringContainsString('init_gitignore', $result);
});

test('GitIgnore CLI command will correctly copy the .gitignore file in the custom folder with set arguments', function () {
	$gitignore = $this->gitignore;
	$gitignore([], [
		'root' => './test',
	]);

	$this->assertFileExists(dirname(__FILE__, 3) . '/cliOutput/test/.gitignore');
});


test('GitIgnore CLI documentation is correct', function () {
	$gitignore = $this->gitignore;

	$documentation = $gitignore->getDoc();

	$key = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($key, $documentation);
	$this->assertArrayHasKey('synopsis', $documentation);
	$this->assertSame('Initialize Command for building your projects gitignore.', $documentation[$key]);
});
