<?php

namespace Tests\Unit\ModifyAdminAppearance;

use EightshiftLibs\ModifyAdminAppearance\ModifyAdminAppearanceCli;

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

$this->modifyAdminAppearance = new ModifyAdminAppearanceCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	deleteCliOutput();
});

test('ModifyAdminAppearance CLI command will correctly copy the ModifyAdminAppearance class with defaults', function () {
	$modifyAdminAppearance = $this->modifyAdminAppearance;
	$modifyAdminAppearance([], []);

	$outputPath = \dirname(__FILE__, 4) . '/cliOutput/src/ModifyAdminAppearance/ModifyAdminAppearance.php';

	// Check the output dir if the generated method is correctly generated.
	$generatedModifyAdminAppearance = \file_get_contents($outputPath);

	$this->assertStringContainsString('class ModifyAdminAppearance implements ServiceInterface', $generatedModifyAdminAppearance);
	$this->assertStringContainsString('@package EightshiftLibs\ModifyAdminAppearance', $generatedModifyAdminAppearance);
	$this->assertStringContainsString('namespace EightshiftLibs\ModifyAdminAppearance', $generatedModifyAdminAppearance);
	$this->assertStringNotContainsString('footer.php', $generatedModifyAdminAppearance);
	$this->assertFileExists($outputPath);
});

test('ModifyAdminAppearance CLI command will correctly copy the ModifyAdminAppearance class with set arguments', function () {
	$modifyAdminAppearance = $this->modifyAdminAppearance;
	$modifyAdminAppearance([], [
		'namespace' => 'CoolTheme',
	]);

	// Check the output dir if the generated method is correctly generated.
	$generatedModifyAdminAppearance = \file_get_contents(\dirname(__FILE__, 4) . '/cliOutput/src/ModifyAdminAppearance/ModifyAdminAppearance.php');

	$this->assertStringContainsString('namespace CoolTheme\ModifyAdminAppearance;', $generatedModifyAdminAppearance);
});


test('ModifyAdminAppearance CLI documentation is correct', function () {
	$modifyAdminAppearance = $this->modifyAdminAppearance;

	$documentation = $modifyAdminAppearance->getDoc();

	$key = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($key, $documentation);
	$this->assertArrayNotHasKey('synopsis', $documentation);
	$this->assertSame('Generates Modify Admin Appearance class.', $documentation[$key]);
});
