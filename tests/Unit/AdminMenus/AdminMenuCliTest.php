<?php

namespace Tests\Unit\CustomPostType;

use EightshiftLibs\AdminMenus\AdminMenuCli;

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

	$this->cpt = new AdminMenuCli('boilerplate');
});

/**
 * Cleanup after tests.
 */
afterEach(function () {
	deleteCliOutput();
});


test('Admin menu CLI command will correctly copy the admin menu example class with defaults', function () {
	$cpt = $this->cpt;
	$cpt([], $cpt->getDevelopArgs([]));

	// Check the output dir if the generated method is correctly generated.
	$generatedCPT = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/AdminMenus/AdminTitleAdminMenu.php');

	$this->assertStringContainsString('class AdminTitleAdminMenu extends AbstractAdminMenu', $generatedCPT, 'Class name not correctly set');
	$this->assertStringContainsString('Admin Title', $generatedCPT, 'Menu title not correctly replaced');
	$this->assertStringNotContainsString('product', $generatedCPT);
});


test('Admin menu CLI command will correctly copy the admin menu class with set arguments', function () {
	$cpt = $this->cpt;
	$cpt([], [
		'title' => 'Reusable Blocks',
		'menu_title' => 'Reusable Blocks',
		'capability' => 'edit_reusable_blocks',
		'menu_slug' => 'reusable-blocks',
		'menu_icon' => 'dashicons-editor-table',
	]);

	// Check the output dir if the generated method is correctly generated.
	$generatedCPT = \file_get_contents(\dirname(__FILE__, 3) . '/cliOutput/src/AdminMenus/ReusableBlocksAdminMenu.php');

	$this->assertStringContainsString('class ReusableBlocksAdminMenu extends AbstractAdminMenu', $generatedCPT, 'Class name not correctly set');
	$this->assertStringContainsString('Reusable Blocks', $generatedCPT, 'Menu title not correctly replaced');
	$this->assertStringContainsString('edit_reusable_blocks', $generatedCPT, 'Capability not correctly replaced');
	$this->assertStringContainsString('100', $generatedCPT, 'Menu position not correctly replaced');
	$this->assertStringContainsString('dashicons-editor-table', $generatedCPT, 'Icon not correctly replaced');
	$this->assertStringNotContainsString('dashicons-analytics', $generatedCPT, 'String found that should not be here');
});


test('Admin menu CLI documentation is correct', function () {
	$cpt = $this->cpt;

	$documentation = $cpt->getDoc();

	$key = 'shortdesc';

	$this->assertIsArray($documentation);
	$this->assertArrayHasKey($key, $documentation);
	$this->assertArrayHasKey('synopsis', $documentation);
	$this->assertSame('Generates admin menu class file.', $documentation[$key]);
});
