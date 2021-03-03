<?php

namespace Tests\Unit\ModifyAdminAppearance;

use EightshiftLibs\ModifyAdminAppearance\ModifyAdminAppearanceCli;

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

$this->modifyAdminAppearance = new ModifyAdminAppearanceCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	$output = dirname(__FILE__, 3) . '/cliOutput';

	deleteCliOutput($output);
});

test('ModifyAdminAppearance CLI command will correctly copy the ModifyAdminAppearance class with defaults', function () {
	$modifyAdminAppearance = $this->modifyAdminAppearance;
	$modifyAdminAppearance([], []);

	// Check the output dir if the generated method is correctly generated.
	$generatedModifyAdminAppearance = file_get_contents(dirname(__FILE__, 3) . '/cliOutput/src/ModifyAdminAppearance/ModifyAdminAppearance.php');

	$this->assertStringContainsString('class ModifyAdminAppearance implements ServiceInterface', $generatedModifyAdminAppearance);
	$this->assertStringContainsString('@package EightshiftBoilerplate\ModifyAdminAppearance', $generatedModifyAdminAppearance);
	$this->assertStringContainsString('namespace EightshiftLibs\ModifyAdminAppearance', $generatedModifyAdminAppearance);
});

test('ModifyAdminAppearance CLI command will correctly copy the ModifyAdminAppearance class with set arguments', function () {
	$modifyAdminAppearance = $this->modifyAdminAppearance;
	$modifyAdminAppearance([], [
		'namespace' => 'CoolTheme',
	]);

	// Check the output dir if the generated method is correctly generated.
	$generatedModifyAdminAppearance = file_get_contents(dirname(__FILE__, 3) . '/cliOutput/src/ModifyAdminAppearance/ModifyAdminAppearance.php');

	$this->assertStringContainsString('class ModifyAdminAppearance implements ServiceInterface', $generatedModifyAdminAppearance);
	$this->assertStringContainsString('namespace CoolTheme\ModifyAdminAppearance;', $generatedModifyAdminAppearance);
});


test('ModifyAdminAppearance CLI documentation is correct', function () {
	$modifyAdminAppearance = $this->modifyAdminAppearance;

	$documentation = $modifyAdminAppearance->getDoc();

	$key = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($key, $documentation);
	$this->assertArrayNotHasKey('synopsis', $documentation);
	$this->assertEquals('Generates Modify Admin Appearance class.', $documentation[$key]);
});
