<?php

namespace Tests\Unit\View;

use EightshiftLibs\View\EscapedViewCli;

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

	$this->escapedView = new EscapedViewCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	deleteCliOutput();
});


test('Escaped view command will correctly copy the EscapedView class with defaults', function () {
	$escapedView = $this->escapedView;
	$escapedView([], $escapedView->getDevelopArgs([]));

	// Check the output dir if the generated method is correctly generated.
	$generatedEscapedView = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/View/EscapedView.php');

	$this->assertNotEmpty($generatedEscapedView);
	$this->assertStringContainsString('class EscapedView extends AbstractEscapedView implements ServiceInterface', $generatedEscapedView);
	$this->assertStringContainsString('register', $generatedEscapedView);
		$this->assertStringNotContainsString('someRandomMethod', $generatedEscapedView);

});


 test('Escaped view CLI documentation is correct', function () {
 	$escapedView = $this->escapedView;

 	$documentation = $escapedView->getDoc();

 	$descKey = 'shortdesc';

	$this->assertNotEmpty($documentation);
	$this->assertIsArray($documentation);
 	$this->assertArrayHasKey($descKey, $documentation);
 	$this->assertSame('Generates project Escape view class.', $documentation[$descKey]);
 });
