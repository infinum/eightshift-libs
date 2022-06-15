<?php

namespace Tests\Unit\CustomMeta;

use EightshiftLibs\CustomMeta\AcfMetaCli;

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

	$this->customMeta = new AcfMetaCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	deleteCliOutput();
});


test('Custom acf meta CLI command will correctly copy the ACF meta class with defaults', function () {
	$meta = $this->customMeta;
	$meta([], $meta->getDefaultArgs());

	// Check the output dir if the generated method is correctly generated.
	$generatedMeta = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/CustomMeta/TitleAcfMeta.php');

	$this->assertStringContainsString('class TitleAcfMeta extends AbstractAcfMeta', $generatedMeta);
	$this->assertStringContainsString('acf_add_local_field_group', $generatedMeta);
});


test('Custom acf meta CLI documentation is correct', function () {
	expect($this->customMeta->getDoc())->toBeArray();
});
