<?php

namespace Tests\Unit\Readme;

use EightshiftLibs\Readme\ReadmeCli;

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

$this->readme = new ReadmeCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	deleteCliOutput();
});

test('Readme CLI command will correctly copy the readme file with defaults', function () {
	$readme = $this->readme;
	$readme([], $readme->getDevelopArgs([]));

	$outputPath = \dirname(__FILE__, 3) . '/cliOutput/README.md';

	// Check the output dir if the generated method is correctly generated.
	$generatedReadme = \file_get_contents($outputPath);

	$this->assertStringContainsString('This is the official repository of the {Project name}.', $generatedReadme);
	$this->assertStringNotContainsString('footer.php', $generatedReadme);
	$this->assertFileExists($outputPath);
});

test('Readme CLI command will run under custom command name', function () {
	$readme = $this->readme;
	$result = $readme->getCommandName();

	$this->assertStringContainsString('readme', $result);
});

test('Readme CLI command will correctly copy the readme in the custom folder with set arguments', function () {
	$readme = $this->readme;
	$readme([], [
		'root' => './test',
	]);

	$outputPath = \dirname(__FILE__, 3) . '/cliOutput/test/README.md';

	$this->assertFileExists($outputPath);
});

test('Readme CLI documentation is correct', function () {
	expect($this->readme->getDoc())->toBeArray();
});
