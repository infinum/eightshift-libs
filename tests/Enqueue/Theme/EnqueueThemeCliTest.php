<?php

namespace Tests\Unit\Enqueue\Theme;

use EightshiftLibs\Enqueue\Theme\EnqueueThemeCli;

use function Tests\deleteCliOutput;

/**
 * Mock before tests.
 */
beforeEach(function () {
	$wpCliMock = \Mockery::mock('alias:WP_CLI');

	$wpCliMock
		->shouldReceive('success')
		->andReturnArg(0);

	$wpCliMock
		->shouldReceive('error')
		->andReturnArg(0);

	$this->enqueueTheme = new EnqueueThemeCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	$output = dirname(__FILE__, 4) . '/cliOutput';

	deleteCliOutput($output);
});

test('Custom enqueue theme CLI command will correctly copy the Enqueue Theme class', function () {
	$theme = $this->enqueueTheme;
	$theme([], $theme->getDevelopArgs([]));

	// Check the output dir if the generated method is correctly generated.
	$generatedTheme = file_get_contents(dirname(__FILE__, 4) . '/cliOutput/src/Enqueue/Theme/EnqueueTheme.php');

	$this->assertStringContainsString('class EnqueueTheme extends AbstractEnqueueTheme', $generatedTheme);
	$this->assertStringContainsString('wp_enqueue_scripts', $generatedTheme);
	$this->assertStringNotContainsString('admin_enqueue_scripts', $generatedTheme);
});


test('Custom Enqueue Theme CLI documentation is correct', function () {
	$theme = $this->enqueueTheme;
	$documentation = $theme->getDoc();
	$descKey = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($descKey, $documentation);
	$this->assertEquals('Generates Enqueue Theme class.', $documentation[$descKey]);
});
