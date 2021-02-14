<?php

namespace Tests\Unit\CustomMeta;

use EightshiftLibs\CustomMeta\AcfMetaCli;

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

	$this->customMeta = new AcfMetaCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	$output = dirname(__FILE__, 3) . '/cliOutput';

	deleteCliOutput($output);
});


test('Custom acf meta CLI command will correctly copy the ACF meta class with defaults', function () {
	$meta = $this->customMeta;
	$meta([], $meta->getDevelopArgs([]));

	// Check the output dir if the generated method is correctly generated.
	$generatedMeta = file_get_contents(dirname(__FILE__, 3) . '/cliOutput/src/CustomMeta/TitleAcfMeta.php');

	$this->assertStringContainsString('class TitleAcfMeta extends AbstractAcfMeta', $generatedMeta);
	$this->assertStringContainsString('acf_add_local_field_group', $generatedMeta);
});


test('Custom acf meta CLI documentation is correct', function () {
	$meta = $this->customMeta;

	$documentation = $meta->getDoc();

	$descKey = 'shortdesc';
	$synopsisKey = 'synopsis';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($descKey, $documentation);
	$this->assertArrayHasKey($synopsisKey, $documentation);
	$this->assertIsArray($documentation[$synopsisKey]);
	$this->assertEquals('Generates custom ACF meta fields class file.', $documentation[$descKey]);
	$this->assertEquals('assoc', $documentation[$synopsisKey][0]['type']);
	$this->assertEquals('name', $documentation[$synopsisKey][0]['name']);
	$this->assertEquals('The name of the custom meta slug. Example: title.', $documentation[$synopsisKey][0]['description']);
	$this->assertEquals(false, $documentation[$synopsisKey][0]['optional']);
});
